<?php
include_once('class.crate.php');

//Container Class, to simulate an empty regular right angle container
class Container
{
	//Serialize vars
	var $a,$b,$c;

	//Private + keep on serialize
	var $width=0,$depth=0,$height=0;
	var $crate_list=array();
	var $name;

	//Private + lose on serialize
	var $standard_id;
	var $weight=0,$max_weight=0;
	var $hot_spots=array();
	var $maximal=array('x'=>0,'y'=>0,'z'=>0);
	var $max_angle=1;
	var $stackable=false;

	function Container($newWidth=0,$newDepth=0,$newHeight=0,$newWeight=0,$newName='')
	{
		$this->setVars($newWidth,$newDepth,$newHeight,$newWeight,$newName);
		$this->addHotSpot(0,0,0);
	}

	function setVars($newWidth=-1,$newDepth=-1,$newHeight=-1,$newWeight=-1,$newName=-1)
	{
		$this->setWidth($newWidth);
		$this->setDepth($newDepth);
		$this->setHeight($newHeight);
		$this->setMaxWeight($newWeight);
		if($newName!==-1) $this->setName($newName);
	}

	function getHotSpotCount()
	{
		return count($this->hot_spots);
	}
	function getHotSpotID($hot_spot=0)
	{
		$keys=array_keys($this->hot_spots);
		return $keys[$hot_spot];
	}
	function addHotSpot($x,$y,$z)
	{
		$this->hot_spots[]=array('x'=>$x,'y'=>$y,'z'=>$z);
		$this->sortHotSpots();
	}
	 function sortHotSpots()
    {
        usort($this->hot_spots, function ($a, $b) {
            if ($a['x'] != $b['x']) return $a['x'] - $b['x']; // Prioritize front-first
            if ($a['z'] != $b['z']) return $a['z'] - $b['z']; // Then depth
            return $a['y'] - $b['y']; // Finally height
        });
    }
	function delHotSpot($hot_spot=0)
	{
		if($this->getHotSpotCount()==1)
			$this->hot_spots=array();
		else
			unset($this->hot_spots[$this->getHotSpotID($hot_spot)]);
	}
	function getHotSpot($hot_spot=0)
	{
		return $this->hot_spots[$this->getHotSpotID($hot_spot)];
	}

	function checkCollision($newCrate,$hot_spot=0)
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

	function findPossible($newCrate,$first_only=false)
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

	function addCrate($newCrate, $hot_spot = 0)
    {
        $spot = $this->hot_spots[$hot_spot];
        $x = $spot['x'];
        $y = $spot['y'];
        $z = $spot['z'];
        
        $newCrate->setPosition($x, $y, $z);
        $this->crate_list[] = $newCrate;

        unset($this->hot_spots[$hot_spot]);
        $this->hot_spots = array_values($this->hot_spots); // Reindex
        
        if ($this->stackable) {
            $this->addHotSpot($x + $newCrate->getWidth(), $y, $z);
            $this->addHotSpot($x, $y, $z + $newCrate->getDepth());
            $this->addHotSpot($x, $y + $newCrate->getHeight(), $z);
        } else {
            $this->addHotSpot($x + $newCrate->getWidth(), 0, $z);
            $this->addHotSpot($x, 0, $z + $newCrate->getDepth());
        }
        
        $this->maximal['x'] = max($this->maximal['x'], $x + $newCrate->getWidth());
        $this->maximal['y'] = max($this->maximal['y'], $y + $newCrate->getHeight());
        $this->maximal['z'] = max($this->maximal['z'], $z + $newCrate->getDepth());
    }

/*
	function __sleep()
	{
		$a=array();
		foreach(array('width','depth','height') as $key)
			$a[]=intval($this->$key);
		$this->a=implode(':',$a);
		$this->b=&$this->crate_list;
		$this->c=&$this->name;
		return array('a','b','c');
	}

	function __wakeup()
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

	function setWidth($newWidth=-1)
	{
		if($newWidth>=0) $this->width=$newWidth;
	}
	function getWidth()
	{
		return $this->width;
	}
	function setHeight($newHeight=-1)
	{
		if($newHeight>=0) $this->height=$newHeight;
	}
	function getHeight()
	{
		return $this->height;
	}
	function setDepth($newDepth=-1)
	{
		if($newDepth>=0) $this->depth=$newDepth;
	}
	function getDepth()
	{
		return $this->depth;
	}
	function setMaxWeight($newWeight=-1)
	{
		if($newWeight>=0) $this->max_weight=$newWeight;
	}
	function getMaxWeight($newWeight=-1)
	{
		return $this->max_weight;
	}
	function getWeight()
	{
		return $this->weight;
	}
	function setName($newName)
	{
		$this->name=$newName;
	}
	function getName()
	{
		return $this->name;
	}
	function getVolume()
	{
		if($this->height==0)
			return ($this->width-$this->maximal['y'])*($this->depth-$this->maximal['y'])*$this->maximal['y'];
		else
			return $this->width*$this->depth*$this->height;
	}
}
?>