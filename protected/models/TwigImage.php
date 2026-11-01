<?php
namespace app\models;

class TwigImage
{

    /**
     * @var string[] $_attributes
     */
    private $_attributes = [];

    /**
     * @param string[]|string $url
     */
    public function __construct($attr)
    {
        if (is_string($attr)) {
            $this->_attributes = ['src' => $attr];
        } elseif (is_array($attr)) {
            $this->_attributes = $attr;
        }
    }

    /**
     * @return string
     */
    public function __tostring()
    {
        return $this->_attributes['src'] ?? '';
    }

    /**
     * @param string $k
     *
     * @return string|null
     */
    public function __get($k)
    {
        if (array_key_exists($k, $this->_attributes)) {
            return $this->_attributes[$k];
        }
        return null;
    }

    /**
     * @return string
     */
    public function alt()
    {
        return $this->_attributes['alt'] ?? '';
    }

    /**
     * @param string[] $attr
     *
     * @return string
     */
    public function html($attr = [])
    {
        $result = [];

        $valid_attributes  = ['alt', 'crossorigin', 'height', 'ismap', 'loading', 'longdesc', 'referrerpolicy', 'sizes', 'src', 'srcset', 'usemap', 'width'];
        $global_attributes = ['accesskey', 'class', 'contenteditable', 'dir', 'draggable', 'hidden', 'id', 'lang', 'spellcheck', 'style', 'tabindex', 'title', 'translate'];

        foreach ($this->_attributes + $attr as $k => $v) {
            if (('data-' === substr($k, 0, 5) || in_array($k, $valid_attributes) || in_array($k, $global_attributes)) && !empty($v)) {
                $result[$k] = sprintf(
                    '%s="%s"',
                    $k,
                    htmlspecialchars($v)
                );
            }
        }
        return sprintf('<img %s  loading="lazy"/>', implode(' ', $result));
    }
}
