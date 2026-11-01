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

<table align="center">
	<thead><tr bgcolor="#000066">
		<td align="center" bgcolor="#336699"><b>
			<a class="td_options" href="#">Id</a>
			<img src="/webadmin/desc.gif" width="7" height="7" border="0" />
		</b></td>
		<td align="center"><b>Preview</b></td>
		<td align="center"><b><a class="td_options" href="#">Show Front</a></b></td>
		<td>&nbsp;</td>
	</tr></thead>
	<tbody>
<?php
// Get all icons
foreach(brandSearch() as $id) {
	$item = brandFromDatabase($id);
?>
			<tr>
				<td><?=$item['id']?></td>
				<td class="preview">
<ul class="block-features__list">
	<li class="block-features__item">
		<div class="block-features__item-icon"><img src="/webadmin/uploads/brand/img/<?=$item['img']?>" width="48" /></div>
		<div class="block-features__item-info">
			<div class="block-features__item-title"><?=$item['name']?></div>
			<div class="block-features__item-subtitle"><?=$item['text']?></div>
		</div>
	</li>
</ul>
				</td>
				<td><?=$item['show_front']?></td>
				<td>
					<a href="addedit.php?action=do_edit&id=<?=$item['id']?>">Edit</a> |
					<a href="delete.php?id=<?=$item['id']?>">Delete</a>
				</td>
			</tr>
<?php
}
?>
	</tbody>
</table>
<?php
include_once __DIR__ . '/footer.inc.php';
