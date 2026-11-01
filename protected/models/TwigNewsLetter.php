<?php
namespace app\models;

use Newsletter_Handler;
use app\models\TwigImage;
use app\cache\CacheService;

/*
components/post-card.twig:
.excerpt
.categories
.date
.image
.name
.url
*/

class TwigNewsLetter
{
    public $id;

    /**
     * @param int $id
     */
    function __construct($id = 0)
    {
        $this->id = $id;
    }

    /** @var mixed[]|null */
    private $newsLetter = null;

    /**
     * @param string $field
     *
     * @return mixed
     */
    function __get($field)
    {
        if (is_null($this->newsLetter)) {
            moduleLoad('newsletter'); //newsFromDatabase

            if (class_exists('Newsletter_Handler')) {
                $NH         = new Newsletter_Handler();   
                $this->newsLetter = $NH->fromDatabase($this->id);
                return $this->newsLetter[$field] ?? null;
            }
        }

        return $this->newsLetter[$field] ?? null;
    }


    /**
     * @param int[] $list
     *
     * @return TwigNews[]
     */
    public static function list($list)
    {
        foreach ($list as $k => $v) {
            $list[$k] = new TwigNewsLetter($v);
        }
        return $list;
    }

    /**
     * @param mixed[] $criteria
     *
     * @return TwigNews[]
     */
    public static function search($criteria = [])
    {
        global $_GLOBALS;

        moduleLoad('newsletter'); //newsSearch

        if (array_key_exists('id', $criteria)) {
            $criteria['id'] = $criteria['id'];
        }

        if (class_exists('Newsletter_Handler')) {
            $NH = new Newsletter_Handler();
            $newsletter_list = array_reverse($NH->search($criteria));
            return self::list($newsletter_list);
        }
        return array();
    }

    public function name(){
         return $this->__get('subject');
    }

    public function short_desc(){
         return $this->__get('short_desc');
    }

    public function description(){
         return $this->__get('description');
    }

    public function image()
    {
        $img = "/noimage.png";
        $image_matches = array();
        preg_match_all('/<img[^<]+src="([^"]+)"/', $this->description(), $image_matches);
        $pattern = '/^https:\/\/www\.arcooda\.com\/p\/.*\d+\.jpg$/'; 
        foreach($image_matches[1] as $k => $v)
        {
            if (preg_match($pattern, $v)) {
                $img =  $v; // Get the first match
                break;
            }
        }
        return $img;
    }

    public function newsletter_url(){
         return $this->__get('newsletter_url');
    }
    public function style_id(){
        return $this->__get('style_id');
    }
    public function newsid(){
        return $this->__get('id');
    }

    public function timestamp_time(){
        return $this->__get('timestamp_time');
    }
    public function newsletter_content()
    {
        global $_GLOBALS;
        moduleLoad('newsletter'); //newsFromDatabase
        $section_handler = new Newsletter_Handler();
        $styles_list = $section_handler->styleFromDatabase(array());
        if (strpos($_SERVER['HTTP_REFERER'], '/webadmin/') === false || $_GLOBALS['staff']['id'] == 0) foreach($styles_list as $k => $v) if ($v['site_id'] != $_GLOBALS['site_settings']['site_id']) unset($styles_list[$k]);
        if (!in_array($this->style_id(), array_keys($styles_list)))
        {
            return 'News letter not found';
        }
        $replace = $_GLOBALS['visitor'];
        if ($_GLOBALS['member']['id'] != 0) $replace = $_GLOBALS['member'];
        $replace['newsletter_cookie'] = '';
        $replace['tracker_key'] = '';
        $newsletter_content = $this->description();
        $newsletter_content = $section_handler->negotiateContent($newsletter_content, $replace);
        $newsletter_content = $section_handler->generateMailMerge($newsletter_content, $replace);
        $newsletter_content = $section_handler->generateMailMerge($newsletter_content);
        $replace = array();
        $replace['tracker_key'] = '';
        if ($_COOKIE['newsletter_id'] != '' && hexdec(substr($_COOKIE['newsletter_id'],-8)) == $this->newsid() && moduleLoad("member"))
        {
            $replace = memberFromDatabase(hexdec(substr(substr($_COOKIE['newsletter_id'],0,-8),-8)));
            $replace['tracker_key'] = '/mail/' . $_COOKIE['newsletter_id'];
        }
        elseif ($_GLOBALS['member']['id'] != 0)
        {
            $replace = $_GLOBALS['member'];
            $replace['tracker_key'] = sprintf('/mail/%08x%08x', $replace['id'], $newsletter_id);
        }
        foreach($replace as $k => $v) if (!is_array($v)) $newsletter_content = str_replace('[' . strtoupper($k) . ']', nl2br(formatHTMLSafe($v)), $newsletter_content);
        $newsletter_content = preg_replace('/\[[A-Z_]{1,32}\]/', '...', $newsletter_content);

        return $newsletter_content;

    }

}
