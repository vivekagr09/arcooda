<?php
namespace app\models;

class TwigDate
{
    private $_timestamp = null;
    private $_string    = null;

    public function __construct($timestamp = null)
    {
        if (!is_null($timestamp)) {
            $this->_timestamp    = $timestamp;
        }
    }

    public function __tostring()
    {
        //Return cached result
        if (!is_null($this->_string)) {
            return $this->_string;
        }

        //Return formatted USD result
        $this->_string = date(_('jS F, Y'), $this->_timestamp ?? time());
        return $this->_string;
    }
}
