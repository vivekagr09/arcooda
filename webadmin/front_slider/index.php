<?php
include_once __DIR__ . '/common.inc.php';

global $DB,$_GLOBALS;
include_once __DIR__ . '/header.inc.php';

$view_list = array();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['random_slider'])){
        $DB->query('UPDATE  `options` set `value`="yes" WHERE `variable`="random_front_page_slider"');
        $_GLOBALS['site_options']['random_front_page_slider']='yes';
    }else{
        $DB->query('UPDATE  `options` set `value`="no" WHERE `variable`="random_front_page_slider"');
        $_GLOBALS['site_options']['random_front_page_slider']='no';
    }
}
$query = $DB->query('SELECT * FROM `front_slider` order by id desc');
while($row = $DB->fetch_assoc($query)) {
	$view_list[$row['id']] = $row;
}
$DB->free_result($query);
if (isset($view_list) && is_array($view_list) && count($view_list)) {
?>
<?= formatHTMLTitle('Front Page Slider') ?>
<div>
    <form method="post" id="slider-form" action="">
	<div style="margin-left: 35px; font-size: 20px; font-weight: 700; padding-bottom: 10px;">
        <input type="checkbox" name="random_slider" id="random_slider" <?= ($_GLOBALS['site_options']['random_front_page_slider']=='yes')?'checked':''?> style="width: 21px; height: 16px;">Random Front Page Slider 
        <span><img src="/svg/question.png" width="20px" onmouseover="tt_in('if check box is checked random slider will be display on front page Default and Active  has not matter');" onmouseout="tt_out();"></span>
        </div>
    </form>
    <table class="table_grid" style="white-space:nowrap;" cellpadding="4">
        <tr>
            <th> Id</th>
            <th style="width:20%;">Name</th>
            <th>Comment</th>
            <th>Default</th>
            <th>Active</th>
            <th>Created At</th>
            <th></th>
        </tr>
<?php
    foreach ($view_list  as $list) { ?>
        <tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
    <td style="text-align:center;"><?=$list['id']?></td>
    <td style="text-align:left;"><?=$list['name']?></td>
    <td style="text-align:left;"><?=$list['comment']?></td>
    <td style="text-align:center;"><?=('y' == $list['default']) ? 'Yes' : 'No'?></td>
    <td style="text-align:center;"><?=('y' == $list['status']) ? 'Yes' : 'No'?></td>
    <td style="text-align:center;"><?=date('d/m/Y',strtotime($list['created_at']))?></td>
    <td style="text-align:center;">
        <a href="preview.php?id=<?=$list['id']?>" target="_blank">Preview</a> |
        <a href="addedit.php?action=do_edit&id=<?=$list['id']?>">Edit</a> |
		<a href="delete.php?id=<?=$list['id']?>">Delete</a>
    </td>
</tr>
 <?php   }
?>
    </table>
<?php
} else {
?>
    No Slider Available to display
<?php
}
?>
    <br />
    <br />
</div>
<script type="text/javascript">
    $('#random_slider').click(function(){
        $('#slider-form').submit();
    })
</script>