<?php
include_once __DIR__ . '/common.inc.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include_once __DIR__ . '/header.inc.php';
include_once __DIR__ .'/../../options/options_functions.inc.php';
moduleLoad('product');
global $DB, $_GLOBALS;

$move_up_url='?action=move_up&id=';
$move_down_url='?action=move_down&id=';
function pageMoveRankFAQ($id, $rank_delta)
{
    global $DB;
    if (0 == $rank_delta) {
        return;
    }

    $cache_rank = array();

    $old_order = array();
    $query     = $DB->query("SELECT `id`, `rank` FROM `faq`  ORDER BY `rank` ASC" );
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
                "UPDATE `faq` SET `rank` = %d WHERE `id` = %d LIMIT 1",
                $k,
                $v
            ));
        }
    }
}
?>
<link rel="stylesheet" href="style.css" type="text/css" />
<style>
    .flagHeader{
        width:90% !important;
    }
</style>

<div id="optcontent">
<div align="center" width="95%">
<?php
$messages     = array();
$time_now     = time();
$posted       = array_merge($_GET, $_POST);

//get all category
$getAllCategory = [];
$sql            = "SELECT * FROM faq_category";
if ($query = $DB->query($sql)) {
    while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
        $getAllCategory[$row['id']] = $row['name'];
    }
    $DB->free_result($query);
}

// Handle action request
if ('do_edit' == $posted['action']) {
    $action = 'do_edit';
    GLOBAL $DB;
    // Convert the array of product IDs to a comma-separated string
    $product_ids = implode(',', $posted['productid']);
    $old_vals = $DB->query_first("SELECT * FROM faq WHERE id='" . $posted['id'] . "' LIMIT 1");
    $fields     = $DB->table_columns('faq');
     $set_vals = array();
     $update_comment = '';
    foreach ($fields as $k => $v) {
        if (array_key_exists($k, $posted) && $posted[$k] !=$old_vals[$k]) {
            if (is_array($posted[$k])) {
                $set_vals[$k] = "`" . $k . "` = " . $DB->escape(implode(",", $posted[$k]));
                $update_comment .= $k. ' changed from :'.$old_vals[$k] .' to '. implode(",", $posted[$k])."\n";
            } elseif (isset($posted[$k])) {
                $set_vals[$k] = "`" . $k . "` = " . $DB->escape($posted[$k]);
                $update_comment .= $k. ' changed from :'.$old_vals[$k] .' to '. $posted[$k]."\n";
            } else {
                $set_vals[$k] = "`" . $k . "` = NULL";
                $update_comment .= $k. ' changed from :'.$old_vals[$k] .'to NULL'."\n" ;
            }
        }
    }
    if (count($set_vals)) {
        $DB->query(sprintf(
            "UPDATE %s SET %s WHERE `%s` = %d LIMIT 1",
            'faq',
            implode(', ', $set_vals),
            'id',
            (int)$posted['id']
        ));
        $sql = "INSERT INTO faq_comment (staff_id, comment) 
            VALUES ('" . $_GLOBALS['staff']['id'] . "', '" . $update_comment . "')";           
        $DB->query($sql);
    }
    echo 'Successfully Updated FAQ Entry';

    $posted['action'] = 'list';
} elseif ('do_add' == $posted['action']) {
    GLOBAL $DB;
    $action = 'do_add';
    // Convert the array of product IDs to a comma-separated string
     $product_ids = implode(',', $posted['productid']);
    // Insert new FAQ entry
    $sql = "INSERT INTO faq (question, answer, productid, category, status) 
            VALUES (" . $DB->escape($posted['question']) . ", " . $DB->escape($posted['answer']) . ", '" . $product_ids . "', '" . $posted['category'] . "', '" . $posted['status'] . "')";

    $DB->query($sql);
    echo 'Successfully Created New FAQ Entry';

    $posted['action'] = 'list';
} else {
    // Handle other actions or errors
    if($posted['action']=='move_down' && $posted['id']>0){
		pageMoveRankFAQ($posted['id'], -1);
		echo '<script>window.location.href="/webadmin/owner/faq/faq.php"</script>';
	}
	if($posted['action']=='move_up' && $posted['id']>0){
		pageMoveRankFAQ($posted['id'], 1);
		echo '<script>window.location.href="/webadmin/owner/faq/faq.php"</script>';
	}
}

if (count($messages) > 0) {
    foreach ($messages as $msg) {
        echo $msg . "<br />\n";
    }
}

if ('edit' == $posted['action'] ) {
    $action = 'edit';
} elseif ('add' == $posted['action'] ) {
    $action = 'add';
} elseif ('delete' == $posted['action']) {
    $sql = "DELETE FROM `faq` WHERE `id` = ".$posted['id']." LIMIT 1";
	$DB->query($sql);
    $action = 'list';

    $faqs = array();
	$query        = $DB->query("SELECT * FROM faq");
	while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
	    $faqs[] = $row;
	}
} else {
    $action = 'list';

	$faqs = array();
	$query        = $DB->query("SELECT * FROM faq order by `rank` DESC");
	while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
	    $faqs[] = $row;
	}
}
switch ($action) {

    case 'list':
?>
<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionSiteOptions\');">Faq</a>')?>

<div id="sectionSiteOptions">
	<table class="table_grid spacetable" style="width:90%">
		<tr><th>Rank</th><th>Question</th><th>Answer</th><th>Product Id</th><th>Category</th><th>Status</th><th></th></tr>
<?php
$alt = false;
        foreach ($faqs as $faq) {
            $category_name = '';
            if ($faq['category'] != '') {
                $sql = sprintf(
                    "SELECT `name` FROM faq_category WHERE `id` = %d LIMIT 1",
                    $faq['category']
                );
                if ($row = $DB->query_first($sql, MYSQLI_ASSOC)) {
                    $category_name = $row['name'];
                }
            }
            $products_lists = [];
            $product_ids = explode(',', $faq['productid']); // Convert the string into an array

            $products_lists = $product_ids;

            $firstProduct  = productFromDatabase($products_lists[0]);
?>
		<tr<?=($alt = !$alt) ? '' : ' class="alt"'?> onmouseover="this.style.backgroundColor='#9DD6D2';" onmouseout="this.style.backgroundColor='';">
            <td align="center"><a href="<?php hecho($move_up_url . $faq["id"])?>"><img src="/webadmin/images/arrowUp.gif" width="11" height="11" border="0" /></a><a href="<?php hecho($move_down_url . $faq["id"])?>"><img src="/webadmin/images/arrowDown.gif" width="11" height="11" border="0" /></a></td>
			<td style="width:25%;padding:5px 10px;">
				<a href="<?= $firstProduct['product_url']?>" target="_blank"><?= $faq['question'] ?> </a>
			</td>
            <td style="width:5%;padding:5px 10px;">
               <i class="far fa-question-circle"
               onmouseover='tt_in(<?= json_encode($faq["answer"]) ?>);'
               onmouseout="tt_out();"></i>
				
			</td>
            <td style="width:25%;text-align:center;">
				<!-- <a href="/webadmin/products_new/edit.php?action=edit&id=<?= $faq['productid']?>" target="_blank"><?= $faq['productid'] ?></a> -->
                <?php
                // Assuming $faq['productid'] contains comma-separated product IDs
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
            <td style="width:10%;text-align:center;">
                <a href="/webadmin/owner/faq/faqcategory.php?action=edit&id=<?= $faq['category']?>" target="_blank"><?= $category_name ?></a>
			</td>
            <!-- <td style="text-align:center"><?= $faq['status'] == 'y' ? 'Active' : 'In-Active' ?></td> -->
            <td style="text-align:center;<?=$faq['status'] == 'y' ? 'background-color: #e9f8bc':'background-color: #ffe6ee'?>"><?= $faq['status'] == 'y' ? 'Active' : 'Deactive' ?></td>
            <?php
			$links = [];		
			$links[] = '<a href="?action=edit&id='.$faq['id'].'" target="_blank">Edit</a>';
			$links[] = '<a onclick="return confirm(\'Are you sure you want to delete this FAQ?\')" href="?action=delete&id='.$faq['id'].'">Delete</a>';
?>
			<td style="text-align:center" width="20%"><?= implode('  ',$links) ?></td>
			<!-- <td style="width:0px;text-align:center;"><a href="?action=edit&id=<?=$faq['id']?>">Edit</a></td> -->
		</tr>
<?php
}
?>
		
	</table>
</div>

<br />

<?php
break;

case 'add':
?>
<form action="?action=do_add" method="post">
    <div><h2 style="text-align:center;">Add/Edit FAQ</h2></div>
    <table class="wide">
        <tr>
            <td class="left_title">Question:</td>
            <td>
                <input name="question" style="width:98%;" type="text" value="" required/>
            </td>
        </tr>
        <tr>
            <td class="left_title">Answer:</td>
            <td>
                <textarea name="answer" style="width:98%;" rows="10" value="" required onkeyup="javascript:textareaAutoSize(this,1,3,25);"></textarea>
            </td>
        </tr>
        <tr style="line-height: 35px;">
            <td class="left_title">&nbsp;
            </td>
            <td>
                <input style="width:96px;" type="button" value="Insert Link" onclick="generic_insert('answer','a',' href=&quot;https://www.WEBSITE.com/&quot; target=&quot;_blank&quot;');" />
                <input style="width:96px;font-weight:bold;" type="button" value="Bold" onclick="generic_insert('answer', 'b');" />
                <input style="width:96px;font-style:italic;" type="button" value="Italic" onclick="generic_insert('answer', 'i');" />
                <input style="width:96px;text-decoration:underline;" type="button" value="Underline" onclick="generic_insert('answer', 'u');" />
                <input style="width:96px;" type="button" value="Bullet Point" onclick="generic_insert('answer','li');" />
                <input type="button" value="YouTube" onclick="javascript:insertYouTubeIframe('answer');" />
                    <!-- <span>Text Colour <input type="color" name="data1[text_color]" value="<?= $data1['font_color'] ?>"></span> -->
                <span>
                    Text Color:
                    <input type="color" id="textColorPicker" name="data1[text_color]" value="#000000" 
                           onmousedown="saveSelection('answer');" 
                           onchange="applyTextColorWithPicker('answer','textColorPicker');">
                </span>
            </td>
        </tr>
        <tr>
            <td class="left_title">Category:</td>
            <td>
                <select name="category" required>
                    <option value="">Not Selected</option>
                    <?=formatHTMLOptions($getAllCategory)?>
                </select>
            </td>
        </tr>
        <tr>
            <td class="left_title">Permission:</td>
            <td>
                <select name="permission" required>
                    <option value="">Not Selected</option>
                    <?=formatHTMLOptions(['every' => 'Live for Everyone','owner'=>'Live for Owners Only'],$faq['permission'])?>
                </select>
            </td>
        </tr>
    </table>
    <?php include '_machine_linked.tpl.php'; ?>
    <div align="center">
        <?= formatHTMLTitle('Status');?>
    </div>
    <table class="table_fields" align="center" >
            <tr>
                <td>
                    <select class="form-control col-sm-9" id="status" name="status"  onchange="this.style.backgroundColor=((this.value=='n')?'rgb(255 0 0)':'#00FF00');" style="width:90%;background:<?=('n' == $row['status']) ?  'rgb(255 0 0);':'#00FF00;';?>">
                        <?= formatHTMLOptions(['y'=>'Active','n'=>'Deactive',], $row['status']) ?>
                    </select>
                </td>
            </tr>
            
        </table>
    <br />
    <p>
        <input type="submit" value="Save Changes" />
    </p>
</form>
<?php
break;

case 'edit':
    $faq = $DB->query_first("SELECT * FROM faq WHERE id='" . $posted['id'] . "' LIMIT 1");
    $faq_product = explode(',', $faq['productid']);
    if($faq_product) {
        rsort($faq_product);
    }
    $faq_product_software = explode(',', $faq['softwareid']);
    if($faq_product_software) {
        rsort($faq_product_software);
    }
?>
<form action="?action=do_edit&id=<?=$posted['id']?>" method="post">
    <div><h2 style="text-align:center;">Add/Edit FAQ</h2></div>
    <table class="wide">
        <tr>
            <td class="left_title">Question:</td>
            <td>
                <input name="question" style="width:98%;" type="text" value="<?=htmlspecialchars($faq['question'])?>" required/>
            </td>
        </tr>
        <tr>
            <td class="left_title">Answer:</td>
            <td>
                <textarea name="answer" style="width:98%;" rows="10" required  onkeyup="javascript:textareaAutoSize(this,1,3,25);"><?=htmlspecialchars($faq['answer'])?></textarea>
            </td>
        </tr>
         <tr>
            <td class="left_title">&nbsp;
            </td>
            <td style="line-height: 35px;">
                <input style="width:96px;" type="button" value="Insert Link" onclick="generic_insert('answer','a',' href=&quot;https://www.WEBSITE.com/&quot; target=&quot;_blank&quot;');" />
                <input style="width:96px;font-weight:bold;" type="button" value="Bold" onclick="generic_insert('answer', 'b');" />
                <input style="width:96px;font-style:italic;" type="button" value="Italic" onclick="generic_insert('answer', 'i');" />
                <input style="width:96px;text-decoration:underline;" type="button" value="Underline" onclick="generic_insert('answer', 'u');" />
                <input style="width:96px;" type="button" value="Bullet Point" onclick="generic_insert('answer','li');" />
                <input type="button" value="YouTube" onclick="javascript:insertYouTubeIframe('answer');" />
                    <!-- <span>Text Colour <input type="color" name="data1[text_color]" value="<?= $data1['font_color'] ?>"></span> -->
                <span>
                    Text Color:
                    <input type="color" id="textColorPicker" name="data1[text_color]" value="#000000" 
                           onmousedown="saveSelection('answer');" 
                           onchange="applyTextColorWithPicker('answer','textColorPicker');">
                </span>
            </td>
        </tr>
        <tr>
            <td class="left_title">Category:</td>
            <td>
                <select name="category" required>
                    <option value="">Not Selected</option>
                    <?=formatHTMLOptions($getAllCategory,$faq['category'])?>
                </select>
            </td>
        </tr>
         <tr>
            <td class="left_title">Permission:</td>
            <td>
                <select name="permission" required>
                    <option value="">Not Selected</option>
                    <?=formatHTMLOptions(['every' => 'Live for Everyone','owner'=>'Live for Owners Only'],$faq['permission'])?>
                </select>
            </td>
        </tr>
    </table>
    <?php include '_machine_linked.tpl.php'; ?>
    <?php //include '_software_linked.tpl.php'; ?>
    <div align="center">
        <?= formatHTMLTitle('Last 5 Changes');?>
    </div>
    <table class="table_grid" align="center" style="width:90%">
        <?php
            $faq_comment = array();
            $query        = $DB->query("SELECT * FROM faq_comment order by created_at DESC limit 5");
            while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) {
                $faq_comment[] = $row;
            }
        ?>
        <tr>
            <th>Date</th>
            <th>User</th>
            <th>Comment</th>
        </tr>
         <?php
            $stafflist =getStaffList();
            $faq_comment = array();
            $query        = $DB->query("SELECT * FROM faq_comment order by created_at DESC limit 5");
            while ($row = $DB->fetch_array($query, MYSQLI_ASSOC)) { ?>
                <tr>
                    <td><?= date('d/m/Y',strtotime($row['created_at'])) ?></td>
                    <td><?= $stafflist[$row['staff_id']] ?></td>
                    <td><?= str_replace("\n","<br>",$row['comment']) ?></td>
                </tr>
            <?php }
        ?>
    </table>
    <div align="center">
	    <?= formatHTMLTitle('Status');?>
    </div>

    <table class="table_grid" align="center" style="width:90%">
            <tr>
                <td>
                    <select class="form-control col-sm-9" id="status" name="status"  onchange="this.style.backgroundColor=((this.value=='n')?'rgb(255 0 0)':'#00FF00');" style="width:90%;background: <?=('n' == $faq['status']) ?  'rgb(255 0 0);':'#00FF00;';?>">
                        <?= formatHTMLOptions(['y'=>'Active','n'=>'Deactive'], $faq['status']) ?>
                    </select>
                </td>
            </tr>
            
        </table>
    <br />
    <p align="center">
        <input type="submit" value="Save Changes" />
    </p>
</form>
<?php
break;
}
?>
</div>
<!-- footer.inc.php -->
 <script>
    function textareaAutoSize(theElem,stepSize,minSize,maxSize)
    {
        if(theElem==null || theElem.rows==null || theElem.cols==null || theElem.value==null) return;
        if(stepSize==null || stepSize < 1) stepSize=5;

        var rowContent=theElem.value.split('\n');
        var rowCount=rowContent.length;

        for(i=0;i < rowContent.length;i++)
            rowCount+=Math.floor(rowContent[i].length/((theElem.cols>0)?theElem.cols:60));

        rowCount=Math.floor(rowCount/stepSize)*stepSize+stepSize;

        if(minSize!=null && minSize>rowCount) rowCount=minSize;
        if(maxSize!=null && maxSize>minSize && maxSize < rowCount) rowCount=maxSize;

        theElem.rows=rowCount;
    }
    function adjustTextareaHeight(elem) 
    {
        elem.style.height = 'auto';
        elem.style.height = (elem.scrollHeight) + 'px';
    }
    function insertYouTubeIframe(textareaId) {
        const youtubeLink = prompt("Enter the YouTube video link:");
        if (!youtubeLink) return;

        const videoIdMatch = youtubeLink.match(/(?:https?:\/\/)?(?:www\.)?youtu(?:be\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|\.be\/)([a-zA-Z0-9_-]{11})/);
        if (!videoIdMatch || !videoIdMatch[1]) {
            alert("Invalid YouTube URL. Please try again.");
            return;
        }
        const videoId = videoIdMatch[1];
        const iframeHTML = `<iframe width="640" height="480"  src="https://www.youtube.com/embed/${videoId}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;

        const textarea = document.querySelector(`[name="${textareaId}"]`);
        if (!textarea) return;

        const { selectionStart, selectionEnd } = textarea;

        if (selectionStart !== null && selectionEnd !== null) {
            textarea.value =
                textarea.value.substring(0, selectionStart) +
                iframeHTML +
                textarea.value.substring(selectionEnd);

            textarea.selectionStart = selectionStart;
            textarea.selectionEnd = selectionStart + iframeHTML.length;
            textarea.focus();
        } else {
            textarea.value += iframeHTML;
        }
    }
    // Function to apply the selected text color
function applyTextColorWithPicker(textareaId , elementId) {
    const colorPicker = document.getElementById(elementId);
    const color = colorPicker.value; // Selected color value
    if (!color) return;

    const textarea = document.querySelector(`[name="${textareaId}"]`);
    if (!textarea) return;

    // Use saved selection range if available
    const selectionStart = savedSelectionStart;
    const selectionEnd = savedSelectionEnd;

    if (selectionStart !== null && selectionEnd !== null && selectionStart !== selectionEnd) {
        const selectedText = textarea.value.substring(selectionStart, selectionEnd);
        const coloredText = `<span style="color:${color};">${selectedText}</span>`;
        textarea.value =
            textarea.value.substring(0, selectionStart) +
            coloredText +
            textarea.value.substring(selectionEnd);

        // Update selection positions and focus the textarea
        textarea.selectionStart = selectionStart;
        textarea.selectionEnd = selectionStart + coloredText.length;
        textarea.focus();
    } else {
        alert("Please select text to apply the color.");
    }
}
function saveSelection(textareaId) {
    const textarea = document.querySelector(`[name="${textareaId}"]`);
    if (textarea) {
        savedSelectionStart = textarea.selectionStart;
        savedSelectionEnd = textarea.selectionEnd;
    }
}
</script>
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/webadmin/footer.inc.php";
?>