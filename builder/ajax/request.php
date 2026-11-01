<?php
/**
 *     Project: Product Builder
 *
 *    request.php
 *
 *    All templates are requested via ajax.
 *    This ajax return the templates and the status of if
 *    the options are all loaded.
 *
 *    @author Kelvin (khtat@highwaygroup.net)
 *    @since  01/02/2018
 */
function utf8ize($d) {
    if (is_array($d)) {
        foreach ($d as $k => $v) {
            $d[$k] = utf8ize($v);
        }
    } else if (is_string ($d)) {
        return utf8_encode($d);
    }
    return $d;
}
// Include common.inc.php
$path = realpath(dirname(__FILE__) . "/..");
include $path . "/inc/common.inc.php";

// Get the list of countries
$countries = getCountryList(false);

if (isset($_GET['machine_key']) && !empty($_GET['machine_key']) &&
    isset($_GET['option_key']) && !empty($_GET['option_key']) &&
    isset($_GET['builder_id']) && !empty($_GET['builder_id'])) {
    $machineKey = intval($_GET['machine_key']) - 1; // Machine Array Key
    $optionKey  = intval($_GET['option_key']) - 1; // Option Array Key
    $builder_id = $_GET['builder_id'];

    $preference = 0;

    // Initiate product builder module
    $pb = new ProductBuilder($DB);
    $selected_data=(isset($_GET['summary']))?$_GET['summary']:[];
    // Get the builder
    $builder = $pb->getBuilderByIdWithRules($builder_id,$selected_data);
    // Get builder website
    $website = (isset($_GET['website'])) ? $_GET['website'] : "";

    // Link for continue shopping
    $continueShopping = $_SERVER['HTTP_HOST'];

    // Get machines data from builder
    $machines = $builder['machines'];

    // Check if it's final step
    $done = (count($machines[$machineKey]['options']) < $optionKey);

    ob_start();

    if ($done) {
        // Final step then get summary page
        // Summary of items selected
        $summary = array();

        // Votalge selected
        $voltage = 0;

        // Shipping method selected
        $shipping = "";

        // Price of shipping method
        $shipping_price = 0;

        // Total price of whole built
        $total_price = 0;

        // Get summary of items selected
        if (isset($_GET['summary']) && !empty($_GET['summary'])) {
            $summary = $_GET['summary'];
        }

        // Get voltage selected
        if (isset($_GET['voltage']) && !empty($_GET['voltage'])) {
            $voltage = $_GET['voltage'];
        }

        // Get total price of whole built
        if (isset($_GET['total_price']) && !empty($_GET['total_price'])) {
            $total_price = $_GET['total_price'];
        }

        // Get the shipping methods and its price
        if (isset($_GET['shipping']) && !empty($_GET['shipping'])) {
            if ("pickup" == $_GET['shipping']) {
                $shipping       = "Pickup";
                $shipping_price = $machines[$machineKey]['delivery_pickup_price'];
            } else if ("sea" == $_GET['shipping']) {
                $shipping       = "Sea Freight";
                $shipping_price = $machines[$machineKey]['delivery_sea_price'];
            } else {
                $shipping       = "Air Freight";
                $shipping_price = $machines[$machineKey]['delivery_air_price'];
            }
        }

        // Get the summary page
        include "../templates/summary.tpl.php";
    } else if (count($machines[$machineKey]['options']) == $optionKey) {
        // For preference page
        $preference = 1;

        // Get preference page
        include "../templates/preference.tpl.php";
    } else {
        // Option items selection page
        $options = $machines[$machineKey]['options'][$optionKey];

        // Get the page based on template
        if ("standard" == $options['template']) {
            include "../templates/standard_v2.tpl.php";
        } else {
            include "../templates/videofeat.tpl.php";
        }
    }

    // Get all HTML Content
    $html = ob_get_contents();

    // Clean the buffer
    ob_end_clean();
    $ioption_html='';
    if(isset($optionData) && $optionData['option_image']){
        ob_start();
        //@fixme /images/builder/... needs to be relocated
?>
<div class="grid-inline-top align-middle cat" style="outline:none;padding:25px 0;border-left:1px solid #eee;border-right:1px solid #eee;width:259px;margin:10px"><div class="option-container-border"><div class="option-container">
    <div class="option-name-wrapper"><h4><?=$optionData['title']?></h4></div>
    <div class="option-image-wrapper"><img src="/images/builder/<?=$optionData['option_image']?>" style="width:99%;height:250px" class="option-image" /></div>
    <div style="padding:42px 0"></div>
    <div class="desc-wrapper" style="display:none"><p class="clr option-desc"><?=$optionData['mouse_over_text']?></p></div>
</div></div></div>
<?php
        $ioption_html = ob_get_clean();
    }
    $html= $ioption_html.$html;
    // Build option list link
    $option_list = array();
    foreach ($machines[$machineKey]['options'] as $k => $v) {
        $option_list[$k] = strtoupper($v['title']);
    }

    $option_list[] = "PREFERENCE";
    $option_list[] = "SUMMARY";
    // Build json object for returned result
    $result = array(
        "html"        => $html,
        "done"        => ($done) ? 1 : 0,
        "total"       => count($machines[$machineKey]['options']) + 1,
        "option_list" => $option_list,
        "builder_id"  => $_GET['builder_id'],
        "preference"  => $preference,
        'defaultkey'  => $defaultkey ?? 0,
        'default_color_array'=>isset($default_color_array)?$default_color_array:[]
    );

    // Return all contents
    echo json_encode(utf8ize($result));
}
