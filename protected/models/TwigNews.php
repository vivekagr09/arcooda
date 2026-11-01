<?php
namespace app\models;

use News_Handler;
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

class TwigNews
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
    private $news = null;

    /**
     * @param string $field
     *
     * @return mixed
     */
    function __get($field)
    {
        if (is_null($this->news)) {
            moduleLoad('news'); //newsFromDatabase

            if (class_exists('News_Handler')) {
                $NH         = new News_Handler();
                $cacheService = new CacheService();
                $this->news = $cacheService->getData('news', $this->id);
                if($this->news == false){   
                    $this->news = $NH->fromDatabase($this->id);
                    $cacheService->updateData('news', $this->id,$this->news);
                }
                return $this->news[$field] ?? null;
            }

            if (function_exists('newsFromDatabase')) {
                $this->news = newsFromDatabase($this->id);
                return $this->news[$field] ?? null;
            }
        }

        return $this->news[$field] ?? null;
    }

    /**
     * @return string
     */
    function __tostring()
    {
        return $this->name();
    }

    /**
     * @param int[] $list
     *
     * @return TwigNews[]
     */
    public static function list($list)
    {
        foreach ($list as $k => $v) {
            $list[$k] = new TwigNews($v);
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

        if (array_key_exists('news', $_GLOBALS['criteria_options'])) {
            $criteria = array_merge(
                $_GLOBALS['criteria_options']['news'],
                $criteria
            );
        }
        moduleLoad('news'); //newsSearch

        if (array_key_exists('id', $criteria)) {
            $criteria['news_id'] = $criteria['id'];
        }

        if (class_exists('News_Handler')) {
            $NH = new News_Handler();
            return self::list($NH->search($criteria));
        }

        if (function_exists('newsSearch')) {
            return self::list(newsSearch($criteria));
        }

        return array();
    }

    /**
     * @return string
     */
    public function alt()
    {
        return $this->__get('caption') ?? '';
    }

    /**
     * @return TwigImage|null
     */
    public function image()
    {
        moduleLoad('news'); //newsImagesFromDatabase

        if (is_file($this->image_file())) {
            return new TwigImage([
                'src' => sprintf(
                    '/n/%s-%d.jpg',
                    urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $this->name()), '-')),
                    $this->id
                ),
                'alt' => $this->alt(),
            ]);
        }

        if (function_exists('newsImagesFromDatabase')) {
            foreach (newsImagesFromDatabase($this->id) as $v) {
                if ('y' == $v['default_image'] && 'y' == $v['online']) {
                    return new TwigImage([
                        'src' => $v['src'],
                        'alt' => $v['caption'],
                    ]);
                }
            }
        }

        return null;
    }

    /**
     * @return TwigImage|null
     */
    public function thumbnail()
    {
        moduleLoad('news'); //newsImagesFromDatabase

        if (is_file($this->thumbnail_file())) {
            return new TwigImage([
                'src' => sprintf(
                    '/n/%s-%d_sml.jpg',
                    urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $this->name()), '-')),
                    $this->id
                ),
                'alt' => $this->alt(),
            ]);
        }

        if (function_exists('newsImagesFromDatabase')) {
            foreach (newsImagesFromDatabase($this->id) as $v) {
                if ('y' == $v['default_image']) {
                    return new TwigImage([
                        'src' => $v['src'],
                        'alt' => $v['caption'],
                    ]);
                }
            }
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function name()
    {
        return $this->__get('news_title');
    }

    /**
     * @return string|null
     */
    public function url()
    {
        return sprintf(
            '/n/%s-%d.html',
            urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $this->news_title), '-')),
            $this->id
        );
    }

    /**
     * @return string|null
     */
    public function image_file()
    {
        moduleLoad('news'); //newsGetOptionList

        if (class_exists('News_Handler')) {
            $NH   = new News_Handler();
            $test = $NH->getOptionList('image_path') . '/' . $this->__get('image');
            if (is_file($test)) {
                return $test;
            }
        }

        if (function_exists('newsGetOptionList')) {
            $test = newsGetOptionList('image_path') . '/' . $this->__get('image');
            if (is_file($test)) {
                return $test;
            }
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function thumbnail_file()
    {
        moduleLoad('news');

        if (class_exists('News_Handler')) {
            $NH   = new News_Handler();
            $test = $NH->getOptionList('thumbnail_path') . '/' . $this->__get('thumbnail');
            if (is_file($test)) {
                return $test;
            }
        }

        if (function_exists('newsGetOptionList')) {
            $test = newsGetOptionList('thumbnail_path') . '/' . $this->__get('thumbnail');
            if (is_file($test)) {
                return $test;
            }
        }

        return null;
    }



    public static function totalnews($criteria = [])
    {
        global $_GLOBALS;
        if (array_key_exists('news', $_GLOBALS['criteria_options'])) {
            $criteria = array_merge(
                $_GLOBALS['criteria_options']['news'],
                $criteria
            );
        }

        //TODO: Can't search by ext_fields, or id ext_field doesn't exists yet?
        if (array_key_exists('id', $criteria)) $criteria['news_id'] = $criteria['id'];

        moduleLoad('news'); //newsCount

        if (class_exists('News_Handler')) {
            $NH = new News_Handler();
            //@fixme WTF? Why was this attached to the generic class?
            return $NH->totalnews($criteria);
        }

        /*
        if (function_exists('newsCount')) {
            return ['total' => newsCount($criteria)];
        }
        */

        return [];
    }
    public function title()
    {
        $title = $this->meta_title;
        if (empty($title)) $title = $this->name();
        return trim($title);
    }
    public function description()
    {
        $desc = $this->meta_desc;
        if (empty($desc)) $desc = $this->short_description;
        if (empty($desc)) $desc = explode("\n", $this->long_description)[0];
        return trim($desc);
    }
    public function keywords()
    {
        $keys = $this->meta_keys;
        if (empty($keys)) {
            $keys = ucwords(implode(', ', preg_split('/[,\-\s]+/', $this->short_description)));
        }
        return $keys;
    }
    public function breadcrumbs()
    {
        return [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'Latest News', 'url' => '/n'],
            ['title' => $this->name(), 'url' => $this->url()],
        ];
    }


    public function date()
    {
        return date(_('Y-m-d'), $this->time_added);
    }
    public function schedule_date()
    {
        return date(_('Y-m-d'), $this->date_schedule);
    }
    public function date_long()
    {
        return date(_('jS F, Y'), $this->time_added);
    }
    public function excerpt()
    {
        return $this->short_description;
    }
    public function getAllImages()
    {
        moduleLoad('news'); //newsImagesFromDatabase

        if (class_exists('News_Handler')) {
            $NH = new News_Handler();
            return $NH->getAllImages($this->news_id,$this->news_title);
        }

        /*
        if (function_exists('newsImagesFromDatabase')) {
            $videos = [];
            foreach (newsImagesFromDatabase($this->news_id) as $k => $v) {
                if ('y' != $v['default_image']) {
                    $videos[$k] = $v;
                }
            }
            return $videos;
        }
        */

        return [];
    }
    public function getAllVideos()
    {
        moduleLoad('news'); //newsVideosFromDatabase

        if (class_exists('News_Handler')) {
            $NH = new News_Handler();
            return $NH->getAllVideos($this->news_id);
        }

        /*
        if (function_exists('newsVideosFromDatabase')) {
            $videos = [];
            foreach (newsVideosFromDatabase($this->news_id) as $k => $v) {
                if ('n' != $v['active']) {
                    $videos[$k] = $v;
                }
            }
            return $videos;
        }
        */

        return [];
    }
    public function getvideoid($link)
    {
        //Taken from https://github.com/ytdl-org/youtube-dl/blob/master/youtube_dl/extractor/youtube.py#L522
        $url_regexp = "@^
            (
                (?:https?://|//)                                    # http(s):// or protocol-independent URL
                (?:(?:(?:(?:\w+\.)?[yY][oO][uU][tT][uU][bB][eE](?:-nocookie|kids)?\.com|
                (?:www\.)?deturl\.com/www\.youtube\.com|
                (?:www\.)?pwnyoutube\.com|
                (?:www\.)?hooktube\.com|
                (?:www\.)?yourepeat\.com|
                tube\.majestyc\.net|
                %(invidious)s|
                youtube\.googleapis\.com)/                        # the various hostnames, with wildcard subdomains
                (?:.*?\#/)?                                          # handle anchor (#/) redirect urls
                (?:                                                  # the various things that can precede the ID:
                    (?:(?:v|embed|e)/(?!videoseries))                # v/ or embed/ or e/
                    |shorts/
                    |(?:                                             # or the v= param in all its forms
                        (?:(?:watch|movie)(?:_popup)?(?:\.php)?/?)?  # preceding watch(_popup|.php) or nothing (like /?v=xxxx)
                        (?:\?|\#!?)                                  # the params delimiter ? or # or #!
                        (?:.*?[&;])??                                # any other preceding param (like /?s=tuff&v=xxxx or ?s=tuff&amp;v=V36LpHqtcDY)
                        v=
                    )
                ))
                |(?:
                youtu\.be|                                        # just youtu.be/xxxx
                vid\.plus|                                        # or vid.plus/xxxx
                zwearz\.com/watch|                                # or zwearz.com/watch/xxxx
                %(invidious)s
                )/
                |(?:www\.)?cleanvideosearch\.com/media/action/yt/watch\?videoId=
                )
            )?                                                       # all until now is optional -> you can pass the naked ID
            (?P<id>[0-9A-Za-z_-]{11})                                # here is it! the YouTube video ID
            (?(1).+)?                                                # if we found the ID, everything can follow
        $@x";

        if (preg_match($url_regexp, $link, $m)) {
            return $m['id'];
        }

        $video_id = $link;

        $temp = explode('#', $video_id);
        if (count($temp)) {
            $video_id = $temp[0];
        }
        $temp = explode("?v=", $video_id); 
        if (count($temp) > 1) {
            $video_id = $temp[1];
        }
        $temp = explode("/v/", $video_id); 
        if (count($temp) > 1) {
            $video_id = $temp[1];
        }
        $temp = explode("&", $video_id); // Deleting any other params
        if (count($temp) > 1) {
            $video_id = $temp[0];
        }

        return $video_id;
    }
    public function text()
    {
        if (empty($this->long_description)) {
            $this->long_description = preg_replace("/\r\n|\r|\n/", '<br/>', $this->long_description);
        }

        return $this->long_description;
    }


    private $_categories = null;
    public function categories()
    {
        /*if (is_null($this->_categories)) {
            $this->_categories = ['News'];
            //if ('n' === $this->archived) {
            //    $this->_categories[] = 'Latest News';
            //}

        }*/

		if ($this->category != '') {
			$this->_categories = [$this->category];
		} else {
			$this->_categories = ['Latest News'];
		}

		return $this->_categories;
    }


    public function previous()
    {
        foreach (self::search() as $item) {
            if ($item->id < $this->id) {
                return $item;
            }
        }
        return null;
    }
    public function next()
    {
        $next = null;
        foreach (self::search() as $item) {
            if ($item->id > $this->id) {
                $next = $item;
            } else {
                break;
            }
        }
        return $next;
    }
}
