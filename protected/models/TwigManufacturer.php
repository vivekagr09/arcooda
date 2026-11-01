<?php
namespace app\models;

use app\models\TwigCategory;
use app\models\TwigImage;

$_GLOBALS['criteria_options']['manufacturer'] = ['show_front' => 'y'];

moduleLoad('manufacturer');
if (!function_exists('manufacturerSearch')) {
    /**
     * @param mixed[] $criteria
     *
     * @return int[]
     */
    function manufacturerSearch($criteria = [])
    {
        global $DB;

        $whereAND = [];
        foreach ($criteria as $k => $v) {
            $whereAND[] = sprintf(
                "`%s` IN('%s')",
                $k,
                is_array($v) ? implode("', '", $v) : $v
            );
        }

        $result = [];
        if ($query = $DB->query(sprintf(
            "SELECT `id` FROM `manufacturers`%s ORDER BY `manufacturer` ASC",
            (count($whereAND)) ? (" WHERE " . implode(" AND ", $whereAND)) : ""
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $result[] = $row['id'];
            }
            $DB->free_result($query);
        }

        return $result;
    }
}

class TwigManufacturer
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
     * Path to the img files, including trailing slash
     *
     * @return string
     */
    public static function img_path()
    {
        return $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/manufacturer/image/';
    }


    /**
     * @var mixed[]|null $_manufacturer
     */
    private $_manufacturer;

    /**
     * @param string $field
     *
     * @return mixed
     */
    public function __get($field)
    {
        if (!is_null($this->_manufacturer)) {
            return $this->_manufacturer[$field] ?? null;
        }

        global $DB;
        if ($this->_manufacturer = $DB->query_first(sprintf(
            "SELECT * FROM `manufacturers` WHERE `id` = %d LIMIT 1",
            $this->id
        ), MYSQLI_ASSOC)) {

            //TODO: Remove this when admin is saving properly
            $this->_manufacturer['img'] = basename($this->_manufacturer['img']);

            if (!is_file(self::img_path() . $this->_manufacturer['img'])) {
                $this->_manufacturer['img'] = '';
            }

            return $this->_manufacturer[$field] ?? null;
        }

        return null;
    }


    /**
     * @return string
     */
    public function __tostring()
    {
        return '[Manufacturer id:' . $this->id . ']';
    }

    /**
     * @param int[] $list
     *
     * @return TwigManufacturer[]
     */
    public static function list($list) {
        $result = [];
        foreach ($list as $k => $v) {
            $result[$k] = new TwigManufacturer($v);
        }

        return $result;
    }

    /**
     * @param mixed[] $criteria
     *
     * @return TwigManufacturer[]
     */
    public static function search($criteria = [])
    {
        global $_GLOBALS, $DB;
        if (array_key_exists('manufacturer', $_GLOBALS['criteria_options'])) {
            $criteria = array_merge(
                $_GLOBALS['criteria_options']['manufacturer'],
                $criteria
            );
        }
        moduleLoad('manufacturer');
        return self::list(manufacturerSearch($criteria));
    }


    /**
     * @return string
     */
    public function name()
    {
        return $this->__get('manufacturer');
    }

    /**
     * @return ?string The relative url of the image
     */
    public function image()
    {
        if (!is_file(self::img_path() . $this->__get('img'))) {
            return null;
        }

        return new TwigImage([
            'src'      => sprintf(
                '/manufacturer/%s-%d.jpg',
                urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $this->__get('manufacturer')), '-')),
                $this->id
            ),
            'alt'      => 'Logo for ' . $this->name(),
            'src_file' => self::img_path() . $this->__get('img'),
        ]);
    }

    /**
     * @var string|null $_url
     */
    private $_url;

    /**
     * @return string
     */
    public function url()
    {
        if (!is_null($this->_url)) {
            return $this->_url;
        }

        return $this->_url = sprintf(
            '%s?m=%d',
            (new TwigCategory(2))->url(),
            $this->id
        );
    }
}
