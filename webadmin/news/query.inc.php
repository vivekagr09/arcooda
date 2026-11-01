<?php
include_once __DIR__ . '/query_news.inc.php';
include_once __DIR__ . '/query_news_comment.inc.php';
include_once __DIR__ . '/query_news_image.inc.php';
include_once __DIR__ . '/query_news_video.inc.php';

if (!class_exists('News_Handler')) {
    // include_once __DIR__ . '/../home_common/class.genericsection_handler.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . "/webadmin/home_common/class.genericsection_handler.php";
    class News_Handler extends GenericSection_Handler
    {
        public $section_name    = 'news';
        public $table_name      = "news";
        public $database_prefix = '';
        public $search_bits     = array('`a`.`news_id`', '`a`.`news_title`', '`a`.`long_description`', '`a`.`short_description`', '`a`.`meta_title`', '`a`.`meta_key`', '`a`.`meta_desc`', '`a`.`news_caption`');
        public $primary_key     = 'news_id';
        public function search($criteria = '',$order_by = NULL)
        {
            return parent::search($criteria,'date_added');
        }
        public function totalnews($criteria = '')
        {
            return parent::totalnews($criteria);
        }
        public function fromDatabase($id)
        {
            global $_GLOBALS;
            $result = parent::fromDatabase($id);

            if ('' != $result['image']) {
                $result['image_url'] = '/' . $result['image'];
                $result['thumb_url'] = '/' . $result['thumbnail'];
            }
            if ('' != $result['thumbnail']) {
                $result['thumb_url'] = '/' . $result['thumbnail'];
            }
            $result['news_url'] = sprintf("/news/%d.html", $result["news_id"]);

            if (!empty($_GLOBALS["site_settings"]["static_path"] ?? '') && substr($_SERVER["REQUEST_URI"], 0, 10) != "/webadmin/") {
                if (array_key_exists('image_url', $result)) {
                    foreach (array(
                        '/images/news/large/' . $result['id'] . '.png',
                        '/images/news/large/' . $result['id'] . '.jpg',
                        '/images/news/thumb/' . $result['id'] . '.png',
                        '/images/news/thumb/' . $result['id'] . '.jpg',
                    ) as $v) {
                        if (is_file($_GLOBALS["site_settings"]["static_path"] . $v)) {
                            $result['image_url'] = $_GLOBALS["site_settings"]["static_uri"] . $v;
                            break;
                        }
                    }
                }
                if (array_key_exists('thumb_url', $result)) {
                    foreach (array(
                        '/images/news/thumb/' . $result['id'] . '.png',
                        '/images/news/thumb/' . $result['id'] . '.jpg',
                        '/images/news/large/' . $result['id'] . '.png',
                        '/images/news/large/' . $result['id'] . '.jpg',
                    ) as $v) {
                        if (is_file($_GLOBALS["site_settings"]["static_path"] . $v)) {
                            $result['thumb_url'] = $_GLOBALS["site_settings"]["static_uri"] . $v;
                            break;
                        }
                    }
                }
            }

            return $result;
        }
        public function formatNumber($id)
        {
            return sprintf('NW#%04d', $id);
        }
        public function getDefaultImage($id)
        {
            global $DB;
            return $DB->query_first("SELECT *  FROM `news_images` WHERE `news_id` = " . intval($id) . " AND default_image='y' LIMIT 1",MYSQLI_ASSOC);
        }
        public function getAllImages($id,$name)
        {
            global $DB;
            $images=[];
            $query= $DB->query("SELECT *  FROM `news_images` WHERE `news_id` = " . intval($id) . "  AND online='y' order by `rank` asc");
            while ($row = $DB->fetch_assoc($query)) {
                    $row['src']=sprintf(
                        '/news_image/%s-%d.%s',
                        urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $row['caption']), '-')),
                        $row['id'],
                        'jpg'
                    );
                    $images[] = $row;
            }
            return $images;
        }
        public function getAllVideos($id)
        {
            global $DB;
            $news_videos=[];
            $query= $DB->query("SELECT *  FROM `news_videos` WHERE `news_id` = " . intval($id) . " AND active='y' order by `rank` asc");
            while ($row = $DB->fetch_assoc($query)) {
                    $news_videos[] = $row;
            }
            return $news_videos;
        }
        public function getOptionList($table_name)
        {
            global $DB;
            global $_GLOBALS;
            $table       = '';
            $option_list = array();
            switch ($table_name) {
                case 'id':
                case 'news_id':
                    $table = 'news';
                    $key   = '`news_id`';
                    $value = "CONCAT_WS(' - ', `date_added`, `news_title`";
                    break;

                case 'active':
                case 'archived':
                    return array("y" => "Yes", "n" => "No");

                case 'author':
                case 'change_by':
                    return getStaffList();

                case 'style_id':
                    $table = 'news_styles';
                    if (3 != $_GLOBALS['site_settings']['site_id']) {
                        $table .= " WHERE site_id=" . $_GLOBALS['site_settings']['site_id'];
                    }

                    $key   = 'style_id';
                    $value = 'style_description';
                    break;

                case 'thumbnail_path':
                    return $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/news/thumb';

                case 'image_path':
                    return $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/news/image';

                case 'ext_fields':
                    return array(
                        'id'            => '`a`.`news_id`',
                        'time_added'    => 'UNIX_TIMESTAMP(`a`.`date_added`)',
                        'modified_time' => 'UNIX_TIMESTAMP(`a`.`last_change`)',
                    );
                    break;

                default:
                    return parent::getOptionList($table_name);
            }
            if ('' != $table) {
                $query = $DB->query("SELECT " . $key . ", " . $value . " FROM " . $table);
                while ($row = $DB->fetch_array($query, MYSQLI_NUM)) {
                    $option_list[$row[0]] = $row[1];
                }

            }
            return $option_list;
        }
    }
}
if (!defined('QUERY_NEWS')) {
    define('QUERY_NEWS', 111223);
}
