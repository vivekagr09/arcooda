<?php
/**
 * @var mixed[] $posted
 */
include_once __DIR__ . '/common.inc.php';

$action  = $posted["action"];
$data  = $posted["data"];
$id = $posted['id'];
switch ($action) {
    case 'do_edit':
        doEdit($posted);
        break;
    case 'edit':
    default:
        showEditData($id , $data);
        break;
}
function showEditData($id , $data)
{
    global $DB;
 
    $row = $DB->query_first(
    "SELECT * FROM `highwaygroup`.`invoice_dashboard_history`  WHERE `id` = $id LIMIT 1", MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
    </head>
    <body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
        <form action="" method="post">
            <input type="hidden" name="action" value="do_edit" />
            <table style="width:50%;border:1px solid blue;background-color:#99CCFF;margin:auto">
                 <tr>
                    <input type="hidden" name="action" value="do_edit" />
                    <input type="hidden" name="id" value="<?php echo $id ?>" />
                    <input type="hidden" name="data" value="<?php echo $data ?>" />
                    <input type="hidden" name="invoice_id" value="<?php echo $row['invoice_id'] ?>" />
                    <input type="hidden" name="invoice_data" value="<?php echo $row['display_to_customer'] ?>" />
                    <td style="text-align:right;font: bold 14px verdana;">Online Display to Customer:</td>
                    <td style="text-align:left;">
                       <textarea style="width:365px;font: 13px verdana;" type="text" name="display_to_customer" value="<?php hecho($row['display_to_customer'])?>" row="10"><?php hecho($row['display_to_customer'])?></textarea>
                    </td>
                </tr>

            </table>
            <br />
            <div style="text-align:center"><input type="submit" value="Update" /></div>
        </form>
    </body>
</html>
<?php
}
function doEdit($posted)
{
     global $DB,$_GLOBALS;
     $DB->query(sprintf("UPDATE `highwaygroup`.`invoice_dashboard_history` set `display_to_customer`=%s where `id`=%d",
         $DB->escape($posted['display_to_customer']),
         $posted['id']
    ));      
   if($_GET['data'] == $posted['invoice_data']){
         $DB->query(sprintf("UPDATE `highwaygroup`.`invoices` set `display_to_customer`=%s,`is_display_customer` = %d , `display_to_date`=%s where `invoice_id`=%d",
             $DB->escape($posted['display_to_customer']),
             1,
             date('Y-m-d'),
             $posted['invoice_id']
        ));
    }
     $message = array(
        'invoice_id'      => $posted['invoice_id'],
        'message_sender'  => $_GLOBALS['staff']['id'],
        'message_sentto'  => 'Invoice System',
        'message_type_id' =>  4,
        'message_title'   => 'Display to Customer',
        'message_notes'   => $posted['display_to_customer'],
    );
    invoiceHistoryToDatabase($posted['invoice_id'], $message);
    ?>
    <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
    </head>
    <body>
        <script type="text/javascript">
        window.opener.location.reload();
        window.close();

        //]]>
                </script>
            </body>
        </html>
<?php   
}
?>
