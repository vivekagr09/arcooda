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
    "SELECT * FROM `arcooda_www`.`products_reviews`  WHERE `id` = $id LIMIT 1", MYSQLI_ASSOC);
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
                    <td style="text-align:right;font: bold 14px verdana;">Our Reply:</td>
                    <td style="text-align:left;">
                       <textarea style="width:365px;font: 13px verdana;" type="text" name="our_reply" value="<?php hecho($row['our_reply'])?>" row="10"><?php hecho($row['our_reply'])?></textarea>
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
    reviewSetValue($posted['id'], 'our_reply', $posted['our_reply']);
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
