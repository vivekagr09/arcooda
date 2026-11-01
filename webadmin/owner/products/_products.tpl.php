<!-- _products.tpl -->

<style type="text/css">
div.product_content tr.specialprice a ,div.product_content tr.specialprice td  {
    color: #f00 !important;
}

</style>
<?php
/**
 * Product List Template
 *
 * Inputs:
 * - $item_list
 */
moduleLoad('product'); //productFromDatabase

if (!isset($item_list) || !is_array($item_list))
{
    return;
}

if (!moduleLoad('invoice'))
{
    return;
}

$active_list = productGetOptionList('active');

$alt = false;
$ord_img = '<i class="fas fa-sort-' . (('desc' == $order) ? 'down' : 'up') . '"></i>';

$action_url = $_GET;
unset($action_url['id']);
$action_url['id'] = '';
$activate_url = '.?' . http_build_query(array(
    'action' => 'activate'
) + $action_url);
$copy_url = '.?' . http_build_query(array(
    'action' => 'copy'
) + $action_url);
$deactivate_url = '.?' . http_build_query(array(
    'action' => 'deactivate'
) + $action_url);
$delete_url = '.?' . http_build_query(array(
    'action' => 'delete'
) + $action_url);
$edit_url = '/webadmin/products_new/?' . http_build_query(array(
    'action' => 'edit'
) + $action_url);
$select_url = '.?' . http_build_query(array(
    'action' => 'select'
) + $action_url);
$unlock_url = '.?' . http_build_query(array(
    'action' => 'unlock'
) + $action_url);
$view_url = '.?' . http_build_query(array(
    'action' => 'view'
) + $action_url);
$sort_url = $_GET;
unset($sort_url['order_by']);
$sort_url['order_by'] = '';
$sort_url = '.?' . http_build_query(array(
    'order' => ('desc' == $order) ? 'asc' : 'desc'
) + $sort_url);

?>

<script type="text/javascript">
//<![CDATA[
function selectAll(checked) {
    var collection = getElements('item_list[]');
    for (i = 0; i < collection.length; i++) collection[i].checked = checked;
}
//]]>
</script>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionProducts\');return false">Owners Section – Product List</a>') ?>

<?php
$filter_url = $_GET;
unset($filter_url['stock_type']);
unset($filter_url['active']);
unset($filter_url['discontinued']);
$filter_url = '?' . http_build_query($filter_url);
?>


<div id="sectionProducts">
<?php
$select_url = $_GET;
$select_url['action'] = 'select';
$select_url = '.?' . http_build_query($select_url);
?>
    <table class="table_grid">
        <tr>
            <th><a href="<?php hecho($sort_url) ?>id">Master ID</a><?=('id' == $order_by) ? $ord_img : '' ?></th>
            <th><a href="<?php hecho($sort_url) ?>product_name">Product Name</a><?=('product_name' == $order_by) ? $ord_img : '' ?></th>
            <th>Production Quantity</th>
            <th>Registered Owners</th>
            <th style="width:20%">Latest Manual</th>
            <th style="width:20%">Latest Firmware</th>
            <th><a href="<?php hecho($sort_url) ?>active">Active</a><?=('active' == $order_by) ? $ord_img : '' ?></th>
            <th>Date Added</th>

            <th>&nbsp;</th>
        </tr>
<?php
$keyid=1;
foreach ($item_list as $id)
{
    $item             = productFromDatabase($id);
    $serail_number= getserialNumberProduct($id);

    $class = '';
    if ($alt = !$alt)
    {
        $class = 'alt';
    }
?>
    <tr <?php echo ($item['active'] == 'y') ? 'style="background-color:rgb(144, 238, 160)"' : ''; ?> onmouseover="this.style.backgroundColor='#9DD6D2';" onmouseout="this.style.backgroundColor='<?php echo ($item['active'] == 'y') ? 'rgb(144, 238, 160)' : ''; ?>';" >
                <td style="text-align:center";><?=$item['id']?>
            </td>
      <td>          
<?php
        
        if (array_key_exists('view_url', $item))
        {
            echo '<a href="' . hecho($item['view_url'], 1) . '" target="_blank" style="' . $styles . '">';
        }

?>
                <?php hecho($item['product_name']) ?>
<?php
        if (array_key_exists('view_url', $item))
        {
            echo '</a>';
        }

?>
                </td>
                <td style="text-align:center;">
                    <?php if(count($serail_number) > 0) { ?>
                        <a href="/webadmin/serial/" target="_blank"><?= count($serail_number)?></a>
                    <?php } else {?>
                    <?= count($serail_number)?>
                    <?php } ?>
                </td>
                <td style="text-align:center;">
                    <?php 
                        $units_registerd=0;
                        foreach ($serail_number as $key => $data) {
                            if($data['member_id']){
                                $units_registerd++;
                            }
                        }

                        if($units_registerd > 0) {
                            $units_registerd = '<a href="/webadmin/owner/registered/" target="_blank">'.$units_registerd.'</a>';
                        }
                    ?>
                    <?= $units_registerd ?>
                </td>
                <td style="text-align:center;">
                    <?php 
                        $manual=getlatestmanual($id);
                        if($manual){

                            echo '<a href="/webadmin/owner/manuals/edit.php?id='.$manual["id"].'" target="_blank">'.$manual["name"].'</a>';
                        }
                    ?>
                </td>
                <td style="text-align:center;">
                    <?php 
                        $firmware=getlatestfirmware($id);
                        if($firmware){
                          
                            echo '<a href="/webadmin/owner/firmware/edit.php?id='.$firmware["id"].'" target="_blank">'.$firmware["name"].'</a>';
                        }
                    ?>
                </td>
                <td style="text-align:center;"><?= $item['active']?>
                </td>
                <td style="text-align:center;"><?= ($item['date_added']!='0000-00-00 00:00:00')?date('d/m/Y',strtotime($item['date_added'])):'N/A'?>
                </td>
                

<?php
        $links = array();
      
                $links[] = '<a style="' . $styles . '" href="' . $edit_url . $item['id'] . '" target="_blank">Edit</a>';

            

            
?>
                <td style="text-align:center;<?=$styles?>white-space:nowrap;"><?=implode(' | ', $links) ?></td>
            </tr>
<?php  $keyid++; }
?>
    </table>
    <br /><br />


</div>

<div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionProducts_hideNote">
    Product list can be viewed by Clicking "Products" above<br />
</div>

<!-- /_products.tpl -->
