<?php
moduleLoad('asset'); //assetFromDatabase, assetTableInfoFromType

foreach ($layout as $chapter_id => $page_list)
{
  $chapter = projectChapterFromDatabase($chapter_id);
?>
<h1><?php hecho($chapter['chapter_title']) ?></h1>
<?php
  foreach ($page_list as $page_id)
  {
    $page = projectPageFromDatabase($page_id);
    $output = $page['page_content'];

    switch ($page['page_type'])
    {
    case 'image':
      $asset = assetFromDatabase('project_pages', $page_id);
      $table_info = assetTableInfoFromType('project_pages');
      $from_file = $table_info['directory'] . $asset['filename'];
      $landscape = false;
      if (is_file($from_file) && filesize($from_file) > 0)
      {
        $dim = getimagesize($from_file);
        if ($dim[0] > 640 && $dim[0] > $dim[1])
          $landscape = true;
      }
      $output = '<div style="text-align:center;"><img src="' . hecho(relative_url('/webadmin/assets/index.php?asset_type=project_pages&action=view&asset_id=' . $page_id, $_GLOBALS['site_settings']['home_uri']), 1) . '"' . (($landscape) ? ' style="filter:progid:DXImageTransform.Microsoft.BasicImage(rotation=3);max-width:100%;"' : '') . ' /><h5>' . hecho($asset['asset_caption'], 1) . '</h5></div>';
      break;
    case 'plain':
      $output = nl2br(hecho($output, 1));
      break;
    case 'semihtml':
      $output = explode("\n", $output);
      foreach ($output as $key => $row)
        if (substr($row, 0, 2) == '- ')
          $output[$key] = '<li>' . trim(substr($row, 1)) . '</li>';
      $output = nl2br(implode("\n", $output));
      break;
    case 'html':
      break;
    //case 'ubb':
    //case 'markdown':
    }
    echo $output . "\n";
    if ($chapter_id != end(array_keys($layout)) || $page_id != end($page_list))
    {
      if ($page['page_type'] != 'image')
      {
?>
<br style="page-break-after:always;" />
<?php
      }
    }
  }
}
?>