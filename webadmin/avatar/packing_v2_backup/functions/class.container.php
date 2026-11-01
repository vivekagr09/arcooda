<?php
include_once('class.crate.php');

//Container Class, to simulate an empty regular right angle container
class Container
{
	//Serialize vars
	public $a,$b,$c;

	//Private + keep on serialize
	public $width=0,$depth=0,$height=0;
	public $crate_list=array();
	public $name;

	//Private + lose on serialize
	public $standard_id;
	public $weight=0,$max_weight=0;
	public $hot_spots=array();
	public $maximal=array('x'=>0,'y'=>0,'z'=>0);
	public $max_angle=1;
	public $stackable=false;

	public  function Container($newWidth=0,$newDepth=0,$newHeight=0,$newWeight=0,$newName='')
	{
		$this->setVars($newWidth,$newDepth,$newHeight,$newWeight,$newName);
		$this->addHotSpot(0,0,0);
	}

	public function setVars($newWidth=-1,$newDepth=-1,$newHeight=-1,$newWeight=-1,$newName=-1)
	{
		$this->setWidth($newWidth);
		$this->setDepth($newDepth);
		$this->setHeight($newHeight);
		$this->setMaxWeight($newWeight);
		if($newName!==-1) $this->setName($newName);
	}

	public function getHotSpotCount()
	{
		return count($this->hot_spots);
	}
	public function getHotSpotID($hot_spot=0)
	{
		$keys=array_keys($this->hot_spots);
		return $keys[$hot_spot];
	}
	public function addHotSpot($x,$y,$z)
	{
		$this->hot_spots[]=array('x'=>$x,'y'=>$y,'z'=>$z);
	}
	public function delHotSpot($hot_spot=0)
	{
		if($this->getHotSpotCount()==1)
			$this->hot_spots=array();
		else
			unset($this->hot_spots[$this->getHotSpotID($hot_spot)]);
	}
	public function getHotSpot($hot_spot=0)
	{
		return $this->hot_spots[$this->getHotSpotID($hot_spot)];
	}

	public function checkCollision($newCrate,$hot_spot=0)
	{
		$spot=$this->getHotSpot($hot_spot);
		$x=$spot['x'];
		$y=$spot['y'];
		$z=$spot['z'];

		//Make sure fits within weight restrictions
		if($this->max_weight>0 && ($this->weight+$newCrate->getWeight())>$this->max_weight) return true;

		//See if will fit inside container
		if($this->height==0)
		{
			$tester=$this->maximal;
			$tester['x']=max($tester['x'],$newCrate->getWidth()+$x);
			$tester['y']=max($tester['y'],$newCrate->getHeight()+$y);
			$tester['z']=max($tester['z'],$newCrate->getDepth()+$z);
			if($this->width<($tester['x']+$tester['y'])) return true;
			if($this->depth<($tester['z']+$tester['y'])) return true;
		}
		else
		{
			if(($x+$newCrate->getWidth())>$this->width) return true;
			if(($y+$newCrate->getHeight())>$this->height) return true;
			if(($z+$newCrate->getDepth())>$this->depth) return true;
		}

		//See if colliding with other crates
		foreach($this->crate_list as $testCrate)
			if($x<($testCrate->getX()+$testCrate->getWidth()) && ($x+$newCrate->getWidth())>$testCrate->getX())
				if($z<($testCrate->getZ()+$testCrate->getDepth()) && ($z+$newCrate->getDepth())>$testCrate->getZ())
					if($y<($testCrate->getY()+$testCrate->getHeight()) && ($y+$newCrate->getHeight())>$testCrate->getY())
						return true;

		//See that it is inside the container
		if($x<0 || $y<0 || $z<0) return true;

		//No collisions found
		return false;
	}

	public function findPossible($newCrate,$first_only=false)
	{
		global $config;

		$hot_spot_count=$this->getHotSpotCount();

		$possible=array();
		for($angle=0;$angle<=$this->max_angle;$angle++)
//		for($angle=$this->max_angle;$angle>=0;$angle--)
		{
			$newCrate->setAngle($angle);
			for($spot=0;$spot<$hot_spot_count;$spot++)
			{
				if(!$this->checkCollision($newCrate,$spot))
				{
					$possible[]=array('angle'=>$angle,'hot_spot'=>$spot);
					if($first_only) break(2);
				}
			}
		}
		return $possible;
	}

	public function addCrate($newCrate,$hot_spot=0)
	{
		global $config;

		//Add the crate to the list
		$spot=$this->getHotSpot($hot_spot);
		$x=$spot['x'];
		$y=$spot['y'];
		$z=$spot['z'];
		$newCrate->setPosition($x,$y,$z);

		$this->crate_list[]=$newCrate;

		//Increase the weight
		$this->weight+=$newCrate->getWeight();

		//Remove the hot spot from the list
		$this->delHotSpot($hot_spot);

		//Add some new hot spots
		if($this->stackable)
		{
			$this->addHotSpot($newCrate->getX()+$newCrate->getWidth(),0,$newCrate->getZ());
			$this->addHotSpot($newCrate->getX(),0,$newCrate->getZ()+$newCrate->getDepth());
			$this->addHotSpot($newCrate->getX()+$newCrate->getWidth(),$newCrate->getY(),$newCrate->getZ());
			$this->addHotSpot($newCrate->getX(),$newCrate->getY(),$newCrate->getZ()+$newCrate->getDepth());
			$this->addHotSpot($newCrate->getX(),$newCrate->getY()+$newCrate->getHeight(),$newCrate->getZ());
		}
		else
		{
			$this->addHotSpot($newCrate->getX()+$newCrate->getWidth(),0,$newCrate->getZ());
			$this->addHotSpot($newCrate->getX(),0,$newCrate->getZ()+$newCrate->getDepth());
		}

		//Recalculate the maximal point
		$this->maximal['x']=max($this->maximal['x'],$newCrate->getWidth()+$newCrate->getX());
		$this->maximal['y']=max($this->maximal['y'],$newCrate->getHeight()+$newCrate->getY());
		$this->maximal['z']=max($this->maximal['z'],$newCrate->getDepth()+$newCrate->getZ());

		//Experimental - Sort Hot Spots zxy
		$sort_array=array();
		foreach($this->hot_spots as $spot)
			$sort_array[]=$spot['z'];
		array_multisort($sort_array,$this->hot_spots);

		$sort_array=array();
		foreach($this->hot_spots as $spot)
			$sort_array[]=$spot['x'];
		array_multisort($sort_array,$this->hot_spots);

		$sort_array=array();
		foreach($this->hot_spots as $spot)
			$sort_array[]=$spot['y'];
		array_multisort($sort_array,$this->hot_spots);
	}

/*
	public function __sleep()
	{
		$a=array();
		foreach(array('width','depth','height') as $key)
			$a[]=intval($this->$key);
		$this->a=implode(':',$a);
		$this->b=&$this->crate_list;
		$this->c=&$this->name;
		return array('a','b','c');
	}

	public function __wakeup()
	{
		$a=explode(':',$this->a);
		foreach(array('width','depth','height') as $key1=>$key2)
			$this->$key2=$a[$key1];
		$this->crate_list=$this->b;
		$this->name=$this->c;
		unset($this->a);
		unset($this->b);
		unset($this->c);
	}
*/

	public function setWidth($newWidth=-1)
	{
		if($newWidth>=0) $this->width=$newWidth;
	}
	public function getWidth()
	{
		return $this->width;
	}
	public function setHeight($newHeight=-1)
	{
		if($newHeight>=0) $this->height=$newHeight;
	}
	public function getHeight()
	{
		return $this->height;
	}
	public function setDepth($newDepth=-1)
	{
		if($newDepth>=0) $this->depth=$newDepth;
	}
	public function getDepth()
	{
		return $this->depth;
	}
	public function setMaxWeight($newWeight=-1)
	{
		if($newWeight>=0) $this->max_weight=$newWeight;
	}
	public function getWeight()
	{
		return $this->weight;
	}
	public function setName($newName)
	{
		$this->name=$newName;
	}
	public function getName()
	{
		return $this->name;
	}
	public function getVolume()
	{
		if($this->height==0)
			return ($this->width-$this->maximal['y'])*($this->depth-$this->maximal['y'])*$this->maximal['y'];
		else
			return $this->width*$this->depth*$this->height;
	}
}
?>