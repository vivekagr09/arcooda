<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';
?>
<br />
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,400i,500,500i,700,700i" />

<style type="text/css">
.preview {color:#262626;font-family:"Roboto", "sans-serif";font-size:14px;font-weight:400;line-height:22px}
.block-features__list{border-top:2px solid #ebebeb;padding:0;display:flex;list-style:none;margin:0;box-sizing:border-box;margin-block-end:0px;margin-block-start:0px;margin-inline-end:0px;margin-inline-start:0px;padding-inline-start:0px;font:inherit}
.block-features__item {padding:1.5rem 1.5rem;-webkit-box-pack:start;display:flex;width:276px;box-sizing:border-box;text-align:-webkit-match-parent;font:inherit}
.block-features__item-icon{margin-right:1.375rem;fill:#1a79ff;box-sizing:border-box;display: block;font:inherit}
.block-features__item-icon img{vertical-align:middle;border-style:none;box-sizing:border-box;width:48px;font:inherit}
.block-features__item-info{box-sizing:border-box;display:block;font:inherit}
.block-features__item-title{font-size:16px;font-weight:500;color:#343a40!important;box-sizing:border-box;display:block;font:inherit}
.block-features__item-subtitle{font-size:14px;color:#6c757d!important;box-sizing:border-box;display:block;font:inherit}
</style>

<table align="center" width="80%">
	<thead><tr bgcolor="#000066">
		<td align="center" bgcolor="#336699"><b>
			<a class="td_options" href="#">Id</a>
		</td>
		<td align="center"><b><a class="td_options" href="#">Color</a></b></td>
		<td align="center"><b><a class="td_options" href="#">Our Code</a></b></td>
		<td>&nbsp;</td>
	</tr></thead>
	<tbody>
<?php
// Get all icons
$icons = array();
$query = $DB->query('SELECT * FROM `product_colour`');
while($color = $DB->fetch_assoc($query)) {
?>
			<tr>
				<td align="center"><?=$color['id']?></td>
				<td align="center"><div><p style="padding: 5px; float:left;width:10px;border: 1px solid black;height:10px;background-color:<?=$color['color_val'];?>"></p><span><?=$color['colour']?></span></div></td>
				<td align="center"><?=$color['code3']?></td>
				<td align="center">
					<a href="addedit.php?action=do_edit&id=<?=$color['id']?>">Edit</a> |
					<a href="delete.php?id=<?=$color['id']?>">Delete</a>
				</td>
			</tr>
<?php
}
?>
	</tbody>
</table>
<?php
include_once __DIR__ . '/footer.inc.php';
