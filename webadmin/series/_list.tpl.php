<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<form action=".?<?=htmlspecialchars(http_build_query($_GET))?>" method="post" autocomplete="off" enctype="multipart/form-data">
    <table class="table_grid">
        <tr>
            <th>ID</th>
            <th style="width:100%">Title</th>
            <th>Active</th>
            <th></th>
        </tr>
<?php
$active_list   = seriesGetOptionList('active');
$alt           = false;
foreach ($item_list as $id) {
    $item = seriesFromDatabase($id);
?>
        <tr<?=($alt = !$alt) ? ' class="alt"' : ''?>>
            <td style="white-space:nowrap"><?=$id?></td>
            <td style="text-align:left"><a href=".?action=view&amp;id=<?=$id?>" target="_blank"><?=htmlspecialchars($item['name'])?></a></td>
            <td style="white-space:nowrap"><a href=".?action=do_activate&amp;id=<?=$id?>"><?=htmlspecialchars($active_list[$item['active']])?></a></td>
            <td style="white-space:nowrap">
                <a href=".?action=edit&amp;id=<?=$id?>">Edit</a> |
                <a href=".?action=delete&amp;id=<?=$id?>">Delete</a>
            </td>
        </tr>
<?php
}
?>
    </table>
    <br />
    <br />
    <!--<button type="submit" name="action" value="do_edit">Edit</button>-->
</form>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
