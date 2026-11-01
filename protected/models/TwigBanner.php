<?php
namespace app\models;

use app\models\TwigImage;

/*
block-slideshow.twig:
.image
.url
.offer
.title
.details
.button
*/

class TwigBanner
{
    /**
     * @var int $id
     */
    public $id;

    /**
     * @param int $id
     */
    public function __construct($id = 0)
    {
        $this->id = $id;
    }

    /**
     * @var mixed[]|null $banner
     */
    private $banner = null;

    /**
     * @param string $field
     *
     * @return mixed
     */
    public function __get($field)
    {
        if (is_null($this->banner)) {
            moduleLoad('banner');
            $this->banner = bannerFromDatabase($this->id);
        }
        if (is_array($this->banner) && array_key_exists($field, $this->banner)) {
            return $this->banner[$field];
        }
        return null;
    }

    /**
     * @return string
     */
    public function __tostring()
    {
        return '[Banner id:' . $this->id . ']';
    }

    /**
     * @param int[] $list
     *
     * @return TwigBanner[]
     */
    public static function list($list)
    {
        $result = [];
        foreach ($list as $k => $v) {
            $result[$k] = new TwigBanner($v);
        }

        return $result;
    }

    /**
     * @param mixed[] $criteria
     *
     * @return TwigBanner[]
     */
    public static function search($criteria = [])
    {
        global $_GLOBALS;
        if (array_key_exists('banner', $_GLOBALS['criteria_options'])) {
            $criteria = array_merge(
                $_GLOBALS['criteria_options']['banner'],
                $criteria
            );
        }
        moduleLoad('banner');
        return self::list(bannerSearch($criteria));
    }

    public function incrementClicks() {
        moduleLoad('banner');
        bannerAddClick($this->id);
    }

    public function incrementViews() {
        moduleLoad('banner');
        bannerAddView($this->id);
    }

    /**
     * @return string
     */
    public function button()
    {
        return $this->__get('button');
    }

    /**
     * @return string
     */
    public function details()
    {
        return $this->__get('details');
    }

    /**
     * @return TwigImage
     */
    public function image()
    {
        return is_file($this->image_file()) ? new TwigImage([
            'src' => sprintf(
                '/lp/%s-%d.jpg',
                urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $this->title()), '-')),
                $this->id
            ),
            'alt' => $this->alt(),
        ]) : null;
    }

    /**
     * @return string
     */
    public function image_file()
    {
        moduleLoad('banner');
        return sprintf(
            '%s/%s',
            bannerGetOptionList('image_path'),
            $this->__get('image')
        );
    }

    /**
     * @return TwigImage
     */
    public function blur_image()
    {
        return is_file($this->blur_file()) ? new TwigImage([
            'src' => sprintf(
                '/lp/%s-h%d.jpg',
                urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $this->title()), '-')),
                $this->id
            ),
            'alt' => $this->alt(),
        ]) : null;
    }

    /**
     * @return string
     */
    public function blur_file()
    {
        moduleLoad('banner');
        return sprintf(
            '%s/%s',
            bannerGetOptionList('hover_path'),
            $this->__get('hover_image')
        );
    }

    /**
     * @return TwigImage
     */
    public function mobile_image()
    {
        return is_file($this->mobile_file()) ? new TwigImage([
            'src' => sprintf(
                '/lp/%s-m%d.jpg',
                urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $this->title()), '-')),
                $this->id
            ),
            'alt' => $this->alt(),
        ]) : null;
    }

    /**
     * @return string
     */
    public function mobile_file()
    {
        moduleLoad('banner');
        return sprintf(
            '%s/%s',
            bannerGetOptionList('mobile_path'),
            $this->__get('mobile_image')
        );
    }

    /**
     * @return string
     */
    public function offer()
    {
        return $this->__get('offer');
    }

    /**
     * @return string
     */
    public function title()
    {
        $title = $this->__get('title');
        if (!empty($title)) {
            return $title;
        }
        $title = $this->__get('text');
        if (!empty($title)) {
            return $title;
        }

        return $this->__get('name');
    }

    /**
     * @return string|null
     */
    public function url()
    {
        if ('' == $this->__get('url')) {
            return null;
        }

        return sprintf(
            '/lp/%s-%d',
            urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $this->title()), '-')),
            $this->id
        );
    }

    /**
     * @return string
     */
    public function redirect_url()
    {
        return $this->__get('url');
    }

    /**
     * @return string
     */
    public function alt()
    {
        return $this->__get('alt');
    }

    public function black_shading(){
        return $this->__get('black_shading');
    }

    public function image_movement(){
        return $this->__get('image_movement');
    }
}
