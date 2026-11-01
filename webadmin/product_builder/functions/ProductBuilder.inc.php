<?php
/**
 *    Section Name : Product Builder
 *    Module          : ProductBuilder
 *
 *    Class            : ProductBuilder
 *
 *    This class is to add and get all Product Builders data to and from database.
 *
 *    @method Mixed(Boolean | String) addBuilder(ArrayObject $data)
 *    @method Mixed(Boolean | String) addOption(ArrayObject $data)
 *    @method Void addHistory(ArrayObject $data, String $id, String $section)
 *    @method String getHistory(String $id, String $section, Integer $limit = null)
 *    @method Mixed(Boolean | ArrayObject) getBuilder(Integer $limit = null, Integer $offset = null, Array $criteria = null, String $order = null)
 *    @method Mixed(Boolean | ArrayObject) getOptions(Integer $limit = null, Integer $offset = null, Array $criteria = null, String $order = null)
 *    @method ArrayObject searchBuildersByName(String $name)
 *    @method ArrayObject searchOptionsByName(String $name)
 *    @method ArrayObject stdBuilderFromPost(ArrayObject $post)
 *    @method ArrayObject stdOptionFromPost(ArrayObject $post)
 *    @method ArrayObject getBuilderById(String $id)
 *    @method Mixed(String | NULL) getBuilderIdByWebsite(String $website)
 *    @method ArrayObject getOptionById(String $id)
 *    @method Boolean updateBuilder(ArrayObject $data, String $id)
 *    @method Boolean updateOption(ArrayObject $data, String $id)
 *    @method String getCategoryNameById(Int $id)
 *    @method ArrayObject getAllCategoryNames()
 *    @method Boolean isBuilderExist(String $id)
 *    @method Boolean isOptionExist(String $id)
 *    @method Void deleteOption(String $id)
 *    @method Void deleteBuilder(String $id)
 *    @method String getErrorMessage()
 *    @method ArrayObject sortOptionsByPostion(ArrayObject $options)
 *
 *    // Currently Working On
 *    @method Void builderSetLock(int $id, int $staff_id, int $timeout)
 *    @method Void optionSetLock(int $id, int $staff_id, int $timeout)
 *    @method Mixed(boolean | ArrayObject) builderGetLock(int $id)
 *    @method Mixed(boolean | ArrayObject) optionGetLock(int $id)
 *    @method ArrayObject builderGetLocks()
 *    @method ArrayObject optionGetLocks()
 *
 *    @author Kelvin (Zaw Moe Htat) (khtat@highwaygroup.net)
 *    @version 09 JAN 2018
 */

class ProductBuilder
{
    /**
     *    Member Variables
     */

    /**
     *    @var string $db The database object to access MySQL Database
     */
    private $db;

    /**
     *    @var string $errMsg Store the error message when error occurred
     */
    private $errMsg;

    /**
     *    Constructor
     *
     *    This is a constructor of ProductBuilder that is called to initalised the $db.
     *
     *    @param String $db Database object. This value should not be null.
     */
    public function __construct($_db)
    {
        // Check if parameter is null value.
        if (null == $_db) {
            die("ProductBuilder Error : Database object cannot be null.");
        }

        $this->db     = $_db;
        $this->errMsg = "";
    }

    /**
     *    Member Functions
     */

    /**
     *    Method: addBuilder
     *
     *    This method is to add builder data to database.
     *
     *    The builder data must have the following attributes:
     *    - name     : Name of the builder
     *    - staff    : The id of staff who's making the change
     *    - status   : The status of this builder. (0 - inactive, 1 - active)
     *    - websites : The array containing all websites associating with this builder
     *    - machines : The array containing all machines to be built including
     *    -             Each machine has options (array)
     *
     *    This method accept the data as an array with the above attributes as keys
     *
     *    @param ArrayObject $data The array of builder data. The array should not be empty.
     *
     *    @return Boolean|String Return ID if added successfully, false otherwise.
     */
    public function addBuilder($data)
    {
        // Initialise error message
        $this->errMsg = "";
        $errHeader    = "<b>ProductBuilder Error in addBuilder Method:</b>";
        $errFooter    = "<i>For more information, read the documentation.</i>";

        // Builder data attributes
        $dataAttributes = array("name", "staff", "status", "websites", "machines", "category");

        // Check if $data is an array
        if (!is_array($data)) {
            $this->errMsg = "{$errHeader} Parameter argument must be array. {$errFooter}";
            return false;
        }

        // Check if all data required exists
        if (!$this->checkKeys($dataAttributes, $data)) {
            $this->errMsg = "{$errHeader} Data does not have all required attributes. {$errFooter}";
            return false;
        }

        // Check if websites is array object
        if (!is_array($data['websites'])) {
            $this->errMsg = "{$errHeader} Data attribute 'websites' should be array object. {$errFooter}";
        }

        // Check if options is array object
        if (!is_array($data['machines'])) {
            $this->errMsg = "{$errHeader} Data attribute 'machines' should be array object. {$errFooter}";
        }

        // Generate uniqid
        $id = $this->generateUniqueId();

        // Current time
        $curTime = formatDateTime();

        //
        // Adding to database
        //

        $this->db->query(sprintf(
            "INSERT INTO `pb_builders` (`id`, `name`, `added_date`, `last_updated`, `staff`, `status`) VALUES (%s, %s, %s, %s, %d, %d)",
            formatSQLString($id),
            formatSQLString($data['name']),
            formatSQLString($curTime),
            formatSQLString($curTime),
            intval($data['staff']),
            intval($data['status'])
        ));

        if (!empty($data['websites'])) {
            foreach ($data['websites'] as $website) {
                $this->db->query(sprintf(
                    "INSERT INTO `pb_websites` (`builder_id`, `link`) VALUES (%s, %s)",
                    formatSQLString($id),
                    formatSQLString(strtolower($website))
                ));
            }
        }

        if (!empty($data['category'])) {
            foreach ($data['category'] as $cat) {
                if ($cat['value']) {
                    $this->db->query(sprintf(
                        "INSERT INTO `pb_builder_category` (`cat`, `builder_id`, `position`) VALUES (%d, %s, %d)",
                        $cat['value'],
                        formatSQLString($id),
                        $cat['position'],
                    ));
                }
            }
        }

        if (!empty($data['machines'])) {
            foreach ($data['machines'] as $machine) {
                $machine_id = $this->generateUniqueId();

                $options = $machine['options'];
                unset($machine['options']);

                $this->db->query(sprintf(
                    "INSERT INTO `pb_machines` (`id`, `name`, `product_id`, `site_id`, `sub_id`,`price`, `currency`, `description`, `special_notes`, `delivery_pickup`, `delivery_sea`, `delivery_air`, `delivery_pickup_price`, `delivery_sea_price`, `delivery_air_price`, `builder_id`, `position`, `cat`) VALUES (%s,%s,%d,%d,%d,%f,%s,%s,%s,%d,%d,%d,%f,%f,%f,%s,%d,%d)",
                    formatSQLString($machine_id),
                    formatSQLString($machine['name']),
                    $machine['product_id'],
                    $machine['site_id'],
                    $machine['sub_id'],
                    $machine['price'],
                    formatSQLString($machine['currency']),
                    formatSQLString($machine['description']),
                    formatSQLString($machine['special_notes']),
                    $machine['delivery_pickup'],
                    $machine['delivery_sea'],
                    $machine['delivery_air'],
                    $machine['delivery_pickup_price'],
                    $machine['delivery_sea_price'],
                    $machine['delivery_air_price'],
                    formatSQLString($id),
                    $machine['position'],
                    $machine['category']
                ));

                if (!empty($options)) {
                    foreach ($options as $option) {
                        $this->db->query(sprintf(
                            "INSERT INTO `pb_machines_options` (`machine_id`, `option_id`, `position`, `qty`) VALUES (%s,%s,%d,%d)",
                            formatSQLString($machine_id),
                            formatSQLString($option['id']),
                            $option['position'],
                            $option['qty']
                        ));
                    }
                }
            }
        }

        return $id;
    }

    /**
     *    Method: addOption
     *
     *    This method is to add options to database.
     *
     *    The option data must have the following attributes:
     *    - name     : Name of this option
     *    - template : Template to be used
     *    - options  : Link options of this option.
     *
     *    Options attribute is an array with the array key (0: Link Product, 1: Link Text, 2: Link Photo).
     *
     *    Attributes for key 0 - product_id: Id of products
     *                         - price: Price to be displayed
     *
     *    Attributes for key 1 - text: Text value to be displayed
     *                         - price: Price to be displayed
     *
     *    Attributes for key 2 - img: Image file name
     *                         - ext: Extension of this image file
     *                         - price: Price to be displayed
     *
     *    @param ArrayObject $data The array of option data. The array should not be empty.
     *
     *    @return Boolean|String Return ID if added successfully, false otherwise.
     */
    public function addOption($data)
    {
        // Initialise error message
        $this->errMsg = "";
        $errHeader    = "<b>ProductBuilder Error in addOption Method:</b>";
        $errFooter    = "<i>For more information, read the documentation.</i>";

        // Required attributes
        $dataAttributes = array("name", "title", "template", "options","option_image","mouse_over_text","type");

        if (!is_array($data)) {
            $this->errMsg = "{$errHeader} Parameter argument must be array. {$errFooter}";
            return false;
        }
        if (!$this->checkKeys($dataAttributes, $data)) {
            $this->errMsg = "{$errHeader} Data does not have all required attributes. {$errFooter}";
            return false;
        }
        if (!is_array($data['options'])) {
            $this->errMsg = "{$errHeader} Options attribute must be array. {$errFooter}";
            return false;
        }

        if (!$this->checkKeys(array(0, 1, 2), $data['options'])) {
            $this->errMsg = "{$errHeader} Options attribute is incorrect. {$errFooter}";
            return false;
        }
        // Generate unique id
        $id = $this->generateUniqueId();

        // Current time
        $curTime = formatDateTime();

        //
        // Add data to database
        //
        $this->db->query(sprintf(
            "INSERT INTO `pb_options` (`id`, `name`, `title`, `added_date`, `template`,`option_image`,`mouse_over_text`,`type`) VALUES (%s, %s, %s, %s, %s,%s,%s,%s)",
            formatSQLString($id),
            formatSQLString($data['name']),
            formatSQLString($data['title']),
            formatSQLString($curTime),
            formatSQLString($data['template']),
            formatSQLString($data['option_image']),
            formatSQLString($data['mouse_over_text']),
            formatSQLString($data['type'])
        ));

        if (!empty($data['options'][0])) {
            foreach ($data['options'][0] as $op) {
                $this->db->query(sprintf(
                    "INSERT INTO `pb_options_product` (`options_id`, `product_id`, `price`, `site_id`, `currency`, `position`, `product_name`, `short_desc`, `color`,`default`,`default_color`,`rules`,`qty`,`youtube`) VALUES (%s, %d, %f, %d, %s, %d, %s, %s,%s,%s,%d,%s,%d,%s)",
                    formatSQLString($id),
                    $op['product_id'],
                    $op['price'],
                    $op['site_id'],
                    formatSQLString(strtoupper($op['currency'])),
                    $op['position'],
                    formatSQLString($op['product_name']),
                    formatSQLString($op['short_desc']),
                    formatSQLString($op['color']),
                    formatSQLString($op['default']),
                    $op['default_color'],
                    formatSQLString($op['rules']),
                    $op['qty'],
                    formatSQLString($op['youtube']),
                ));
                $last_inset_id=$this->db->insert_id();
                foreach ($op['rules'] as $key => $rule) {
                      
                        $this->db->query(sprintf(
                            "INSERT INTO `pb_options_rules` (`option_id`, `section_id`, `type`, `display_rule`, `non_display_rule`) VALUES (%s, %d, %d, %s, %s)",
                            formatSQLString($id),
                            $last_inset_id,
                            0,
                            formatSQLString($rule['rule']),
                            formatSQLString($rule['rulenotshow']),
                        ));
                }
            }
        }

        if (!empty($data['options'][1])) {
            foreach ($data['options'][1] as $op) {
                $this->db->query(sprintf(
                    "INSERT INTO `pb_options_text` (`options_id`, `text`, `header`, `price`, `currency`, `position`,`default`,`rules`,qty) VALUES (%s, %s, %s, %f, %s, %d,%s,%s,%d)",
                    formatSQLString($id),
                    formatSQLString($op['text']),
                    formatSQLString($op['header']),
                    $op['price'],
                    formatSQLString(strtoupper($op['currency'])),
                    $op['position'],
                    formatSQLString($op['default']),
                    formatSQLString($op['rules']),
                    $op['qty'],
                ));
                $last_inset_id=$this->db->insert_id();
                foreach ($op['rules'] as $key => $rule) {
                      
                        $this->db->query(sprintf(
                            "INSERT INTO `pb_options_rules` (`option_id`, `section_id`, `type`, `display_rule`, `non_display_rule`) VALUES (%s, %d, %d, %s, %s)",
                            formatSQLString($id),
                            $last_inset_id,
                            1,
                            formatSQLString($rule['rule']),
                            formatSQLString($rule['rulenotshow']),
                        ));
                }
            }
        }

        if (!empty($data['options'][2])) {
            foreach ($data['options'][2] as $op) {
                $this->db->query(sprintf(
                    "INSERT INTO `pb_options_photo` (`options_id`, `img`, `ext`, `header`, `price`, `currency`, `position`, `description`,`default`,`rules`,`qty`) VALUES (%s, %s, %s, %s, %f, %s, %d, %s,%s,%s,%d)",
                    formatSQLString($id),
                    formatSQLString($op['img']),
                    formatSQLString($op['ext']),
                    formatSQLString($op['header']),
                    $op['price'],
                    formatSQLString(strtoupper($op['currency'])),
                    $op['position'],
                    formatSQLString($op['photo_description']),
                    formatSQLString($op['default']),
                    formatSQLString($op['rules']),
                    $op['qty'],
                ));
                $last_inset_id=$this->db->insert_id();
                foreach ($op['rules'] as $key => $rule) {
                      
                        $this->db->query(sprintf(
                            "INSERT INTO `pb_options_rules` (`option_id`, `section_id`, `type`, `display_rule`, `non_display_rule`) VALUES (%s, %d, %d, %s, %s)",
                            formatSQLString($id),
                            $last_inset_id,
                            2,
                            formatSQLString($rule['rule']),
                            formatSQLString($rule['rulenotshow']),
                        ));
                }
            }
        }

        // Video feature option
        if (!empty($data['options'][3])) {
            foreach ($data['options'][3] as $op) {
                $this->db->query(sprintf(
                    "INSERT INTO `pb_options_video` (`options_id`, `youtube`, `header`, `description`, `infolink` ,`price`, `currency`, `position`,`default`,`rules`,`qty`) VALUES (%s, %s, %s, %s, %s, %f, %s, %d,%s,%s,%d)",
                    formatSQLString($id),
                    formatSQLString($op['youtube']),
                    formatSQLString($op['header']),
                    formatSQLString($op['video_description']),
                    formatSQLString($op['infolink']),
                    $op['price'],
                    formatSQLString($op['currency']),
                    $op['position'],
                    formatSQLString($op['default']),
                    formatSQLString($op['rules']),
                    $op['qty'],
                ));
                $last_inset_id=$this->db->insert_id();
                foreach ($op['rules'] as $key => $rule) {
                      
                        $this->db->query(sprintf(
                            "INSERT INTO `pb_options_rules` (`option_id`, `section_id`, `type`, `display_rule`, `non_display_rule`) VALUES (%s, %d, %d, %s, %s)",
                            formatSQLString($id),
                            $last_inset_id,
                            3,
                            formatSQLString($rule['rule']),
                            formatSQLString($rule['rulenotshow']),
                        ));
                }
            }
        }

        return $id;
    }

    /**
     *    Method: addHistory
     *
     *    This method is to record all activities to database
     *
     *    @param Array  $data    The data to be added to history
     *    @param String $id        ID of section (Options , builders)
     *    @param String $section Options or builders
     */
    public function addHistory($data, $id, $section, $staff)
    {
        $imageurl=(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") .$_SERVER['HTTP_HOST'].'/images/builder/';
        if ("builders" == $section) {
            // Websites
            $websites_array = array();
            foreach ($data['websites'] as $website) {
                $websites_array[] = $website['link'];
            }
            $websites = implode(", ", $websites_array);

            $category_array = array();
            foreach ($data['category'] as $cat) {
                $category_array[] = $this->getCategoryNameById($cat);
            }
            $categories = implode(", ", $category_array);

            $html = "<table class='clr history-content-table'>";
            $html .= "<tr><td>Builder Name</td><td>:</td><td>{$data['name']}</td></tr>";
            $html .= "<tr><td>Websites</td><td>:</td><td>{$websites}</td></tr>";
            $html .= "<tr><td>Category</td><td>:</td><td>{$categories}</td></tr>";
            $html .= "</table>";

            $html .= "<table class='clr history-content-table'>";
            $html .= "<tr><td><h4 class='clr history-content-label-title'>Machines</h4></td></tr>";

            // Machines
            $html .= "<tr><td>";

            foreach ($data['machines'] as $machine) {
                $delivery_method_array = array();
                $delivery_method       = "";
                $options_array         = array();
                $options               = "";

                $category = $this->getCategoryNameById($machine['category']);

                $available_delivery_methods = array(
                    "delivery_pickup" => "Pickup",
                    "delivery_sea"    => "Sea Freight",
                    "delivery_air"    => "Air Freight",
                );

                foreach ($available_delivery_methods as $deliveryKey => $delivery) {
                    if (1 == $machine[$deliveryKey]) {
                        $delivery_method_array[] = $delivery . " ($" . $machine[$deliveryKey . "_price"] . ")";
                    }
                }

                $delivery_method = implode(", ", $delivery_method_array);

                foreach ($machine['options'] as $option) {
                    $options_array[] = $option['name'] . " (" . $option['qty'] . ")";
                }

                $options = implode(", ", $options_array);

                $html .= "<table class='history-content'>";
                $html .= "<tr><td>Name</td><td>:</td><td>{$machine['name']}</td></tr>";
                $html .= "<tr><td>Category</td><td>:</td><td>{$category}</td></tr>";
                $html .= "<tr><td>Position</td><td>:</td><td>{$machine['position']}</td></tr>";
                $html .= "<tr><td>Price</td><td>:</td><td>\${$machine['price']} {$machine['currency']}</td></tr>";
                $html .= "<tr><td>Delivery Method</td><td>:</td><td>{$delivery_method}</td></tr>";
                $html .= "<tr><td>Description</td><td>:</td><td>{$machine['description']}</td></tr>";
                $html .= "<tr><td>Options</td><td>:</td><td>{$options}</td></tr>";
                $html .= "</table>";
            }

            $html .= "</td></tr>";

            $html .= "</table>";
        } else {
            $html = "<table class='clr history-content-table'>";
            $html .= "<tr><td>Option Name</td><td>:</td><td>{$data['name']}</td></tr>";
            $html .= "<tr><td>Title</td><td>:</td><td>{$data['title']}</td></tr>";
            $html .= "<tr><td>Template</td><td>:</td><td>{$data['template']}</td></tr>";
            $html .= "</table>";

            // Options
            $html .= "<table class='clr history-content-table'>";
            $html .= "<tr><td><h4 class='clr history-content-label-title'>Options</h4></td></tr>";
            $html .= "<tr><td>";

            $sortedOptions = $this->sortOptionsByPosition($data['options']);
            foreach ($sortedOptions as $option) {
                $option_key = $option['option_key'];

                $html .= "<table class='history-content'>";

                switch ($option_key) {
                    case 0:
                        $html .= "<tr><td>Link Type</td><td>:</td><td>Product</td></tr>";
                        $html .= "<tr><td>Product ID</td><td>:</td><td>{$option['product_id']}</td></tr>";
                        $html .= "<tr><td>Price</td><td>:</td><td>\${$option['price']} {$option['currency']}</td></tr>";
                        break;

                    case 1:
                        $html .= "<tr><td>Link Type</td><td>:</td><td>Text</td></tr>";
                        $html .= "<tr><td>Title</td><td>:</td><td>{$option['header']}</td></tr>";
                        $html .= "<tr><td>Text</td><td>:</td><td>{$option['text']}</td></tr>";
                        $html .= "<tr><td>Price</td><td>:</td><td>\${$option['price']} {$option['currency']}</td></tr>";
                        break;

                    case 2:
                        $html .= "<tr><td>Link Type</td><td>:</td><td>Photo</td></tr>";
                        $html .= "<tr><td>Title</td><td>:</td><td>{$option['header']}</td></tr>";
                        $html .= "<tr><td>Image</td><td>:</td><td><img src='{$imageurl}{$option['img']}.{$option['ext']}' width='80' height='80' alt='{$option['header']}' title='{$option['header']}'/></td></tr>";
                        $html .= "<tr><td>Description</td><td>:</td><td>{$option['description']}</td></tr>";
                        $html .= "<tr><td>Price</td><td>:</td><td>\${$option['price']} {$option['currency']}</td></tr>";
                        break;

                    case 3:
                        $html .= "<tr><td>Link Type</td><td>:</td><td>Video</td></tr>";
                        $html .= "<tr><td>Title</td><td>:</td><td>{$option['header']}</td></tr>";
                        $html .= "<tr><td>Description</td><td>:</td><td>{$option['description']}</td></tr>";
                        $html .= "<tr><td>Youtube ID</td><td>:</td><td>{$option['youtube']}</td></tr>";
                        $html .= "<tr><td>More Info Link</td><td>:</td><td>{$option['infolink']}</td></tr>";
                        $html .= "<tr><td>Price</td><td>:</td><td>\${$option['price']} {$option['currency']}</td></tr>";
                        break;
                }

                $html .= "</table>";
            }

            $html .= "</td></tr>";
            $html .= "</table>";
        }

        $this->db->query(sprintf(
            "INSERT INTO `pb_history` (`section`, `section_id`, `staff`, `history`, `added_date`) VALUES (%s, %s, %d, %s, NOW())",
            formatSQLString($section),
            formatSQLString($id),
            $staff,
            formatSQLString($html)
        ));
    }

    /**
     *    Method: getHistory
     *
     *    This method is to get history of options and builders from database
     *
     *    @param String $id      Id of options or builders
     *    @param String $section Options or buidlers
     *    @param Int    $limit   Limit of the results
     *
     *    @return ArrayObject The result of query
     */
    public function getHistory($id, $section, $limit = null)
    {
        $history = array();

        $statement = sprintf("SELECT * FROM `pb_history` WHERE `section`=%s AND `section_id`=%s ORDER BY `added_date` DESC", formatSQLString($section), formatSQLString($id));

        if (null != $limit) {
            $statement .= " LIMIT {$limit}";
        }

        $historyQuery = $this->db->query($statement);
        while ($row = $this->db->fetch_assoc($historyQuery)) {
            $history[] = $row;
        }

        return $history;
    }

    /**
     *    Method: getBuilders
     *
     *    This method is to get all builders data from database. This method return the
     *     result from database.
     *
     *    @param Integer|NULL $limit    The maximum number of data to be retrieve. Null if not used.
     *     @param Integer|NULL $offset   The offset of database where data start to be retrieved. Null if not used.
     *    @param Array|NULL     $criteria The data to be ordered by. Null if not used.
     *    @param String|NULL     $order    The order of result, ASC or DESC. Null if not used.
     *
     *    @return Boolean|ArrayObject Return false if error occurred, otherwise return the data retrieved from database.
     */
    public function getBuilders($limit = null, $offset = null, $criteria = null, $order = null)
    {
        // Initialisation
        $criteria = (null == $criteria) ? array() : $criteria;

        // Error message
        $this->errMsg = "";
        $errHeader    = "<b>ProductBuilder Error in getBuilder Method:</b>";
        $errFooter    = "<i>For more information, read the documentation.</i>";

        // Check $limit and $offset
        if (null != $offset && null == $limit) {
            $this->errMsg = "{$errHeader} Limit cannot be null when offset is set. {$errFooter}";
            return false;
        } else if (null != $limit && !is_numeric($limit)) {
            $this->errMsg = "{$errHeader} Limit should be numeric. {$errFooter}";
            return false;
        } else if (null != $offset && !is_numeric($offset)) {
            $this->errMsg = "{$errHeader} Offset should be numeric. {$errFooter}";
        }

        // Check $criteria and $order
        if (!is_array($criteria)) {
            $this->errMsg = "{$errHeader} The criteria should be array. {$errFooter}";
            return false;
        } else if (null != $order && strtoupper($order) != "ASC" && strtoupper($order) != "DESC") {
            $this->errMsg = "{$errHeader} Order must be either ASC for ascending order and DESC for decending order. {$errFooter}";
        } else if (empty($criteria) && null != $order) {
            $this->errMsg = "{$errHeader} The data cannot be sorted without criteria. {$errFooter}";
            return false;
        }

        // SQL Database Communication
        $builderStatement = "SELECT * FROM `pb_builders`";

        if (!empty($criteria)) {
            $order = (null == $order) ? "" : strtoupper($order);
            $builderStatement .= " ORDER BY `" . implode('` AND `', $criteria) . "` {$order}";
        }

        if (null != $offset) {
            $builderStatement .= " LIMIT {$offset}, {$limit}";
        } else if (null != $limit) {
            $builderStatement .= " LIMIT {$limit}";
        }

        //
        //    Select all data from database
        //

        // Builders data
        $builders = array();

        $buildersQuery = $this->db->query($builderStatement);
        while ($row = $this->db->fetch_assoc($buildersQuery)) {
            $builders[] = $row;
        }

        foreach ($builders as &$builder) {
            $builder['websites'] = array();

            $websitesQuery = $this->db->query(sprintf("SELECT * FROM `pb_websites` WHERE `builder_id`=%s", formatSQLString($builder['id'])));
            while ($row = $this->db->fetch_assoc($websitesQuery)) {
                $builder['websites'][] = $row;
            }

            $builder['machines'] = array();
            $machineQuery        = $this->db->query(sprintf("SELECT * FROM `pb_machines` WHERE `builder_id`=%s", formatSQLString($builder['id'])));
            while ($row = $this->db->fetch_assoc($machineQuery)) {
                $options      = array();
                $optionsQuery = $this->db->query(sprintf("SELECT * FROM `pb_machines_options` WHERE `machine_id`=%s ORDER BY `position` ASC", formatSQLString($row['id'])));
                while ($row2 = $this->db->fetch_assoc($optionsQuery)) {
                    $option_data = $this->getOptionById($row2['option_id']);
                    $options     = array(
                        "id"       => $row2['option_id'],
                        "name"     => $option_data['name'],
                        "qty"      => $row2['qty'],
                        "template" => $option_data['template'],
                    );
                }

                $builder['machines'][] = array(
                    "id"                    => $row['id'],
                    "name"                  => $row['name'],
                    "product_id"            => $row['product_id'],
                    "site_id"               => $row['site_id'],
                    "sub_id"                => $row['sub_id'],
                    "price"                 => $row['price'],
                    "currency"              => $row['currency'],
                    "description"           => $row['description'],
                    "delivery_pickup"       => $row['delivery_pickup'],
                    "delivery_sea"          => $row['delivery_sea'],
                    "delivery_air"          => $row['delivery_air'],
                    "delivery_pickup_price" => $row["delivery_pickup_price"],
                    "delivery_sea_price"    => $row["delivery_sea_price"],
                    "delivery_air_price"    => $row["delivery_air_price"],
                    "options"               => $options,
                    "position"              => $row["position"],
                    "category"              => $row['cat'],
                );
            }

            $builder['category'] = array();
            $categoryQuery       = $this->db->query(sprintf("SELECT * FROM `pb_builder_category` WHERE `builder_id` = %s ORDER BY `cat` ASC", formatSQLString($id)));
            while ($row = $this->db->fetch_assoc($categoryQuery)) {
                $builder['category'][] = $row['cat'];
            }

        }

        return $builders;
    }

    /**
     *    Method: getBuilderIdByWebsite
     *
     *    This method is to get the id of builder by website
     *
     *    @param String $website The website of the builder
     *
     *    @return NULL|String The id of builder if exists, null otherwise
     */
    public function getBuilderIdByWebsite($website)
    {
        $data  = array();
        $query = $this->db->query(sprintf("SELECT * FROM `pb_websites` WHERE `link`=%s", formatSQLString($website)));
        while ($row = $this->db->fetch_assoc($query)) {
            $data[] = $row;
        }

        return (count($data) > 0) ? $data[0]['builder_id'] : null;
    }

    /**
     *    Method: getOptions
     *
     *    Thid method is to get all product builder options data from database. This method
     *    return the result from database
     *
     *    @param Integer|NULL $limit   The maximum number of data to be retireved. Null if not used.
     *    @param Integer|NULL $offset  The offset of database where data start to be retrieved. Null if not used.
     *    @param String|NULL  $orderby The data to be ordered by. Null if not used.
     *    @param String|NULL     $order   The order of result, ASC or DESC. Null if not used.
     *
     *    @return Boolean|ArrayObject Return false if error occurred, otherwise return the data retrieved from database.
     */
    public function getOptions($limit = null, $offset = null, $criteria = null, $order = null,$type='option')
    {
        // Initialisation
        $criteria = (null == $criteria) ? array() : $criteria;

        // Error message
        $this->errMsg = "";
        $errHeader    = "<b>ProductBuilder Error in getOptions Method:</b>";
        $errFooter    = "<i>For more information, read the documentation.</i>";

        // Check $limit and $offset
        if (null != $offset && null == $limit) {
            $this->errMsg = "{$errHeader} Limit cannot be null when offset is set. {$errFooter}";
            return false;
        } else if (null != $limit && !is_numeric($limit)) {
            $this->errMsg = "{$errHeader} Limit should be numeric. {$errFooter}";
            return false;
        } else if (null != $offset && !is_numeric($offset)) {
            $this->errMsg = "{$errHeader} Offset should be numeric. {$errFooter}";
        }

        // Check $criteria and $order
        if (!is_array($criteria)) {
            $this->errMsg = "{$errHeader} The criteria should be array. {$errFooter}";
            return false;
        } else if (null != $order && strtoupper($order) != "ASC" && strtoupper($order) != "DESC") {
            $this->errMsg = "{$errHeader} Order must be either ASC for ascending order and DESC for decending order. {$errFooter}";
        } else if (empty($criteria) && null != $order) {
            $this->errMsg = "{$errHeader} The data cannot be sorted without criteria. {$errFooter}";
            return false;
        }

        // SQL Database Communication

            $optionStatement = "SELECT * FROM `pb_options` where is_deleted=0";

        if (!empty($criteria)) {
            $order = (null == $order) ? "" : strtoupper($order);
            $optionStatement .= " ORDER BY `" . implode('` AND `', $criteria) . "` {$order}";
        }

        if (null != $offset) {
            $optionStatement .= " LIMIT {$offset}, {$limit}";
        } else if (null != $limit) {
            $optionStatement .= " LIMIT {$limit}";
        }

        //
        //    Select all data from database
        //

        // Options data
        $options = array();

        $optionsQuery = $this->db->query($optionStatement);
        while ($row = $this->db->fetch_assoc($optionsQuery)) {
            $options[] = $row;
        }
        if($type=='rule'){
            return $options;
        }
        foreach ($options as &$option) {
            $option['options'] = array(
                0 => array(), // Link Product
                1 => array(), // Link Text
                2 => array(), // Link Photo
                3 => array(), // Link Video
            );

            $linkProductQuery = $this->db->query(sprintf("SELECT * FROM `pb_options_product` WHERE `options_id`=%s", formatSQLString($option['id'])));
            while ($row = $this->db->fetch_assoc($linkProductQuery)) {
                $option['options'][0][] = $row;
            }

            $linkTextQuery = $this->db->query(sprintf("SELECT * FROM `pb_options_text` WHERE `options_id`=%s", formatSQLString($option['id'])));
            while ($row = $this->db->fetch_assoc($linkTextQuery)) {
                $option['options'][1][] = $row;
            }

            $linkPhotoQuery = $this->db->query(sprintf("SELECT * FROM `pb_options_photo` WHERE `options_id`=%s", formatSQLString($option['id'])));
            while ($row = $this->db->fetch_assoc($linkPhotoQuery)) {
                $option['options'][2][] = $row;
            }

            $linkVideoQuery = $this->db->query(sprintf("SELECT * FROM `pb_options_video` WHERE `options_id`=%s", formatSQLString($option['id'])));
            while ($row = $this->db->fetch_assoc($linkVideoQuery)) {
                $option['options'][3][] = $row;
            }

        }

        return $options;
    }

    /**
     *    Method: searchBuildersByName
     *
     *    This method is to search builders by matching the name of the builders from database.
     *
     *    @param String $name Name of the builder
     *
     *    @return ArrayObject The result of the search
     */
    public function searchBuildersByName($name)
    {
        // Search options
        $builders = array();

        // Name to be searched
        $name = "%" . strtolower($name) . "%";

        $query = $this->db->query(sprintf("SELECT * FROM `pb_builders` WHERE `name` LIKE %s", formatSQLString($name)));

        while ($row = $this->db->fetch_assoc($query)) {
            $builders[] = $row;
        }

        return $builders;
    }

    /**
     *    Method: searchOptionsByName
     *
     *    This method is to search options by matching the name of the option from database.
     *
     *    @param String $name Name of the option
     *
     *    @return ArrayObject The result of the search
     */
    public function searchOptionsByName($name)
    {
        // Search options
        $options = array();

        // Name to be searched
        $name = "%" . strtolower($name) . "%";

        $query = $this->db->query(sprintf("SELECT * FROM `pb_options` WHERE `name` LIKE %s", formatSQLString($name)));

        while ($row = $this->db->fetch_assoc($query)) {
            $options[] = $row;
        }

        return $options;
    }

    /**
     *    Method: stdBuilderFromPost
     *
     *    This method standardise builder data from post.
     *
     *    @param ArrayObject $post Post data of builder
     *
     *    @return ArrayObject Standardised builder data
     */
    public function stdBuilderFromPost($post)
    {
        $data = array();
        if (empty($post) === false) {
            $data['name']     = $post['name'];
            $data['staff']    = $post['staff'];
            $data['status']   = $post['status'];
            $data['websites'] = array();
            $data['category'] = array();
            $data['machines'] = array();

            if (isset($post['websites']) && empty($post['websites']) === false) {
                foreach ($post['websites'] as $website) {
                    $data['websites'][] = strtolower($website);
                }
            }

            if (isset($post['category']) && empty($post['category']) === false) {
                foreach ($post['category'] as $cat) {
                    $data['category'][] = $cat;
                }
            }

            if (isset($post['machines']) && empty($post['machines']) === false) {
                foreach ($post['machines'] as $machine) {
                    $data['machines'][] = array(
                        "name"                  => $machine['name'],
                        "product_id"            => $machine['product_id'],
                        "site_id"               => $machine['site_id'],
                        "sub_id"                => $machine['sub_id'],
                        "price"                 => $machine['price'],
                        "currency"              => $machine['currency'],
                        "delivery_pickup"       => (isset($machine['delivery_pickup']) && !empty($machine['delivery_pickup'])) ? 1 : 0,
                        "delivery_sea"          => (isset($machine['delivery_sea']) && !empty($machine['delivery_sea'])) ? 1 : 0,
                        "delivery_air"          => (isset($machine['delivery_air']) && !empty($machine['delivery_air'])) ? 1 : 0,
                        "delivery_pickup_price" => $machine['delivery_pickup_price'],
                        "delivery_sea_price"    => $machine['delivery_sea_price'],
                        "delivery_air_price"    => $machine['delivery_air_price'],
                        "description"           => $machine['description'],
                        "special_notes"         => $machine['special_notes'],
                        "id"                    => (isset($machine['id']) && !empty($machine['id'])) ? $machine['id'] : "",
                        "options"               => (isset($machine['options']) && !empty($machine['options'])) ? $machine['options'] : array(),
                        "position"              => $machine['position'],
                        "category"              => $machine['category'],
                    );
                }
            }
        }

        return $data;
    }

    /**
     *    Method: stdOptionFromPost
     *
     *    This method standardise option data from post.
     *
     *    @param ArrayObject $post Post data of option
     *
     *    @return ArrayObject Standardised option data
     */
    public function stdOptionFromPost($post,$files=[])
    {

        $data = array();
        if (empty($post) === false) {
            $data['name']     = $post['name'];
            $data['title']    = (isset($post['title']) && !empty($post['title'])) ? $post['title'] : $post['name'];
            $data['template'] = $post['template'];
            $data['type'] = 'option';
            $data['mouse_over_text'] = $post['mouse_over_text'];
            if(isset($files['name'])){
                $file_orig = $files['name'];
                $file_temp = $files['tmp_name'];
                $error     = $files['error'];
                $file_ext = substr($file_orig, strrpos($file_orig, '.'));
                if (substr($file_ext, 0, 1) == '.') {
                    $file_ext = substr($file_ext, 1);
                }
                $file_new  = "option_" .time(). '_' .rand(1,100000) . '.' . $file_ext;
                $dir       = $this->categoryImagePath();
                if (move_uploaded_file($file_temp, $dir . $file_new)) {
                    $data['option_image']=$file_new;
                }
            }
            $data['options']  = array();

            $data['options'][0] = array();
            $data['options'][1] = array();
            $data['options'][2] = array();
            $data['options'][3] = array();

            if (isset($post['options'][0])) {
                foreach ($post['options'][0] as $op) {
                    if (empty($op['option_val']) === false || empty($op['price']) === false) {
                        $data['options'][0][] = array(
                            "product_id" => $op['option_val'],
                            "price"      => $op['price'],
                            "site_id"    => $op['site_id'],
                            "product_name" => $op['product_name'],
                            "short_desc"    => $op['short_desc'],
                            "currency"   => $op['currency'],
                            "position"   => $op['position'],
                            'color'     => ($op['color'])?implode(',',$op['color']):'',
                            'default_color' => ($op['default_color'])?$op['default_color']:0,
                            'default'   => (isset($op['default']))?'y':'n',
                            'youtube'   => (isset($op['youtube']))?$op['youtube']:'',
                            'qty'   => (isset($op['qty']))?$op['qty']:1,
                            'rules'   => (isset($op['rules']))?$op['rules']:[],
                        );
                    }
                }
            }

            if (isset($post['options'][1])) {
                foreach ($post['options'][1] as $op) {
                    if (empty($op['option_val']) === false || empty($op['price']) === false || empty($op['header']) === false) {
                        $data['options'][1][] = array(
                            "text"     => $op['option_val'],
                            "header"   => $op['header'],
                            "price"    => $op['price'],
                            "currency" => $op['currency'],
                            "position" => $op['position'],
                            'default'   => (isset($op['default']))?'y':'n',
                            'rules'   => (isset($op['rules']))?$op['rules']:[],
                            'qty'   => (isset($op['qty']))?$op['qty']:1,
                        );
                    }
                }
            }

            if (isset($post['options'][2])) {
                foreach ($post['options'][2] as $op) {
                    if (empty($op['option_val']) === false || empty($op['price']) === false || empty($op['header']) === false || empty($op['photo_description']) === false) {
                        $imgFile = explode(".", $op['option_val']);

                        $data['options'][2][] = array(
                            "img"               => $imgFile[0],
                            "ext"               => $imgFile[1],
                            "header"            => $op['header'],
                            "price"             => $op['price'],
                            "currency"          => $op['currency'],
                            "position"          => $op['position'],
                            "photo_description" => $op['photo_description'],
                            'default'   => (isset($op['default']))?'y':'n',
                            'rules'   => (isset($op['rules']))?$op['rules']:[],
                            'qty'   => (isset($op['qty']))?$op['qty']:1,
                        );
                    }
                }
            }

            if (isset($post['options'][3])) {
                foreach ($post['options'][3] as $op) {
                    if (empty($op['option_val']) === false || empty($op['price']) === false || empty($op['header']) === false || empty($op['video_description']) === false) {
                        $data['options'][3][] = array(
                            "youtube"           => $op['option_val'],
                            "header"            => $op['header'],
                            "video_description" => $op['video_description'],
                            "infolink"          => $op['infolink'],
                            "price"             => $op['price'],
                            "currency"          => $op['currency'],
                            "position"          => $op['position'],
                            'default'           => (isset($op['default']))?'y':'n',
                            'rules'   => (isset($op['rules']))?$op['rules']:[],
                            'qty'   => (isset($op['qty']))?$op['qty']:1,
                        );
                    }
                }
            }

            return $data;
        }

        return false;
    }

    private function categoryImagePath(){
        return $_SERVER['DOCUMENT_ROOT'] . '/images/builder/';
    }

    /**
     * Delete Category by seting deleted_at value
     * @param is for delete category
     * @return delete status
     */
    public function deleteCategory($id){
        $DB = $this->db;
        $table='`pb_machines_category`';
        $primary_key='id';
        $DB->query(sprintf(
            "UPDATE %s SET `deleted_at` = NOW() WHERE `deleted_at` IS NULL AND `%s` = %d LIMIT 1",
            //"DELETE FROM %s WHERE `%s` = %d LIMIT 1",
            $table,
            $primary_key,
            $id
        ));
        return $DB->affected_rows();
    }

    /**
     *    Method: stdOptionFromPost
     *
     *    This method standardise option data from post.
     *
     *    @param ArrayObject $post Post data of option
     *
     *    @return ArrayObject Standardised option data
     */
    public function stdCategoryFromPost($posted,$files=[]){
        foreach ($posted as $key => $value) {
            if(array_key_exists($key, $files['name'])){
                $file_orig = $files['name'][$key]['image'];
                $file_temp = $files['tmp_name'][$key]['image'];
                $error     = $files['error'][$key]['image'];
                $file_ext = substr($file_orig, strrpos($file_orig, '.'));
                if (substr($file_ext, 0, 1) == '.') {
                    $file_ext = substr($file_ext, 1);
                }
                $file_new  = "category_" .time(). '_' .rand(1,100000) . '.' . $file_ext;
                $dir       = $this->categoryImagePath();
                if (move_uploaded_file($file_temp, $dir . $file_new)) {
                    $posted[$key]['filename']=$file_new;
                    $posted[$key]['file_orig']=$file_orig;
                }
            }
        }
        return $posted;
    }

    /**
     *    Method: addUpdateCategory
     *
     *    This method addUpdateCategory are use to add and update category.
     *
     *    @param ArrayObject $post Post data of category
     *
     *    @return true
     */
    public function addUpdateCategory($posted){
        $table='`pb_machines_category`';
        $primary_key='id';
        $DB = $this->db;
        foreach ($posted as $key => $item) {
            $id = intval($item['id']);
            $fields     = $DB->get_table_columns('`pb_machines_category`');
            $old_vals   = $DB->query_first(sprintf(
                "SELECT * FROM %s WHERE `%s` = %d LIMIT 1",
                $table,
                $primary_key,
                $id
            ), MYSQLI_ASSOC);
            if ($old_vals) {
                $set_vals = array();
                foreach ($fields as $k => $v) {
                    if (array_key_exists($k, $item) && $item[$k] !=$old_vals[$k]) {
                        if (is_array($item[$k])) {
                            $set_vals[$k] = "`" . $k . "` = " . $DB->escape(implode(",", $item[$k]));
                        } elseif (isset($item[$k])) {
                            $set_vals[$k] = "`" . $k . "` = " . $DB->escape($item[$k]);
                        } else {
                            $set_vals[$k] = "`" . $k . "` = NULL";
                        }
                    }
                }
                if (count($set_vals)) {
                    $DB->query(sprintf(
                        "UPDATE %s SET %s WHERE `%s` = %d LIMIT 1",
                        $table,
                        implode(', ', $set_vals),
                        $primary_key,
                        $id
                    ));
                }
            }else {
                $insert_vals = array();
                foreach ($fields as $k => $v) {
                    if (array_key_exists($k, $item)) {
                        if (is_array($item[$k])) {
                            $insert_vals[$k] = $DB->escape(implode(",", $item[$k]));
                        } elseif (isset($item[$k])) {
                            $insert_vals[$k] = $DB->escape($item[$k]);
                        } else {
                            $insert_vals[$k] = "NULL";
                        }
                    }
                }
                if (count($insert_vals)) {
                    $DB->query(sprintf(
                        "INSERT INTO %s (`%s`) VALUES (%s)",
                        $table,
                        implode('`, `', array_keys($insert_vals)),
                        implode(", ", $insert_vals)
                    ));
                }
            }
        }
        
    }
    public function getOptionRulesById($id){
        $rules['display']='';
        $rules['non_display']='';
            $rulesQuesry = $this->db->query(sprintf("SELECT `pb_options_rules`.*,`pb_options`.name FROM `pb_options_rules` JOIN `pb_options` on `pb_options`.id=`pb_options_rules`.display_rule WHERE `option_id`=%s ORDER BY `pb_options_rules`.`id` asc", formatSQLString($id)));
            while ($row = $this->db->fetch_assoc($websitesQuery)) {
                $rules['display'] = $rules['display'].'<p style=padding:2px;font-size:15px;>'.$row['name'].'<p>';
            }
             $rulesQuesry = $this->db->query(sprintf("SELECT `pb_options_rules`.*,`pb_options`.name FROM `pb_options_rules` JOIN `pb_options` on `pb_options`.id=`pb_options_rules`.non_display_rule WHERE `option_id`=%s ORDER BY `pb_options_rules`.`id` asc", formatSQLString($id)));
            while ($row = $this->db->fetch_assoc($websitesQuery)) {
                $rules['non_display'] = $rules['non_display'].'<p style=padding:2px;font-size:15px;>'.$row["name"].'<p>';
            }
        return $rules;
    }
    public function getBuilderByIdWithRules($id,$selected_data){
            // Initialisation
        $this->errMsg = "";
        $errHeader    = "<b>ProductBuilder Error in getBuilderById:</b>";
        $errFooter    = "<i>For more information, please read documentation.</i>";

        // Pulling data
        $builder = $this->db->query_first(sprintf("SELECT * FROM `pb_builders` WHERE `id`=%s", formatSQLString($id)), MYSQLI_ASSOC);

        if (!empty($builder)) {
            $builder['websites'] = array();

            $websitesQuery = $this->db->query(sprintf("SELECT * FROM `pb_websites` WHERE `builder_id`=%s", formatSQLString($id)));
            while ($row = $this->db->fetch_assoc($websitesQuery)) {
                $builder['websites'][] = $row;
            }

            $builder['machines'] = array();
            $machineQuery        = $this->db->query(sprintf("SELECT * FROM `pb_machines` WHERE `builder_id`=%s", formatSQLString($id)));
            while ($row = $this->db->fetch_assoc($machineQuery)) {
                $options      = array();
                $optionsQuery = $this->db->query(sprintf("SELECT * FROM `pb_machines_options` WHERE `machine_id`=%s ORDER BY `position` ASC", formatSQLString($row['id'])));
                $keyval=0;
                while ($optionRow = $this->db->fetch_assoc($optionsQuery)) {
                    $option_data = $this->getOptionById($optionRow['option_id']);
                    $optionList = $this->sortOptionsByPosition($option_data['options']);
                    $defaultkey = array_search('y',array_column($optionList, 'default'));
                    $rulekey=((isset($selected_data[$keyval]))?($selected_data[$keyval]-1):$defaultkey)+1;
                    if(!array_key_exists($rulesdata['option_id'], $options)){
                        $options[$optionRow['option_id']]   = array(
                            "id"       => $optionRow['option_id'],
                            "name"     => $option_data['name'],
                            "title"    => $option_data['title'],
                            "qty"      => $optionRow['qty'],
                            "template" => $option_data['template'],
                        );
                        $keyval++;
                    }
                    foreach ($optionList[$rulekey]['rules'] ?? [] as $key => $rulesdata) {
                        if($rulesdata['display_rule'] && !array_key_exists($rulesdata['display_rule'], $options)){
                            $option_rule = $this->getOptionById($rulesdata['display_rule']);
                            $optionListrule = $this->sortOptionsByPosition($option_rule['options']);
                            $defaultkey = array_search('y',array_column($optionListrule, 'default'))+1;
                            $qty=$optionListrule[$defaultkey]['qty'];
                            $options[$rulesdata['display_rule']]   = array(
                                "id"       => $rulesdata['display_rule'],
                                "name"     => $option_rule['name'],
                                "title"    => $option_rule['title'],
                                "qty"      => $qty,
                                "template" => $option_rule['template'],
                            );
                            $keyval++;
                        }
                    }
                }
                
                $builder['machines'][] = array(
                    "id"                    => $row['id'],
                    "name"                  => $row['name'],
                    "product_id"            => $row['product_id'],
                    "site_id"               => $row['site_id'],
                    "sub_id"                => $row['sub_id'],
                    "price"                 => $row['price'],
                    "currency"              => $row['currency'],
                    "description"           => $row['description'],
                    "special_notes"         => $row['special_notes'],
                    "delivery_pickup"       => $row['delivery_pickup'],
                    "delivery_sea"          => $row['delivery_sea'],
                    "delivery_air"          => $row['delivery_air'],
                    "delivery_pickup_price" => $row["delivery_pickup_price"],
                    "delivery_sea_price"    => $row["delivery_sea_price"],
                    "delivery_air_price"    => $row["delivery_air_price"],
                    "options"               => array_values($options),
                    "position"              => $row["position"],
                    "category"              => $row['cat'],
                );
            }

            $builder['category'] = array();
            $categoryQuery       = $this->db->query(sprintf(
                "SELECT * FROM `pb_builder_category` WHERE `builder_id` = %s AND cat <> 0 ORDER BY `position` ASC",
                formatSQLString($id)
            ));
            while ($row = $this->db->fetch_assoc($categoryQuery)) {
                $builder['category'][] = $row['cat'];
            }

        }

        return $builder;


    }
    /**
     *    Method: getBuilderById
     *
     *    This method is to get all builder data from database by its id. This method return the
     *    result from database.
     *
     *    @param Integer $id The ID of builder. This should not be empty.
     *
     *    @return ArrayObject The data retrieved from database.
     */
    public function getBuilderById($id)
    {
        // Initialisation
        $this->errMsg = "";
        $errHeader    = "<b>ProductBuilder Error in getBuilderById:</b>";
        $errFooter    = "<i>For more information, please read documentation.</i>";

        // Pulling data
        $builder = $this->db->query_first(sprintf("SELECT * FROM `pb_builders` WHERE `id`=%s", formatSQLString($id)), MYSQLI_ASSOC);

        if (!empty($builder)) {
            $builder['websites'] = array();

            $websitesQuery = $this->db->query(sprintf("SELECT * FROM `pb_websites` WHERE `builder_id`=%s", formatSQLString($id)));
            while ($row = $this->db->fetch_assoc($websitesQuery)) {
                $builder['websites'][] = $row;
            }

            $builder['machines'] = array();
            $machineQuery        = $this->db->query(sprintf("SELECT * FROM `pb_machines` WHERE `builder_id`=%s", formatSQLString($id)));
            while ($row = $this->db->fetch_assoc($machineQuery)) {
                $options      = array();
                $optionsQuery = $this->db->query(sprintf("SELECT * FROM `pb_machines_options` WHERE `machine_id`=%s ORDER BY `position` ASC", formatSQLString($row['id'])));
                while ($optionRow = $this->db->fetch_assoc($optionsQuery)) {
                    $option_data = $this->getOptionById($optionRow['option_id']);
                    $options[]   = array(
                        "id"       => $optionRow['option_id'],
                        "name"     => $option_data['name'],
                        "title"    => $option_data['title'],
                        "qty"      => $optionRow['qty'],
                        "template" => $option_data['template'],
                    );
                }

                $builder['machines'][] = array(
                    "id"                    => $row['id'],
                    "name"                  => $row['name'],
                    "product_id"            => $row['product_id'],
                    "site_id"               => $row['site_id'],
                    "sub_id"                => $row['sub_id'],
                    "price"                 => $row['price'],
                    "currency"              => $row['currency'],
                    "description"           => $row['description'],
                    "special_notes"         => $row['special_notes'],
                    "delivery_pickup"       => $row['delivery_pickup'],
                    "delivery_sea"          => $row['delivery_sea'],
                    "delivery_air"          => $row['delivery_air'],
                    "delivery_pickup_price" => $row["delivery_pickup_price"],
                    "delivery_sea_price"    => $row["delivery_sea_price"],
                    "delivery_air_price"    => $row["delivery_air_price"],
                    "options"               => $options,
                    "position"              => $row["position"],
                    "category"              => $row['cat'],
                );
            }

            $builder['category'] = array();
            $categoryQuery       = $this->db->query(sprintf(
                "SELECT * FROM `pb_builder_category` WHERE `builder_id` = %s AND cat <> 0 ORDER BY `position` ASC",
                formatSQLString($id)
            ));
            while ($row = $this->db->fetch_assoc($categoryQuery)) {
                $builder['category'][] = $row['cat'];
            }

        }

        return $builder;
    }

    /**
     *    Method: getOptionById
     *
     *    This method is to get all option data from database by its id. This method return the
     *    result from database.
     *
     *    @param Integer $id The ID of option. This should not be empty.
     *
     *    @return ArrayObject The data retrieved from database.
     */
    public function getOptionById($id)
    {
        // Initialisation
        $this->errMsg = "";
        $errHeader    = "<b>ProductBuilder Error in getOptionById:</b>";
        $errFooter    = "<i>For more information, please read documentation.</i>";

        // Pulling data
        $option = $this->db->query_first(sprintf("SELECT * FROM `pb_options` WHERE `id`=%s", formatSQLString($id)), MYSQLI_ASSOC);

        if (!empty($option)) {
            $option['options'] = array(
                0 => array(), // Link Product
                1 => array(), // Link Text
                2 => array(), // Link Photo
                3 => array(), // Link Video
            );

            $linkProductQuery = $this->db->query(sprintf("SELECT * FROM `pb_options_product` WHERE `options_id`=%s", formatSQLString($option['id'])));
            $key=0;
            while ($row = $this->db->fetch_assoc($linkProductQuery)) {
                $row['color_html']='';
                if($row['product_id']){
                    $product_color=$this->db->query_first(sprintf("SELECT `colour` FROM `products` WHERE `id`=%d", $row['product_id']), MYSQLI_ASSOC);
                    if($product_color['colour']){
                        $row['color_html']=json_encode($this->getcolorhtmlforbuilderoption($product_color['colour'],$row['color'],$row['position'],$row['default_color']));
                    }
                }
                $rules=[];
                $optionrules = $this->db->query(sprintf("SELECT * FROM `pb_options_rules` WHERE `option_id`=%s AND `type`=0 AND `section_id`=%d ORDER BY `id` asc", formatSQLString($option['id']),
                    $row['id']
                    ));
                while ($ruledata = $this->db->fetch_assoc($optionrules)) {
                    $rules[]=['display_rule'=>$ruledata['display_rule'],'non_display_rule'=>$ruledata['non_display_rule']];
                }
                $row['rules']=$rules;
                $key++;
                $option['options'][0][] = $row;
            }
            $linkTextQuery = $this->db->query(sprintf("SELECT * FROM `pb_options_text` WHERE `options_id`=%s", formatSQLString($option['id'])));
            while ($row = $this->db->fetch_assoc($linkTextQuery)) {
                $rules=[];
                $optionrules = $this->db->query(sprintf("SELECT * FROM `pb_options_rules` WHERE `option_id`=%s AND `type`=1 AND `section_id`=%d ORDER BY `id` asc", formatSQLString($option['id']),
                    $row['id']
                    ));
                while ($ruledata = $this->db->fetch_assoc($optionrules)) {
                    $rules[]=['display_rule'=>$ruledata['display_rule'],'non_display_rule'=>$ruledata['non_display_rule']];
                }
                $row['rules']=$rules;
                $option['options'][1][] = $row;
            }

            $linkPhotoQuery = $this->db->query(sprintf("SELECT * FROM `pb_options_photo` WHERE `options_id`=%s", formatSQLString($option['id'])));
            while ($row = $this->db->fetch_assoc($linkPhotoQuery)) {
                $rules=[];
                $optionrules = $this->db->query(sprintf("SELECT * FROM `pb_options_rules` WHERE `option_id`=%s AND `type`=2 AND `section_id`=%d ORDER BY `id` asc", formatSQLString($option['id']),
                    $row['id']
                    ));
                while ($ruledata = $this->db->fetch_assoc($optionrules)) {
                    $rules[]=['display_rule'=>$ruledata['display_rule'],'non_display_rule'=>$ruledata['non_display_rule']];
                }
                $row['rules']=$rules;
                $option['options'][2][] = $row;
            }

            $linkVideoQuery = $this->db->query(sprintf("SELECT * FROM `pb_options_video` WHERE `options_id`=%s", formatSQLString($option['id'])));
            while ($row = $this->db->fetch_assoc($linkVideoQuery)) {
                 $rules=[];
                $optionrules = $this->db->query(sprintf("SELECT * FROM `pb_options_rules` WHERE `option_id`=%s AND `type`=3 AND `section_id`=%d ORDER BY `id` asc", formatSQLString($option['id']),
                    $row['id']
                    ));
                while ($ruledata = $this->db->fetch_assoc($optionrules)) {
                    $rules[]=['display_rule'=>$ruledata['display_rule'],'non_display_rule'=>$ruledata['non_display_rule']];
                }
                $row['rules']=$rules;
                $option['options'][3][] = $row;
            }

        }
        return $option;
    }

    /**
     *    Method: updateBuilder
     *
     *    This method is to update builder data.
     *
     *    Data Key is in:
     *    - name
     *    - staff
     *    - status
     *    - websites
     *    - options
     *
     *    Websites attribute is an array of site links.
     *
     *    @see addBuilder() For documentation
     *
     *    @param ArrayObject $data Data to be updated.
     *    @param String        $id      Id of builder.
     *
     *    @return Boolean True if successfully updated, false otherwise.
     */
    public function updateBuilder($data, $id)
    {
        //    Initialisation
        $this->errMsg = "";
        $errHeader    = "<b>ProductBuilder Error in updateBuilder: </b>";
        $errFooter    = "<i>For more information, please read documentation</i>";

        // Data attributes
        $dataAttributes = array("name", "staff", "status", "websites", "machines", "category");

        // Check $data
        if (!is_array($data)) {
            $this->errMsg = "{$errHeader} Data should be an array. {$errFooter}";
            return false;
        }

        foreach ($data as $dataKey => $dataVal) {
            if (!in_array($dataKey, $dataAttributes)) {
                $this->errMsg = "{$errHeader} The key <b><i>{$dataKey}</i></b> is Unknow. {$errFooter}";
                return false;
            }
        }

        // Check $id
        if (null == $id || "" == $id) {
            $this->errMsg = "{$errHeader} ID of builder need to be specified. {$errFooter}";
            return false;
        }

        if (!empty($data)) {
            // Get websites
            $websites = (isset($data['websites']) && !empty($data['websites'])) ? $data['websites'] : array();

            // Get options
            $machines = (isset($data['machines']) && !empty($data['machines'])) ? $data['machines'] : array();

            // Get Categories
            $categories = (isset($data['category']) && !empty($data['category'])) ? $data['category'] : array();

            // Remove websites from data to update builder information
            unset($data['websites']);

            // Remove options from data to update builder information
            unset($data['machines']);

            // Remove categories from data to update builder information
            unset($data['category']);

            // For builder information
            if (count($data) > 0) {
                $sqlStatement = "UPDATE `pb_builders` SET";
                $i            = 0; // Marker

                foreach ($data as $dataKey => $dataVal) {
                    // Sql Statement
                    $sqlStatement .= " `{$dataKey}`=" . ((is_numeric($dataVal)) ? $dataVal : formatSQLString($dataVal));

                    // Add comma
                    if ($i < count($data) - 1) {
                        $sqlStatement .= ",";
                    }

                    // Shift marker
                    $i++;
                }

                $sqlStatement .= " WHERE `id`=" . formatSQLString($id);

                $this->db->query($sqlStatement);
            }

            // For websites

            // Clear all previous data
            $this->db->query(sprintf("DELETE FROM `pb_websites` WHERE `builder_id`=%s", formatSQLString($id)));

            if (count($websites) > 0) {
                foreach ($websites as $website) {
                    // Sql statement
                    $this->db->query(sprintf("INSERT INTO `pb_websites` (`link`, `builder_id`) VALUES (%s, %s)",
                        formatSQLString($website),
                        formatSQLString($id)
                    ));
                }
            }

            // For category

            // Clear all previous data
            $this->db->query(sprintf("DELETE FROM `pb_builder_category` WHERE `builder_id`=%s", formatSQLString($id)));

            if (count($categories) > 0) {
                foreach ($categories as $cat) {
                    $this->db->query(sprintf(
                        "INSERT INTO `pb_builder_category` (`cat`, `builder_id`,`position`) VALUES (%d, %s, %d)",
                        $cat['value'],
                        formatSQLString($id),
                        $cat['position']
                    ));
                }
            }

            // For Machines

            $this->db->query(sprintf("DELETE FROM `pb_machines` WHERE `builder_id`=%s", formatSQLString($id)));
            if (count($machines) > 0) {
                foreach ($machines as $machine) {
                    $_machine_id = $machine['id'];
                    $machine_id  = $this->generateUniqueId();

                    $options = $machine['options'];
                    unset($machine['options']);
                    $this->db->query(sprintf(
                        "INSERT INTO `pb_machines` (`id`, `name`, `product_id`, `site_id`, `sub_id`, `price`, `currency`, `description`, `special_notes`, `delivery_pickup`, `delivery_sea`, `delivery_air`, `delivery_pickup_price`, `delivery_sea_price`, `delivery_air_price`, `builder_id`, `position`, `cat`) VALUES (%s, %s, %d, %d, %d, %f, %s, %s, %s, %d, %d, %d, %f, %f, %f, %s, %d, %d)",
                        formatSQLString($machine_id),
                        formatSQLString($machine['name']),
                        $machine['product_id'],
                        $machine['site_id'],
                        $machine['sub_id'],
                        $machine['price'],
                        formatSQLString($machine['currency']),
                        formatSQLString($machine['description']),
                        formatSQLString($machine['special_notes']),
                        $machine['delivery_pickup'],
                        $machine['delivery_sea'],
                        $machine['delivery_air'],
                        $machine['delivery_pickup_price'],
                        $machine['delivery_sea_price'],
                        $machine['delivery_air_price'],
                        formatSQLString($id),
                        $machine['position'],
                        $machine['category']
                    ));

                    $this->db->query(sprintf("DELETE FROM `pb_machines_options` WHERE `machine_id`=%s", formatSQLString($_machine_id)));
                    if (!empty($options)) {
                        foreach ($options as $option) {
                            $this->db->query(sprintf(
                                "INSERT INTO `pb_machines_options` (`machine_id`, `option_id`, `position`,`qty`) VALUES (%s,%s,%d,%d)",
                                formatSQLString($machine_id),
                                formatSQLString($option['id']),
                                $option['position'],
                                $option['qty']
                            ));
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     *    Method: updateOption
     *
     *    This method is to update options data.
     *
     *    Data key has
     *    - name
     *    - options
     *
     *    The options has three keys : 0, 1, 2. [See addOptions Method Documentation]
     *
     *    If options key is not empty, the key associating to the option key must be with options array
     *    when array is passed to this method. [See addOptions Method Documentation for options keys.]
     *
     *    @see addOption() For documentation
     *
     *    @param ArrayObject $data Data to be updated.
     *    @param String        $id      Id of option.
     *
     *    @return Boolean True if successfully updated, false otherwise.     */
    public function updateOption($data, $id)
    {
        //    Initialisation
        $this->errMsg = "";
        $errHeader    = "<b>ProductBuilder Error in updateOption: </b>";
        $errFooter    = "<i>For more information, please read documentation</i>";

        // Data attributes
        $dataAttributes = array("name", "title", "template", "options","option_image","mouse_over_text",'type');

        // Check $data
        if (!is_array($data)) {
            $this->errMsg = "{$errHeader} Data should be an array. {$errFooter}";
            return false;
        }
                      
        foreach ($data as $dataKey => $dataVal) {
            if (!in_array($dataKey, $dataAttributes)) {

                $this->errMsg = "{$errHeader} The key <b><i>{$dataKey}</i></b> is Unknow. {$errFooter}";
                return false;
            }
        }

        // Check $id
        if (null == $id || "" == $id) {
            $this->errMsg = "{$errHeader} ID of builder need to be specified. {$errFooter}";
            return false;
        }

        // Check options
        if (isset($data['options'])) {
            if (!is_array($data['options'])) {
                $this->errMsg = "{$errHeader} Options should be array. {$errFooter}";
                return false;
            }

            if (!$this->checkKeys(array(0, 1, 2), $data['options'])) {
                $this->errMsg = "{$errHeader} Options keys invalid. {$errFooter}";
                return false;
            }
        }
        if (!empty($data)) {
            // For options information
            if (isset($data['name']) && !empty($data['name'])) {
                $this->db->query(sprintf("UPDATE `pb_options` SET `name`=%s, `title`=%s,`mouse_over_text`=%s %s WHERE `id`=%s",
                    formatSQLString($data['name']),
                    formatSQLString($data['title']),
                    formatSQLString($data['mouse_over_text']),
                    (isset($data['option_image']))?',`option_image`='.formatSQLString($data['option_image']).'':'',
                    formatSQLString($id)
                ));
            }
            // For options product

            // Clear all previous data
            $this->db->query(sprintf("DELETE FROM `pb_options_product` WHERE `options_id`=%s", formatSQLString($id)));
            $this->db->query(sprintf("DELETE FROM `pb_options_rules` WHERE `option_id`=%s", formatSQLString($id)));

            foreach ($data['options'][0] as $op) {
                // Sql statement
                $this->db->query(sprintf(
                    "INSERT INTO `pb_options_product` (`options_id`, `product_id`, `price`, `site_id`, `currency`, `position`, `product_name`, `short_desc`,`color`,`default`,`default_color`,`rules`,`qty`,`youtube`) VALUES (%s, %d, %f, %d, %s, %d, %s, %s,%s,%s,%d,%s,%d,%s)",
                    formatSQLString($id),
                    $op['product_id'],
                    $op['price'],
                    $op['site_id'],
                    formatSQLString(strtoupper($op['currency'])),
                    $op['position'],
                    formatSQLString($op['product_name']),
                    formatSQLString($op['short_desc']),
                    formatSQLString($op['color']),
                    formatSQLString($op['default']),
                    $op['default_color'],
                    formatSQLString($op['rules']),
                    $op['qty'],
                    formatSQLString($op['youtube']),
                ));
                $last_inset_id=$this->db->insert_id();
                foreach ($op['rules'] as $key => $rule) {
                      
                        $this->db->query(sprintf(
                            "INSERT INTO `pb_options_rules` (`option_id`, `section_id`, `type`, `display_rule`, `non_display_rule`) VALUES (%s, %d, %d, %s, %s)",
                            formatSQLString($id),
                            $last_inset_id,
                            0,
                            formatSQLString($rule['rule']),
                            formatSQLString($rule['rulenotshow']),
                        ));
                }

            }

            // For options Text

            // Clear all previous data
            $this->db->query(sprintf("DELETE FROM `pb_options_text` WHERE `options_id`=%s", formatSQLString($id)));

            foreach ($data['options'][1] as $op) {
                // Sql statement
                
                $this->db->query(sprintf("INSERT INTO `pb_options_text` (`options_id`, `text`, `header`, `price`, `currency`, `position`,`default`,`rules`,`qty`) VALUES (%s, %s, %s, %f, %s, %d,%s,%s,%d)",
                    formatSQLString($id),
                    formatSQLString($op['text']),
                    formatSQLString($op['header']),
                    $op['price'],
                    formatSQLString($op['currency']),
                    $op['position'],
                   formatSQLString($op['default']),
                    formatSQLString($op['rules']),
                    $op['qty'],
                ));
                 $last_inset_id=$this->db->insert_id();
                foreach ($op['rules'] as $key => $rule) {
                      
                        $this->db->query(sprintf(
                            "INSERT INTO `pb_options_rules` (`option_id`, `section_id`, `type`, `display_rule`, `non_display_rule`) VALUES (%s, %d, %d, %s, %s)",
                            formatSQLString($id),
                            $last_inset_id,
                            1,
                            formatSQLString($rule['rule']),
                            formatSQLString($rule['rulenotshow']),
                        ));
                }
            }

            // For options photo

            // Clear all previous data
            $this->db->query(sprintf("DELETE FROM `pb_options_photo` WHERE `options_id`=%s", formatSQLString($id)));

            foreach ($data['options'][2] as $op) {
                // Sql statement
                $this->db->query(sprintf("INSERT INTO `pb_options_photo` (`options_id`, `img`, `ext`, `header`, `price`, `currency`, `position`, `description`,`default`,`rules`,`qty`) VALUES (%s, %s, %s, %s, %f, %s, %d, %s,%s,%s,%d)",
                    formatSQLString($id),
                    formatSQLString($op['img']),
                    formatSQLString($op['ext']),
                    formatSQLString($op['header']),
                    $op['price'],
                    formatSQLString($op['currency']),
                    $op['position'],
                    formatSQLString($op['photo_description']),
                    formatSQLString($op['default']),
                    formatSQLString($op['rules']),
                    $op['qty'],
                ));
                 $last_inset_id=$this->db->insert_id();
                foreach ($op['rules'] as $key => $rule) {
                      
                        $this->db->query(sprintf(
                            "INSERT INTO `pb_options_rules` (`option_id`, `section_id`, `type`, `display_rule`, `non_display_rule`) VALUES (%s, %d, %d, %s, %s)",
                            formatSQLString($id),
                            $last_inset_id,
                            2,
                            formatSQLString($rule['rule']),
                            formatSQLString($rule['rulenotshow']),
                        ));
                }    
            }

            // For Video feature option

            // Clear all previous data
            $this->db->query(sprintf("DELETE FROM `pb_options_video` WHERE `options_id`=%s", formatSQLString($id)));
         
            foreach ($data['options'][3] as $op) {
                $this->db->query(sprintf(
                    "INSERT INTO `pb_options_video` (`options_id`, `youtube`, `header`, `description`, `infolink`, `price`, `currency`, `position`,`default`,`rules`,`qty`) VALUES (%s, %s, %s, %s, %s, %f, %s, %d ,%s,%s,%d)",
                    formatSQLString($id),
                    formatSQLString($op['youtube']),
                    formatSQLString($op['header']),
                    formatSQLString($op['video_description']),
                    formatSQLString($op['infolink']),
                    $op['price'],
                    formatSQLString($op['currency']),
                    $op['position'],
                    formatSQLString($op['default']),
                    formatSQLString($op['rules']),
                    $op['qty'],
                ));
                $last_inset_id=$this->db->insert_id();
                foreach ($op['rules'] as $key => $rule) {
                      
                        $this->db->query(sprintf(
                            "INSERT INTO `pb_options_rules` (`option_id`, `section_id`, `type`, `display_rule`, `non_display_rule`) VALUES (%s, %d, %d, %s, %s)",
                            formatSQLString($id),
                            $last_inset_id,
                            3,
                            formatSQLString($rule['rule']),
                            formatSQLString($rule['rulenotshow']),
                        ));
                }
            }
        }

        return true;
    }
     public function optionClone($gid, $option_id,$staff_id)
    {
        if($option_id){
            $id = $this->generateUniqueId();
            
            $option_sql="INSERT INTO pb_options (id,name, title, added_date, template,type,option_image,mouse_over_text) SELECT ".formatSQLString($id)." ,concat(name,' - ','clone'), title, now(),template,type,option_image,mouse_over_text FROM pb_options WHERE id =".formatSQLString($option_id)."";

            $product_sql="INSERT INTO pb_options_product (options_id, product_id, product_name, short_desc,price,site_id,currency,position,color,default_color,`default`,`qty`) SELECT ".formatSQLString($id).", product_id, product_name, short_desc,price,site_id,currency,position,color,default_color,`default`,`qty` FROM pb_options_product WHERE options_id =".formatSQLString($option_id)."";

            $photo_sql="INSERT INTO pb_options_photo (options_id, img, ext, header,price,currency,position,description,`default`,`qty`) SELECT ".formatSQLString($id).", img, ext, header,price,currency,position,description,`default`,`qty` FROM pb_options_photo WHERE options_id =".formatSQLString($option_id)."";

            $text_sql="INSERT INTO pb_options_text (options_id, `text`, header,price,currency,position,`default`,`qty`) SELECT ".formatSQLString($id).", `text`, header,price,currency,position,`default`,`qty` FROM pb_options_text WHERE options_id =".formatSQLString($option_id)."";

           $video_sql="INSERT INTO pb_options_video (options_id, `youtube`, header,description,infolink,price,currency,position,`default`,`qty`) SELECT ".formatSQLString($id).", `youtube`, header,description,infolink,price,currency,position,`default`,`qty` FROM pb_options_video WHERE options_id =".formatSQLString($option_id)."";
            $this->db->query($option_sql);
            $this->db->query($product_sql);
            $this->db->query($photo_sql);
            $this->db->query($text_sql);
            $this->db->query($video_sql);
            unset($option_sql);
            unset($product_sql);
            unset($photo_sql);
            unset($text_sql);
            unset($video_sql);
        }
        
        return true;
    }
    /**
     *    Method: getCategoryNameById
     *
     *    This method is to get the category name from database
     *
     *    @param int $id The ID of category
     *
     *    @return String Name of category
     */
    public function getCategoryNameById($id)
    {
        $cat = $this->db->query_first(sprintf(
            "SELECT * FROM `pb_machines_category` WHERE `id` = %d AND `deleted_at` IS NULL",
            $id
        ), MYSQLI_ASSOC);
        return $cat['name'];
    }

    public function getCategoryImageById($id)
    {
        $cat = $this->db->query_first(sprintf(
            "SELECT * FROM `pb_machines_category` WHERE `id` = %d AND `deleted_at` IS NULL",
            $id
        ), MYSQLI_ASSOC);
        return $cat['filename'];
    }

    /**
     *    Method: getAllCategoryNames
     *
     *    This method is to get all category name from database
     *
     *    @return ArrayObject Names of categories
     */
    public function getAllCategoryNames()
    {
        $cat   = array();
        $query = $this->db->query("SELECT * FROM `pb_machines_category` WHERE `deleted_at` IS NULL ORDER BY `id` ASC");
        while ($row = $this->db->fetch_assoc($query)) {
            $cat[] = $row;
        }

        return $cat;
    }

    /**
     *    Method: isBuilderExist
     *
     *    This method check if the builder exists
     *
     *    @param String $id The ID of builder
     *
     *    @return Boolean True if exists, false otherwise.
     */
    public function isBuilderExist($id)
    {
        $builder = $this->getBuilderById($id);
        return (!empty($builder));
    }

    /**
     *    Method: isBuilderWebsiteExist
     *
     *    This method check if the link for builder already exists
     *
     *    @param String $link The website link of builder
     *
     *    @return Boolean True if exists, false otherwise.
     */
    public function isBuilderWebsiteExist($link, $builder_id = null)
    {

        $statement = "";

        if (null != $builder_id) {
            $statement = " AND `builder_id` != '{$builder_id}'";
        }

        $query = $this->db->query(sprintf("SELECT * FROM `pb_websites` WHERE `link` = %s {$statement}", formatSQLString($link)));

        return ($this->db->num_rows($query) > 0);
    }

    /**
     *    Method: isOptionExist
     *
     *    This method check if the option exists
     *
     *    @param String $id The ID of option
     *
     *    @return Boolean True if exists, false otherwise.
     */
    public function isOptionExist($id)
    {
        $option = $this->getOptionById($id);
        return (!empty($option));
    }

    /**
     *    Method: deleteBuilder
     *
     *    This method delete the builder data by its id
     *
     *    @param String $id Id of builder
     */
    public function deleteBuilder($id)
    {
        $this->db->query(sprintf("DELETE FROM `pb_builders` WHERE `id`=%s", formatSQLString($id)));
        $this->db->query(sprintf("DELETE FROM `pb_websites` WHERE `builder_id`=%s", formatSQLString($id)));
        $this->db->query(sprintf("DELETE FROM `pb_builders_options` WHERE `builder_id`=%s", formatSQLString($id)));
    }

    /**
     *    Method: deleteOption
     *
     *    This method delete the options data by its id
     *
     *    @param String $id Id of option
     */
    public function deleteOption($id)
    {
        $this->db->query(sprintf("UPDATE `pb_options` SET `is_deleted`=%d WHERE `id`=%s",
                1,
            formatSQLString($id)
        ));
        /*$this->db->query(sprintf("DELETE FROM `pb_options` WHERE `id`=%s", formatSQLString($id)));
        $this->db->query(sprintf("DELETE FROM `pb_options_product` WHERE `options_id`=%s", formatSQLString($id)));
        $this->db->query(sprintf("DELETE FROM `pb_options_text` WHERE `options_id`=%s", formatSQLString($id)));
        $this->db->query(sprintf("DELETE FROM `pb_options_photo` WHERE `options_id`=%s", formatSQLString($id)));*/
    }

    /**
     *    Method: getErrorMessage
     *
     *    This method will return the errMsg.
     *
     *    @return String Return $errMsg
     */
    public function getErrorMessage()
    {
        return ("" == $this->errMsg) ? "No errors were found." : $this->errMsg;
    }

    /**
     *    Method: sortOptionsByPosition
     *
     *    This method sort the options by position.
     *
     *    @param ArrayObject $options Options to be sorted
     *
     *    @return ArrayObject Sorted options
     */
    public function sortOptionsByPosition($options)
    {
        $newOptions = array();
        foreach ($options as $optionkey => $optionval) {
            foreach ($optionval as $option) {
                $newOptions[$option['position']]               = $option;
                $newOptions[$option['position']]['option_key'] = $optionkey;
            }
        }

        ksort($newOptions);

        return $newOptions;
    }

    /**
     *    Method: sortOptionsByPrice
     *
     *    This method sort the options by price.
     *    Low to hight
     *
     *    @param ArrayObject $options Options to be sorted
     *
     *    @return ArrayObject Sorted options
     */
    public function sortOptionsByPrice($options)
    {
        $newOptions = array();
        if (is_array($options)) {
            foreach ($options as $optionkey => $optionval) {
                foreach ($optionval as $option) {
                    $newOptions[$option['position']]               = $option;
                    $newOptions[$option['position']]['option_key'] = $optionkey;
                }
            }
        }

        $price = array();
        foreach ($newOptions as $key => $row) {
            $price[$key] = $row['price'];
        }
        array_multisort($price, SORT_ASC, $newOptions);
        return $newOptions;
    }

    /**
     *    Helper Functions
     */

    /**
     *    Method: generateUniqueId
     *
     *    This method generates unique md5 ID with 32 characters long.
     *
     *    @return String The string of generated unique id
     */
    private function generateUniqueId()
    {
        return md5(uniqid());
    }

    /**
     *    Method: checkKeys
     *
     *    This method checks if the data has all required keys for adding/updating database.
     *
     *    @return Boolean True if the data has all required keys, false otherwise.
     */
    private function checkKeys($keys, $data)
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $data)) {
                return false;
            }
        }

        return true;
    }

    /**
     *    Method: builderSetLock
     *
     *    This method is to set the generic lock for builder section
     *
     *    @param int $id          The generic id for builder
     *    @param int $staff_id The ID of staff member who is being locked
     *    @param int $timeout  The expiry time of lock
     */
    public function builderSetLock($id, $staff_id = 0, $timeout = 120)
    {
        $DB           = $this->db;
        $section_name = formatSQLString("pb_builder");
        if (0 != $staff_id) {
            $DB->query(sprintf(
                "UPDATE `generic_locks` SET `expiry_date` = %s, `modified` = NOW() WHERE `section_name` = %s AND `generic_id` = %d AND `staff_id` = %d AND `expiry_date` > NOW() LIMIT 1",
                formatSQLDateTime(time() + $timeout),
                $section_name,
                $id,
                $staff_id
            ));

            if ($DB->affected_rows() == 0) {
                $DB->query(sprintf(
                    "INSERT INTO `generic_locks` (`section_name`, `generic_id`, `staff_id`, `expiry_date`, `created`, `modified`) VALUES (%s, %d, %d, %s, NOW(), NOW())",
                    $section_name,
                    $id,
                    $staff_id,
                    formatSQLDateTime(time() + $timeout)
                ));
            }
        } else {
            $DB->query(sprintf(
                "UPDATE `generic_locks` SET `expiry_date` = NOW(), `modified` = NOW() WHERE `section_name` = %s AND `generic_id` = %d AND `expiry_date` > NOW()",
                $section_name,
                $id
            ));
        }
    }

    /**
     *    Method: optionSetLock
     *
     *    This method is to set the generic lock for option section
     *
     *    @param int $id          The generic id for option
     *    @param int $staff_id The ID of staff member who is being locked
     *    @param int $timeout  The expiry time of lock
     */
    public function optionSetLock($id, $staff_id = 0, $timeout = 120)
    {
        $DB           = $this->db;
        $section_name = formatSQLString("pb_option");
        if (0 != $staff_id) {
            $DB->query(sprintf(
                "UPDATE `generic_locks` SET `expiry_date` = %s, `modified` = NOW() WHERE `section_name` = %s AND `generic_id` = %d AND `staff_id` = %d AND `expiry_date` > NOW() LIMIT 1",
                formatSQLDateTime(time() + $timeout),
                $section_name,
                $id,
                $staff_id
            ));

            if ($DB->affected_rows() == 0) {
                $DB->query(sprintf(
                    "INSERT INTO `generic_locks` (`section_name`, `generic_id`, `staff_id`, `expiry_date`, `created`, `modified`) VALUES (%s, %d, %d, %s, NOW(), NOW())",
                    $section_name,
                    $id,
                    $staff_id,
                    formatSQLDateTime(time() + $timeout)
                ));
            }
        } else {
            $DB->query(sprintf(
                "UPDATE `generic_locks` SET `expiry_date` = NOW(), `modified` = NOW() WHERE `section_name` = %s AND `generic_id` = %d AND `expiry_date` > NOW()",
                $section_name,
                $id
            ));
        }
    }

    /**
     *    Method: builderGetLock
     *
     *    This method is to get the lock by generic id
     *
     *    @param int $id The generic id for builder
     *
     *    @return (Boolean | ArrayObject) False if lock doesn't exist or expired. Otherwise, return the data of generic lock
     */
    public function builderGetLock($id)
    {
        $DB = $this->db;

        $row = false;
        if ($row = $DB->query_first(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`expiry_date`) AS `expiry_time` FROM `generic_locks` WHERE `section_name` = 'pb_builder' AND `generic_id` = %d AND `expiry_date` > NOW() LIMIT 1",
            $id
        ), MYSQLI_ASSOC)) {
            if ($row['expiry_time'] < time()) {
                $row = false;
            }
        }

        return $row;
    }

    /**
     *    Method: optionGetLock
     *
     *    This method is to get the lock by generic id
     *
     *    @param int $id The generic id for option
     *
     *    @return (Boolean | ArrayObject) False if lock doesn't exist or expired. Otherwise, return the data of generic lock
     */
    public function optionGetLock($id)
    {
        $DB = $this->db;

        $row = false;
        if ($row = $DB->query_first(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`expiry_date`) AS `expiry_time` FROM `generic_locks` WHERE `section_name` = 'pb_option' AND `generic_id` = %d AND `expiry_date` > NOW() LIMIT 1",
            $id
        ), MYSQLI_ASSOC)) {
            if ($row['expiry_time'] < time()) {
                $row = false;
            }
        }

        return $row;
    }

    /**
     *    Method: builderGetLocks
     *
     *    This method is to get all the lock data
     *
     *    @return ArrayObject The data of generic lock
     */
    public function builderGetLocks()
    {
        $DB = $this->db;

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`expiry_date`) AS `expiry_time` FROM `generic_locks` WHERE `section_name` = 'pb_builder' AND `expiry_date` > NOW()"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row["generic_id"]] = $row;
            }
            $DB->free_result($query);
        }

        return $result;
    }

    /**
     *    Method: optionGetLocks
     *
     *    This method is to get all the lock data
     *
     *    @return ArrayObject The data of generic lock
     */
    public function optionGetLocks()
    {
        $DB = $this->db;

        $result = array();
        if ($query = $DB->query(sprintf(
            "SELECT *, UNIX_TIMESTAMP(`expiry_date`) AS `expiry_time` FROM `generic_locks` WHERE `section_name` = 'pb_option' AND `expiry_date` > NOW()"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[$row["generic_id"]] = $row;
            }
            $DB->free_result($query);
        }

        return $result;
    }
    private function getcolorhtmlforbuilderoption($color,$selected,$row,$default_color){
            $row=$row-1;
            if(count(explode(',', $color))){
            $selected=explode(',', $selected);
            $html_option='';
            $html ='<table class="table_fields" style="width: 100%; margin-top: 10px;">
            <tr>
                <td>
                    <ul style="list-style:none;">';
                    
                    foreach (explode(',', $color) as $key => $colr) {
                        $color_detail=getProductColourById($colr);
                        $selec=(in_array($colr,$selected))?"checked":"";
                        $selected_val=($color_detail['id']==$default_color)?'selected':'';
                        $html_option .='<option value="'.$color_detail['id'].'" '.$selected_val.' >'.$color_detail["colour"].'</option>';
                        $html =  $html. '<li style="text-align: center; display: inline-block; vertical-align: top; margin: 10px 5px;">
                            <div class="cat action" >
                            <input type="checkbox" value="'.$color_detail['id'].'" name="pb_options[options][0]['.$row.'][color][]" style="width: 24px; height: 24px;" '.$selec.'>
                            <p style="padding:12px;background:'.$color_detail["color_val"].'">
                                </p>
                                </div>
                        </li>';
                }
                $html .='</ul> </td> </tr> </table>';
                $html .='Default Colour : <select name="pb_options[options][0]['.$row.'][default_color]" style="width: 80%; resize: vertical;height: 26px; margin-top: 10px;">';
                $html .=$html_option;
                $html .='</select>';
            return $html;
            }
        return '';
    }
}
