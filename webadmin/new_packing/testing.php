<?php
function perms($n,$r)
{
	if($r>$n) return 0;
	$res=1;
	for($i=0;$i<$r;$i++)
		$res*=$n--;
	return $res;
}

$storage=array(
	'[person]'=>array(
		'I','you','he','she'
	),
	'[verb]'=>array(
		'kicked','punched','pushed','ate','poked'
	),
	'[object]'=>array(
		'car','dog','ball','fart'
	),
	'[owner]'=>array(
		'my','your','his','her','the','their','a'
	)
);

$layout=array(
	'[person]','[verb]','[owner]','[object]','and','[owner]','mum'
);

$permutations=1;
foreach($storage as $key=>$values)
	$permutations*=perms(count($values),count(array_keys($layout,$key)));

echo $permutations.' permutations:<br /><br />';

for($i=0;$i<$permutations;$i++)
{
	$j=$i;
	$result=array();
	$avail=array();
	foreach($storage as $key=>$value)
		$avail[$key]=array_keys($value);

	foreach($layout as $key)
	{
		$modulo=count($avail[$key]);
		if($modulo>0)
		{
			$result[]=$storage[$key][$avail[$key][$j%$modulo]];
			unset($avail[$key][$j%$modulo]);
			$avail[$key]=array_values($avail[$key]);
			if($j>0) $j/=$modulo;
		}
		else
			$result[]=$key;
	}

	echo ucfirst(implode(' ',$result))."<br />\n";
}
?>