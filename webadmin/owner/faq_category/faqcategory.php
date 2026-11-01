<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';
?>
<table border="0" cellpadding="2" cellspacing="2" width="100%">
	<tr>
		<td class="pageMenu" width="20%">
			<div align="center">FAQ Category</div>
		</td>
		<td class="pageMenu" width="80%"><div align="right"><a href="/webadmin/owner/faq/faqcategory.php">List Faq Category</a></div></td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
</table>

<div id="optcontent">
<div><h2 style="text-align:center;">Add/Edit Category</h2></div>
<div align="center" width="95%">
<?php
$messages     = array();
$time_now     = time();
$posted       = array_merge($_GET, $_POST);

// Handle action request
if ('do_edit' == $posted['action']) {
    $action = 'do_edit';
    GLOBAL  $DB;
   	$sql = "UPDATE  faq_category SET name='" .$posted['category'] . "' WHERE id='" . $posted['id'] . "' LIMIT 1";
        $DB->query($sql);
        echo 'Successfully Updated Value';
       
    $posted['action'] = 'list';
} elseif ('do_add' == $posted['action']) {
	GLOBAL  $DB;
    $action = 'do_add';
     $sql = "INSERT INTO faq_category(name) VALUES ('" .$posted['option_variable'] . "')";
        $DB->query($sql);
        echo 'Successfully Created New Variable';
    $posted['action'] = 'list';
}else{

}

if (count($messages) > 0) {
    foreach ($messages as $msg) {
        echo $msg . "<br />\n";
    }
}

if ('edit' == $posted['action'] ) {
    $action = 'edit';
} elseif ('add' == $posted['action'] ) {
    $action = 'add';
} else {
    $action = 'list';
$category = array();
$query        = $DB->query("SELECT * FROM faq_category");
while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
    $category[] = $row;
}

switch ($action) {

    case 'list':
?>
<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionSiteOptions\');">Site Options</a>')?>

<div id="sectionSiteOptions">
	<table class="wide spacetable">
		<tr><th>Name</th><th>Action</th></tr>
<?php
$alt = false;
        foreach ($category as $option) {
?>
		<tr<?=($alt = !$alt) ? '' : ' class="alt"'?>>
			<td style="width:80%; padding:5px;">
				<?= $option['name'] ?>
			</td>
			<td style="width:0px;text-align:center;"><a href="?action=edit&id=<?=$option['id']?>">Edit</a></td>
		</tr>
<?php
}
?>
		<form action="?action=add" method="post">
		<tr class="alt"><td colspan="3" style="text-align:center;">
			<input type="submit" value="Add New Category" />
		</td></tr>
		</form>
	</table>
</div>

<br />

<?php
break;

    case 'add':
?>
<form action="?action=do_add" method="post">
	<table class="wide">
		<tr>
			<td class="left_title">Category Name :</td>
			<td>
<?php
input_text('name');
?>
			</td>
		</tr>
	</table>
	<p align="center">
		<input type="button" value="Go Back" onclick="javascript:history.back();" />
		<input type="submit" value="Save Changes" />
	</p>
</form>
<?php
break;

    case 'edit':
        $row = $DB->query_first("SELECT * FROM faq_category WHERE id='" . $posted['id'] . "' LIMIT 1");
?>
<form action="?action=do_edit&option_id=<?=$posted['id']?>" method="post">
	<table class="wide">
		<tr>
			<td class="left_title">Category Name :</td>
			<td><?=html_safe($row['name'])?></td>
		</tr>
		<tr>
			<td class="left_title">Old Value :</td>
			<td>

				<span style="border:1px solid black;background-color:<?=$row['name']?>;width:16px;height:16px;">&nbsp;&nbsp;&nbsp;</span>

				<?=nl2br(html_safe($row['name']))?>
			</td>
		</tr>
		<tr><td class="left_title">&nbsp;</td><td>&nbsp;</td></tr>
		<tr>
			<td class="left_title">New Value :</td>
			<td>
					<?php input_text("name", $row['name']); ?>
			</td>
		</tr>
	</table>
	<p align="center">
		<input type="button" value="Go Back" onclick="javascript:history.back();" />
		<input type="submit" value="Save Changes" />
	</p>
</form>
<?php
break;
}
}
?>
</div>
<!-- footer.inc.php -->
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/webadmin/footer.inc.php";
?>