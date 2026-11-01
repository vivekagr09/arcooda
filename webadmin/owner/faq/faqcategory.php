<?php
include_once __DIR__ . '/common.inc.php';
global $DB;

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';
include_once __DIR__ .'/../../options/options_functions.inc.php';

moduleLoad('product'); //productFromDatabase

$move_up_url='?action=move_up&id=';
$move_down_url='?action=move_down&id=';
function pageMoveRankCategory($id, $rank_delta)
{
    global $DB;
    if (0 == $rank_delta) {
        return;
    }

    $cache_rank = array();

    $old_order = array();
    $query     = $DB->query("SELECT `id`, `rank` FROM `faq_category`  ORDER BY `rank` ASC" );
    while ($row = $DB->fetch_assoc($query)) {
        $old_order[] = $row['id'];
        $cache_rank[$row['id']] = $row['rank'];
    }
    $DB->free_result($query);
    $old_pos = array_search($id, $old_order);

    $new_pos = min(max(0, $old_pos + $rank_delta), count($old_order) - 1);
    $new_order = $old_order;
    array_splice($new_order, $old_pos, 1);
    array_splice($new_order, $new_pos, 0, array($id));

    foreach ($new_order as $k => $v) {
        if ($cache_rank[$v] != $k) {
            $DB->query(sprintf(
                "UPDATE `faq_category` SET `rank` = %d WHERE `id` = %d LIMIT 1",
                $k,
                $v
            ));
        }
    }
}
?>
<link rel="stylesheet" href="style.css" type="text/css" />
<style>
	/* Tooltip container */
	.tooltipdata {
			position: relative;
		display: inline-block;
		cursor: pointer;
	}

	/* Tooltip text */
	.tooltipdata .tooltiptext {
		visibility: hidden;
		width: 300px;
		text-align: left;
		border-radius: 5px;
		padding: 5px;
		position: absolute;
		z-index: 1;
		opacity: 0;
		transition: opacity 0.3s;
		background-color: #FFFFE1;
		color: #000000;
		border-color: #000000;
		border-width: 1px;
		border-style: solid;
	}

	/* Show the tooltip text when you mouse over the tooltip container */
	.tooltipdata:hover .tooltiptext {
		visibility: visible;
		opacity: 1;
	}
</style>
<table border="0" cellpadding="2" cellspacing="2" width="100%">
	<tr>
		<td class="pageMenu" width="20%" style="text-align:center"><b>FAQ Category</b></td>
		<td class="pageMenu" style="text-align:right;" width="80%">
			<a href="/webadmin/owner/faq/faqcategory.php?action=add">Add New</a> |
			<a href="/webadmin/owner/faq/faqcategory.php">List Faq Category</a>
		</td>
	</tr>
</table>
<div class="meta_content" style="width:100%;">
<br />

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
	$sql = "UPDATE faq_category SET name='" . $posted['name'] . "', status='" . $posted['status'] . "' WHERE id='" . $posted['id'] . "' LIMIT 1";
        $DB->query($sql);

     if(array_key_exists('questions', $posted))  {
     		foreach ( $posted['questions'] as $key => $question) {
     			$sql = "UPDATE faq SET rank='" . $question['rank'] . "' WHERE id='" . $question['id'] . "' LIMIT 1";
        		$DB->query($sql);
     		}
     }
     echo 'Successfully Updated Value';
    $posted['action'] = 'list';
} elseif ('do_add' == $posted['action']) {
	GLOBAL  $DB;
    $action = 'do_add';
     $sql = "INSERT INTO faq_category(name,status) VALUES ('" .$posted['name'] . "', '" . $posted['status'] . "')";
        $DB->query($sql);
        echo 'Successfully Created New Category';
    $posted['action'] = 'list';
}elseif ('delete' == $posted['action']){
	$sql = "DELETE FROM `faq_category` WHERE `id` = ".$posted['id']." LIMIT 1";
	$DB->query($sql);
        echo 'Successfully deleted Category';
    $posted['action'] = 'list';
} else {
	if($posted['action']=='move_down' && $posted['id']>0){
		pageMoveRankCategory($posted['id'], -1);
		echo '<script>window.location.href="/webadmin/owner/faq/faqcategory.php"</script>';
	}
	if($posted['action']=='move_up' && $posted['id']>0){
		pageMoveRankCategory($posted['id'], 1);
		echo '<script>window.location.href="/webadmin/owner/faq/faqcategory.php"</script>';
	}

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
	$query        = $DB->query("SELECT * FROM faq_category order by `rank` DESC");
	while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
	    $category[] = $row;
	}
}
switch ($action) {

    case 'list':

?>
<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionSiteOptions\');">Faq Category</a>')?>

<div id="sectionSiteOptions">
	<table class="table_grid spacetable" style="width:95%">
		<tr><th>Rank</th><th>Name</th><th>Linked To</th><th>Status</th><th style="width:5%;"></th></tr>
<?php
$alt = false;
        foreach ($category as $option) {
			
			$product_list = [];

			$sql = "SELECT productid, softwareid FROM faq WHERE `category` = " . $option['id'] . " ORDER BY `id` DESC";
			$result = $DB->query($sql);
			
			while ($row = $result->fetch_assoc()) {
				// Split the productid and softwareid by commas and merge them into the product_list array
				$product_ids = explode(',', $row['productid']);
				$software_ids = explode(',', $row['softwareid']);
				
				$product_list = array_merge($product_list, $product_ids, $software_ids);
			}
			// Remove any empty values and duplicates from the array
			
			$product_tooltip='<div><h3>Products</h3>';
			$product_tooltip .='<ul style="list-style-type:none;">';
			foreach ($product_list as $key => $product) {
				$pro=productFromDatabase($product);
				$product_tooltip .='<li><a href="/webadmin/products_new/?action=edit&search_for='.$pro['id'].'&id='.$pro["id"].'" target="_blank">'.$pro["product_name"].'</a></li>';
			}
			$product_tooltip .='</ul></div>';
			
?>
		<tr<?=($alt = !$alt) ? '' : ' class="alt"'?> onmouseover="this.style.backgroundColor='#9DD6D2';" onmouseout="this.style.backgroundColor='';">
			<td align="center"><a href="<?php hecho($move_up_url . $option["id"])?>"><img src="/webadmin/images/up_arrow.gif" width="11" height="11" border="0" /></a><a href="<?php hecho($move_down_url . $option["id"])?>"><img src="/webadmin/images/down_arrow.gif" width="11" height="11" border="0" /></a></td>
			<td style="width:80%;padding:5px 10px;">
				<?= $option['name'] ?>
			</td>
			<td style="text-align:center"><div class="tooltipdata">
				   <?= count(array_filter($product_list)) ?>
				    <div class="tooltiptext">
				       <?= $product_tooltip ?>
				    </div>
				</div>

			</td>
			<td style="text-align:center;<?=$option['status'] == 'y' ? 'background-color: #e9f8bc':'background-color: #ffe6ee'?>"><?= $option['status'] == 'y' ? 'Active' : 'In Active' ?></td>
		<?php
			$links = [];		
			$links[] = '<a href="?action=edit&id='.$option['id'].'" target="_blank">Edit</a>';
			$links[] = '<a onclick="return confirm(\'Are you sure you want to delete this category?\')" href="?action=delete&id='.$option['id'].'">Delete</a>';
?>
			<td style="text-align:center" width="20%"><?= implode('  ',$links) ?></td>
			<!-- <td style="width:0px;text-align:center;"><a href="?action=edit&id=<?=$option['id']?>">Edit</a></td> -->
		</tr>
<?php
}
?>
		<form action="?action=add" method="post">
		<tr class="alt"><td colspan="5" style="text-align:center;">
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
		<tr>
			<td class="left_title">Status :</td>
			<td>
				<select class="form-control col-sm-9" id="status" name="status"  onchange="this.style.backgroundColor=((this.value=='n')?'rgb(255 0 0)':'#00FF00');" style="width:90%;background:<?=('y' == 'y') ?  '#00FF00;':'rgb(255 0 0);';?>">
					<?= formatHTMLOptions(['n'=>'In Active','y'=>'Active'], 'y') ?>
				</select>
			</td>
		</tr>
	</table>
	<p align="center">
		<!-- <input type="button" value="Go Back" onclick="javascript:history.back();" /> -->
		<input type="submit" value="Save Changes" />
	</p>
</form>
<?php
break;

    case 'edit':
        $row = $DB->query_first("SELECT * FROM faq_category WHERE id='" . $posted['id'] . "' LIMIT 1");
?>
<form action="?action=do_edit&id=<?=$posted['id']?>" method="post">
	<table class="wide">
		<tr>
			<td class="left_title">Category :</td>
			<td>
					<?php input_text("name", $row['name']); ?>
			</td>
		</tr>
		<tr>
			<td class="left_title">Status :</td>
			<td>
				<select class="form-control col-sm-9" id="status" name="status"  onchange="this.style.backgroundColor=((this.value=='n')?'rgb(255 0 0)':'#00FF00');" style="width:90%;background:<?=('y' == $row['status']) ?  '#00FF00;':'rgb(255 0 0);';?>">
					<?= formatHTMLOptions(['n'=>'In Active','y'=>'Active'], $row['status']) ?>
				</select>
			</td>
		</tr>
	</table>
	<?php include "_questions.tpl.php";  ?>
	<?php include "_products.tpl.php";  ?>
	<p align="center">
		<!-- <input type="button" value="Go Back" onclick="javascript:history.back();" /> -->
		<input type="submit" value="Save Changes" />
	</p>
</form>
<?php
break;
}
?>
</div>
<!-- footer.inc.php -->
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/webadmin/footer.inc.php";
?>