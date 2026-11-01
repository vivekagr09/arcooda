<?php
moduleLoad('asset'); //assetFromDatabase, assetToCompatible

foreach($layout as $chapter_id=>$page_list)
{
	$chapter=workmanualChapterFromDatabase($chapter_id);
?>
	<h1><?= htmlspecialchars($chapter['chapter_title']) ?></h1>
<?php
	foreach($page_list as $page_id)
	{
		$page=workmanualPageFromDatabase($page_id);
		$output=$page['page_content'];

		switch($page['page_type'])
		{
		case 'image':
			$asset=assetFromDatabase('workmanual_pages',$page_id);
			$table_info=assetTableInfoFromType('workmanual_pages');
			$from_file=$table_info['directory'].$asset['filename'];
			$landscape=false;
			if(is_file($from_file) && filesize($from_file)>0)
			{
				$dim=getimagesize($from_file);
				if($dim[0]>640 && $dim[0]>$dim[1]) $landscape=true;
			}

			$output='<div style="text-align:center;">'.
					'<img src="http://'.$_SERVER['HTTP_HOST'].'/webadmin/assets/index.php'.
					'?asset_type=workmanual_pages'.
					'&action=view'.
					'&asset_id='.$page_id.
					'"'.(($landscape)?' style="filter:progid:DXImageTransform.Microsoft.BasicImage(rotation=3);"':'').
					' />'.
					'<h5>'.htmlspecialchars($asset['asset_caption']).'</h5>'.
					'</div>';
			break;
		case 'plain':
			$output=nl2br(htmlspecialchars($output));
			break;
		case 'semihtml':
			$output=explode("\n",$output);
			foreach($output as $key=>$row)
				if(substr($row,0,2)=='- ')
					$output[$key]='<li>'.trim(substr($row,1)).'</li>';
			$output=nl2br(implode("\n",$output));
			break;
		case 'html':
			break;
//		case 'ubb':
		}
		echo $output."\n";
		if($chapter_id!=end(array_keys($layout)) || $page_id!=end($page_list))
		{
			if($page['page_type']!='image')
			{
?>
<br style="page-break-after:always;" />
<?php
			}
		}
	}
}
?>