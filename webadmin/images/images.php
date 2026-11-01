<?php
$dir_list=array();
$file_list=array();

if($dh=opendir('./'))
{
	while(($file=readdir($dh))!==false)
	{
		if(is_dir($file))
			$dir_list[]=$file;
		else
			$file_list[]=$file;
	}
	closedir($dh);
}

?>
<p>
<?php
foreach($dir_list as $dirname)
{
?>
<a href="<?php echo htmlentities($dirname) ?>"><?php echo htmlentities($dirname) ?></a><br />
<?php
}
?>
</p>

<p>
<?php
foreach($file_list as $filename)
{
?>
<img src="<?php echo htmlentities($filename) ?>" /> <a href="<?php echo htmlentities($filename) ?>"><?php echo htmlentities($filename) ?></a><br />
<?php
}
?>
</p>
