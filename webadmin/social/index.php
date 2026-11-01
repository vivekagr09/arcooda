<?php

include_once (__DIR__ . '/common.inc.php');

if (!empty($_POST)) {
	$keys = array('facebook', 'twitter', 'youtube', 'instagram','reddit','tiktok');



	foreach($keys as $key) {
		if (array_key_exists($key, $_POST)) {

			if(!isset($_POST['social'][$key])){
				$_POST['social'][$key]=0;
			}

			if(!isset($_POST['rank'][$key])){
				$_POST['rank'][$key]=0;
			}


			$DB->query(sprintf("UPDATE `social` SET `link` = %s , `status`=%s , `rank`=%s WHERE `name` = %s" , $DB->escape($_POST[$key]) ,$DB->escape($_POST['social'][$key]),$DB->escape($_POST['rank'][$key]),$DB->escape($key)));
		}
	}
}

$social = array();

$query = $DB->query('SELECT * FROM `social` ORDER BY `social`.`rank` ASC');
while($row = $DB->fetch_assoc($query)) {
	$social[$row['name']] = $row;
}

include($_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php');

?>

<link rel="stylesheet" href="style.css" type="text/css" />

<style>
div.news td.left_title{
	text-align: left;
}

.sicon{
    background: #3c5a99;
    color: #fff;
    margin: 3px;
    padding: 8px;
    height: 13px !important;
    width: 13px !important;
    border-radius: 50%;
}

.fa-twitter{
	background: #00a2e8;
}

.fa-youtube{
    background: #e52e2e;	
}
.fa-instagram{
	    background: #815dc7;
}
.fa-reddit{
	background:#FF4300;
}
.fa-tiktok{
	    background: #010101;
}
</style>
<script>


function upgradelinkingMoveUp(elem) {
    var row = $(elem).parents(".upgrade_linking_row");

    if (row.prev().hasClass("upgrade_linking_row")) {
        var n = row.prev().find(".row_count").val();
        row.prev().find(".row_count").val(row.find(".row_count").val());
        row.find(".row_count").val(n);
        row.prev().before(row);
    }
	ranksset();
    return false;
}

function upgradelinkingMoveDown(elem) {
	//debugger;
    var row = $(elem).parents(".upgrade_linking_row");

    if (row.next().hasClass("upgrade_linking_row")) {
        var n = row.next().find(".row_count").val();
        row.next().find(".row_count").val(row.find(".row_count").val());
        row.find(".row_count").val(n);
        row.next().after(row);
    }
	ranksset();

    return false;
}

function ranksset(){
	var ik=1;
	$('.ranks').each(function(){
		console.log($(this).val(ik));
		ik++;

	});
}
</script>


<table cellspacing="2" cellpadding="2" style="width: 100%;">
	<tbody>
		<tr>
			<td style="text-align: center; font-weight: bold; width: 20%; background: #def;">Social Media</td>
			<td style="text-align: right; width: 80%; background: #def;"></td>
		</tr>
	</tbody>
</table>

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionSocialLinks\');">Social Media Links</a>') ?>

<div class="news">
	<form action="" method="post">
		<div id="sectionSocialLinks">

		
			<table  class="table_grid" id="products_upgradelinking" style="width: 95%;" align="center">
				<?php

				$ik=0;
				
				foreach($social as $k => $v) :

				 ?>


				<tr id="product_upgradelinking_rowa[<?php echo $ik; ?>]" class="upgrade_linking_row">

				<td><?=str_replace("\n", "", formatHTMLInputs($hidden))?> <a href="#" onclick="return upgradelinkingMoveUp(this);"><img src="/webadmin/images/up_arrow.gif" alt="↑" border="0"></a> <a href="#" onclick="return upgradelinkingMoveDown(this);"><img src="/webadmin/images/down_arrow.gif" alt="↓" border="0"></a> <input type="hidden" class="row_count" name="item_upgradelinking[<?=$count?>][rank]" value="<?= $count ?>"> </td>


			<td style="width: 5%;" class="left_title">
				<input class="social_<?php echo $v['id']; ?>" onchange="UpdateSocialstatus('<?php echo $v['id'];?>')" <?php echo ($v['status']==1) ? 'checked' : ''; ?> type="checkbox" value="1" name="social[<?php echo $v['name']; ?>]"> <?= ucwords($v['name']) ?>
			</td>
				
		    	<td style="width: 5%;">
					<span class="socialicons"><?php echo $v['fafaicon'];?></span>
				</td>

					<td style="width: 60%;">
						<input style="width:90%; text-align:left;" type="text" name="<?= $v['name'] ?>" value="<?= $v['link'] ?>" style="width: 80%;" />

						<input class="ranks" type="hidden" name="rank[<?= $v['name'] ?>]" value="<?= $v['rank'] ?>" style="width: 5%;margin-left:10px;" />
					
					</td>
					<td style="width: 10%;">
						<?php if($v['link'] != '') : ?>
							<a href="<?= $v['link'] ?>" target="_blank">Visit</a>
						<?php endif; ?>
					</td>
				</tr>
				<?php 
			$ik++;
			endforeach; ?>

				<tr>
				<td style="width: 5%" class="left_title"></td>
					<td style="width: 10%" class="left_title">
				
					</td>
					<td lass="left_title"></td>
					<td style="width: 60%; text-align: center;">
						<br />
						<input type="submit" value="Save" style="width: 200px;"/>
						<br /><br />
					</td>
					<td style="width: 10%;">
					</td>
				</tr>

			</table>

		</div>
	</form>
</div>

<?php

include($_SERVER['DOCUMENT_ROOT'] . '/webadmin/footer.inc.php');

?>
