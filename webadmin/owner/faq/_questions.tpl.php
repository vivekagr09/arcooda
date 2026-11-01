<!-- videos.tpl -->
<style type="text/css">
    .flagHeader{
        width: 90%;
    }
</style>
<?php
$productListQuesions = [];
$item_questions = array();
$query        = $DB->query("SELECT * FROM faq WHERE category = '" . $posted['id'] . "' order by `rank` ASC");
while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
    $item_questions[] = $row;
}
function item_question_row($count = "[ROW_COUNT]", $row_data = array())
{
    GLOBAL  $DB;
    static $alt = false;

    $section_name = 'questions';
    $name_key = $section_name . "[" . $count . "]";
    $hidden   = array(
        $name_key . "[id]"   => $row_data["id"],
        $name_key . "[rank]" => $row_data["rank"],
    );
    ob_start();
?>
    <tr id="item_question_rowa[<?=$count?>]" <?=(($alt = !$alt) ? ' class="alt"' : "")?>>
        <td style="white-space:nowrap;">
            <a href="#" onclick="item_question_move('<?=$count?>',-1,1);return false"><img src="/webadmin/images/up_arrow.gif" alt="&uarr;" border="0" /></a>
            <a href="#" onclick="item_question_move('<?=$count?>',1,0);return false"><img src="/webadmin/images/down_arrow.gif" alt="&darr;" border="0" /></a>
        </td>
        <td style="white-space:nowrap;">
            <?=formatHTMLInputs($hidden)?>
            <?= $row_data['question'] ?>
        </td>
        <td style="width:5%;padding:5px 10px;">
           <i class="far fa-question-circle"
           onmouseover='tt_in(<?= json_encode($row_data["answer"]) ?>);'
           onmouseout="tt_out();"></i>
            
        </td>
        <td style="width:25%;text-align:center;">
            <?php
            $products_lists = [];
            $product_ids = explode(',', $row_data['productid']); // Convert the string into an array
            $software_ids = explode(',', $row_data['softwareid']);

            $products_lists = array_merge($products_lists,$product_ids,$software_ids);
           foreach ($products_lists as $product_id) {
                $product_id = trim($product_id);
                $product = $DB->query_first("SELECT product_name FROM products WHERE id='" . $product_id . "' LIMIT 1"); 
                $productName = $product['product_name'] ?? '';

                echo '<a href="/webadmin/products_new/edit.php?action=edit&id=' . $product_id . '" 
                        target="_blank"
                        onmouseover=\'tt_in(' . json_encode($productName) . ')\'
                        onmouseout="tt_out();">'
                        . $product_id .
                     '</a>';

                if (end($products_lists) !== $product_id) {
                    echo ', ';
                }
            }
            ?>
        </td>
         <?php
            $links = [];        
            $links[] = '<a href="/webadmin/owner/faq/faq.php?action=edit&id='.$row_data['id'].'" target="_blank">Edit</a>';
            $links[] = '<a onclick="return confirm(\'Are you sure you want to delete this FAQ?\')" href="/webadmin/owner/faq/faq.php?action=delete&id='.$row_data['id'].'">Delete</a>';
            ?>
        <td style="text-align:center" width="20%"><?= implode('  ',$links) ?></td>
    </tr>
<?php
return ob_get_clean();
}

if (is_array($item_questions)) {
?>

<script type="text/javascript">
function item_question_move(row_id, direction, limit)
{
    var $the_row = $("[id='item_question_rowa["+row_id+"]']");
    var $siblings = $the_row.parent().children();
    var position = 0;
    for (var i = 0; i < $siblings.length; i++) if ($siblings[i].id == $the_row[0].id) position = i;
    if (direction < 0 && position + direction >= limit)
    {
        $the_row.insertBefore($siblings.eq(position + direction));
    }
    else if (direction > 0 && position + direction <= $siblings.length - limit)
    {
        $the_row.insertAfter($siblings.eq(position + direction));
    }
    var count = 0;
    $("[id^='item_question_rowa']").each(function() {
       $(this).find("input[name$='[rank]']").val(count++);
    });
    $("[id^='item_question_rowa']").removeClass("alt").filter(":odd").addClass("alt");
}

</script>

<?=formatHTMLTitle('<a href="#" onclick="$(\'#sectionVideos\').add(\'#sectionVideos_hideNote\').slideToggle(\'slow\');return false">' . htmlspecialchars(_("Questions")) . "</a>") ?>

<div id="sectionVideos">
    <table class="grid-view"  style="width:90%">
        <tr>
            <th></th>
            <th>Question</th>
            <th>Answer</th>
            <th>Product Linked</th>
            <th>Edit/Delete</th>
        </tr>
<?php
    $count    = 0;
    $affected = array();
    foreach ($item_questions as $question) {
        $affected[] = $question['id'];
        $product_ids = explode(',', $question['productid']); // Convert the string into an array
        $software_ids = explode(',', $question['softwareid']);

        $productListQuesions = array_merge($productListQuesions,$product_ids,$software_ids);
        echo item_question_row($count++, $question);
    }
?>
    </table>
    <br /><br />

</div>
<div id="sectionVideos_hideNote" style="background:#fff;text-align:center;padding:8px;display:none;">
    <?=htmlspecialchars(sprintf(_("%s can be viewed by clicking \"%s\" above"), _("Videos"), _("Videos")))?><br />
</div>

<?php
}
?>

<!-- /videos.tpl -->
