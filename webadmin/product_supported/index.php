<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';
$move_up_url='index.php?action=move_up&id=';
$move_down_url='index.php?action=move_down&id=';
function supportedMoveRank($id, $rank_delta)
{
    global $DB;
    if (0 == $rank_delta) {
        return;
    }

    $cache_rank = array();

    $old_order = array();
    $query     = $DB->query("SELECT `id`, `rank` FROM `product_supported`  ORDER BY `rank` ASC" );
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
                "UPDATE `product_supported` SET `rank` = %d WHERE `id` = %d LIMIT 1",
                $k,
                $v
            ));
        }
    }
}
if($_REQUEST['action']=='move_down' && $_REQUEST['id']>0){
	supportedMoveRank($_REQUEST['id'], -1);
	echo '<script>window.location.href="/webadmin/product_supported"</script>';
}
if($_REQUEST['action']=='move_up' && $_REQUEST['id']>0){
	supportedMoveRank($_REQUEST['id'], 1);
	echo '<script>window.location.href="/webadmin/product_supported"</script>';
}
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

<table align="center" width="100%">
	<thead><tr bgcolor="#000066">
		<td align="center" width="10%"></td>
		<td align="center" bgcolor="#336699"><b>
			<a class="td_options" href="#">Id</a>
			<img src="/webadmin/desc.gif" width="7" height="7" border="0" />
		</b></td>
		<td align="center"><b>Preview</b></td>
		<td align="center"><b><a class="td_options" href="#">Active Online</a></b></td>
		<td>&nbsp;</td>
	</tr></thead>
	<tbody>
<?php
// Get all icons
$brand = array();
$query = $DB->query('SELECT * FROM `product_supported` order by `rank` DESC');
while($row = $DB->fetch_assoc($query)) {
	$brand[$row['id']] = $row;
}
$DB->free_result($query);
foreach ($brand as $icon) {
?>
			<tr>
				<td align="center"><a href="<?php hecho($move_up_url . $icon["id"])?>"><img src="/webadmin/images/arrowUp.gif" width="11" height="11" border="0" /></a><a href="<?php hecho($move_down_url . $icon["id"])?>"><img src="/webadmin/images/arrowDown.gif" width="11" height="11" border="0" /></a></td>
				<td><?=$icon['id']?></td>
				<td class="preview">
<ul class="block-features__list">
	<li class="block-features__item">
		<div class="block-features__item-icon"><img src="<?=$icon['img']?>" width="48" /></div>
		<div class="block-features__item-info">
			<div class="block-features__item-title">
<?php
    if (empty($icon['slug']) || empty($icon['description'])) {
?>
				<?=htmlspecialchars($icon['name'])?>
<?php
    } else {
?>
				<a href="<?=htmlspecialchars('/arcade-play/' . $icon['slug'])?>" target="_blank"><?=htmlspecialchars($icon['name'])?></a>
<?php
    }
?>
			</div>
			<div class="block-features__item-subtitle"><?=$icon['text']?></div>
		</div>
	</li>
</ul>
				</td>
				<td><?=$icon['active']?></td>
				<td>
					<a href="addedit.php?action=do_edit&id=<?=$icon['id']?>">Edit</a> |
					<a href="delete.php?id=<?=$icon['id']?>">Delete</a>
				</td>
			</tr>
<?php
}
?>
	</tbody>
</table>
<?php
include_once __DIR__ . '/footer.inc.php';
