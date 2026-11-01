<?php
include_once('project_common.inc.php');
include_once('templates/project_header.tpl.php');

$project_id=intval($posted['project_id']);
$project=projectFromDatabase($project_id);

$criteria = array();

$criteria['project_id'] = $project_id;

/*if (isset($_GET['order_by'])) {
	$criteria['order_by'] = $_GET['order_by'];
}

if (isset($_GET['order'])) {
	$criteria['order'] = $_GET['order'];
} */

$project_chapters=projectChapterSearch($criteria);

$doprint_url=$get_vars;
$doprint_url['action']='chapter_view';
$doprint_url='index.php?'.http_build_query($doprint_url);

$action='view';
?>
<form action="<?php echo $doprint_url ?>" method="post" target="_blank">
<?php
include_once('templates/project_view.tpl.php');
?>
</form>
<?php
include_once('templates/project_footer.tpl.php');
?>