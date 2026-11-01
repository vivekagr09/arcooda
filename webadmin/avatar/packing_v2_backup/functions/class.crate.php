<?php
//Crate Class, to simulate a regular right angle crate

class Crate
{
	//Serialize vars
	var $a;

	//Private + keep on serialize
	var $x=0,$y=0,$z=0;
	var $width=0,$depth=0,$height=0;
	var $angle=0,$weight=0;
	var $product_id=0;
	var $site_id=0;

	//Private + lose on serialize
	var $name='';

	function Crate($newWidth=0,$newDepth=0,$newHeight=0,$newWeight=0,$newName='')
	{
		$this->setVars($newWidth,$newDepth,$newHeight,$newWeight,$newName);
	}

	function setVars($newWidth=-1,$newDepth=-1,$newHeight=-1,$newWeight=-1,$newName=-1)
	{
		$this->setWidth($newWidth);
		$this->setDepth($newDepth);
		$this->setHeight($newHeight);
		$this->setWeight($newWeight);
		if($newName!==-1) $this->setName($newName);
	}

	function setAngle($newAngle=0)
	{
		$this->angle=$newAngle%6;
	}
	function getAngle()
	{
		return $this->angle;
	}
	function setWidth($newWidth=-1)
	{
		if($newWidth>=0) $this->width=$newWidth;
	}
	function getWidth()
	{
		switch($this->angle)
		{
		case 1:
		case 4: return $this->depth;
		case 3:
		case 5: return $this->height;
		}
		return $this->width;
	}
	function setHeight($newHeight=-1)
	{
		if($newHeight>=0) $this->height=$newHeight;
	}
	function getHeight()
	{
		switch($this->angle)
		{
		case 2:
		case 3: return $this->depth;
		case 4:
		case 5: return $this->width;
		}
		return $this->height;
	}
	 public function setDepth($newDepth = -1)
    {
        if ($newDepth >= 0) {
            $this->depth = $newDepth;
        }

    }
    public function getDepth()
    {
        switch ($this->angle) {
            case 1:
            case 3:return $this->width;
            case 2:
            case 4:return $this->height;
        }
        return $this->depth;
    }
	public function setWeight($newWeight = -1)
    {
        if ($newWeight >= 0) {
            $this->weight = $newWeight;
        }

    }
    public function getWeight()
    {
        return $this->weight;
    }
    public function setName($newName)
    {
        $this->name = $newName;
    }
    public function getName()
    {
        return $this->name;
    }
	public function setProductID($product_id, $site_id = '')
    {
        $this->product_id = $product_id;
        if ('' != $site_id) {
            $this->setSiteID($site_id);
        }

    }
    public function setSiteID($site_id)
    {
        $this->site_id = $site_id;
    }
    public function getProductID()
    {
        return $this->product_id;
    }
    public function getSiteID()
    {
        return $this->site_id;
    }
    public function getX()
    {
        return $this->x;
    }
    public function getY()
    {
        return $this->y;
    }
    public function getZ()
    {
        return $this->z;
    }
	 public function getVolume()
    {
        return $this->width * $this->depth * $this->height;
    }

    public function rotateBestFit($width = 0)
    {
        if (0 != $this->width && ($width % $this->width) < ($width % $this->depth)) {
            $temp        = $this->width;
            $this->width = $this->depth;
            $this->depth = $temp;
        }
    }

	public function setPosition($newX, $newY, $newZ)
    {
        $this->x = $newX;
        $this->y = $newY;
        $this->z = $newZ;
    }

    public function fixObjectPivot()
    {
        $w            = $this->getWidth();
        $h            = $this->getHeight();
        $d            = $this->getDepth();
        $this->width  = $w;
        $this->height = $h;
        $this->depth  = $d;
        $this->angle  = 0;
    }

	public function __sleep()
    {
        $this->fixObjectPivot();
        return array_keys(get_object_vars($this));
    }


/*
	function __sleep()
	{
		$a=array();
		foreach(array('x','y','z','width','depth','height','angle','weight','product_id') as $key)
			$a[]=intval($this->$key);
		$this->a=implode(':',$a);
//		$this->b=&$this->name;
		return array('a');
	}

	function __wakeup()
	{
		$a=explode(':',$this->a);
		foreach(array('x','y','z','width','depth','height','angle','weight','product_id') as $key1=>$key2)
			$this->$key2=$a[$key1];
//		$this->name=$this->b;
		unset($this->a);
//		unset($this->b);
	}
*/
}
?>