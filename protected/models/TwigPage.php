<?php
namespace app\models;
use app\cache\CacheService;

moduleOverride('page', $_SERVER['DOCUMENT_ROOT'] . '/webadmin/page/query.inc.php');

class TwigPage
{
    public $id;

    function __construct($id = 0)
    {
        $this->id = $id;
    }

    private $_page = null;
    function __get($field)
    {
        if (is_null($this->_page)) {
            moduleLoad('page');
            $cacheService = new CacheService();
            $this->_page = $cacheService->getData('page', $this->id);
            if($this->_page == false){   
                $this->_page = pageFromDatabase($this->id);
                $cacheService->updateData('page', $this->id,$this->_page);
            }
        }
        if (is_array($this->_page) && array_key_exists($field, $this->_page)) {
            return $this->_page[$field];
        }
        return null;
    }

    function __tostring()
    {
        return $this->name;
    }


    public static function list($list)
    {
        foreach ($list as $k => $v) {
            $list[$k] = new TwigPage($v);
        }
        return $list;
    }
    public static function search($criteria = [])
    {
        global $_GLOBALS;
        if (array_key_exists('page', $_GLOBALS['criteria_options'])) {
            $criteria = array_merge(
                $_GLOBALS['criteria_options']['page'],
                $criteria
            );
        }
        moduleLoad('page');
        return self::list(pageSearch($criteria));
    }


    public function title()
    {
        $title = $this->title;
        if (empty($title)) $title = $this->name();
        return trim($title);
    }
    public function description()
    {
        $desc = $this->description;
        if (empty($desc)) $desc = explode("\n", $this->content)[0];
        return trim($desc);
    }
    public function keywords()
    {
        $keys = $this->keywords;
        if (empty($keys)) {
            $keys = ucwords(implode(', ', preg_split('/[,\-\s]+/', $this->content)));
        }
        return $keys;
    }
    public function breadcrumbs()
    {
        return [
            ['title' => 'Home', 'url' => '/'],
            ['title' => $this->name(), 'url' => $this->url()],
        ];
    }


    private $_children = null;
    public function children()
    {
        if (is_null($this->_children)) {
            $this->_children = self::search(['parent_id' => $this->id]);
        }
        return $this->_children;
    }

    private $_chapters = null;
    public function chapters()
    {
        if (is_null($this->_chapters)) {
            $this->_chapters = [];
            moduleLoad('page');
            foreach (pageChapterSearch(['page_id' => $this->id]) as $v) {
                //TODO: $this->_chapters[] = $v['image_url'];
                $this->_chapters[] = pageChapterFromDatabase($v);
            }
        }
        return $this->_images;
    }

    private $_images = null;
    public function images()
    {
        if (is_null($this->_images)) {
            $this->_images = [];
            /*
            moduleLoad('page');
            foreach (pageImagesFromDatabase($this->id) as $v) {
                //TODO: $this->_images[] = $v['image_url'];
            }
            */
        }
        return $this->_images;
    }

    public function name() {
        return $this->name;
    }
    public function content() {
        return $this->content;
    }
    public function contact_form() {
        return $this->contact_form;
    }
    public function form_layout() {
        return $this->form_layout;
    }
    public function have_question() {
        return $this->have_question;
    }
    public function url()
    {
        if ($this->content_type == 'url') {
            return $this->content;
        }
        return sprintf(
            '/%s',
            urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $this->slug), '-'))
        );
    }

    private $_parent   = null;
    public function parent()
    {
        if (is_null($this->_parent)) {
            $this->_parent = new TwigPage($this->parent_id);
        }
        return $this->_parent;
    }
}
