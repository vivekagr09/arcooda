<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
<script type="text/javascript">
function sectionPreview(theFrm,email)
{
    if (email)
    {
        var data=$(theFrm).serializeArray();
        data.push({'name':'send_copy','value':'true'});
        $.post('preview.php',data,function(data,textStatus) {
            alert(textStatus);
        });
    }
}
</script>
<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

global $_GLOBALS;
$id = false;
if(isset($_GET['id'])){
    $id = $_GET['id'];   
    $item = mediaPartnedNewsletterFromDatabase($id);
}
if($id == false){
   $item=array(
        'member_type' => $posted['member_type'],
        'category' => $posted['category'],
        'country' => implode(',', $posted['country']),
    ); 
}

$criteria = [];
if($item['category'] > 0){
   $criteria['category']  = $item['category'];
}
if($item['member_type']){
   $criteria['member_type']  = $item['member_type'];
}
if($item['country']){
   $criteria['country']  = explode(',',$item['country']);
}
$criteria['active'] = 'y';
$item_query = socialMediaPartnerQuery($criteria,[], []);
$item_list  = socialMediaPartnerSort($item_query, $sort_by);
moduleLoad('shipinvoice');
moduleLoad('template');
$invoice_labels  = $DB->query_first("SELECT * FROM `highwaygroup`.`company_info` WHERE `company_id` = " . intval($invoice['site_id']) . " LIMIT 1", MYSQLI_ASSOC);
$email_templates = invoiceEmailTemplatesList();
$templater       = new Template();
$vars            = invoiceTemplateVars($invoice);
foreach ($vars as $key => $value) {
    if (trim($value) == '') {
        unset($vars[$key]);
    }
}

if ($invoice['site_id'] < 1) {
    foreach (getTableColumns('`highwaygroup`.`company_info`') as $key) {
        unset($vars['[' . strtoupper($key) . ']']);
    }
}

if ($invoice_id < 1) {
    unset($vars['[INVOICE_NUMBER]']);
}

$vars['[STAFF_NAME]'] = $_GLOBALS['staff']['name'];
$templater->set_vars($vars);
foreach ($email_templates as $template_id => $template) {
    foreach ($template as $key => $value) {
        $templater->set_contents($value);
        $email_templates[$template_id][$key] = $templater->apply();
    }
}

$email_names    = array();
$email_subjects = array();
$email_bodies   = array();
foreach ($email_templates as $template) {
    $email_names[]    = $template['template_name'];
    $email_subjects[] = $template['template_subject'];
    $email_bodies[]   = $template['template_text'];
}
$invoice['content'] = reset($email_bodies);
function email_row($ecount = '[EMAIL_COUNT]', $contact = array('email' => '[EMAIL_ADDRESS]'))
{
    ob_start();
?>
    <tr name="email_row[<?=$ecount?>]">
        <td class="left_title">eMail To:</td>
        <td><input type="checkbox" name="flag_email_customer[<?=$ecount?>]" /> <select name="flag_email_bcc[<?=$ecount?>]" style="width:auto;"><option value="cc">CC</option><option value="bcc">BCC</option></select> <input type="hidden" name="email_customer[<?=$ecount?>]" value="<?=htmlspecialchars($contact['email'])?>" /> <input type="hidden" name="send_email_cc[<?=$ecount?>]" value="<?=htmlspecialchars($contact['send_email_cc'])?>" /> <?=htmlspecialchars($contact['name'])?> &lt;<?=htmlspecialchars($contact['email'])?>&gt;</td>
    </tr>
<?php
return ob_get_clean();
}
?>

<script type="text/javascript">
//<![CDATA[
var firstEmail = 1;
var lastEmail = <?=max(1, (count($member_contacts) + 1))?>;




var autoTick = true;
var textChange = false;
function emailChangeTemplate(new_id)
{
    var product             = '';
    var template_subjects = <?php jecho(array_values($email_subjects))?>;
    var templates = <?php jecho(array_values($email_bodies))?>;
    // updated by swapnil 1st Aug 2023
    if(new_id == 0)
    {
        product         = ' ' + $("input[name='product_name[1]']").val();
    }

    if (autoTick)
    {
        var checkbox = getElement('flag_email_customer[1]');
        if (checkbox != null) checkbox.checked = true;
        for(var i = firstEmail;i <= lastEmail;i++)
        {
            var checkbox = getElement('flag_email_customer[' + String(i) + ']');
            var auto_check = getValue('send_email_cc[' + String(i) + ']');
            if(checkbox != null && auto_check != 'n') checkbox.checked = true;
        }
        autoTick = false;
    }

    if (template_subjects[new_id] != null)
    {
        setValue('subject', template_subjects[new_id]+product);
        setValue('content', templates[new_id]);
        var msgbox = getElement('content');
        if (msgbox != null) textareaAutoSize(msgbox, 1, 10);
    }
    textChange = true;
}


function textChanged(value)
{
    if (value != '') textChange = true;
}

$(function() {
    var $button = $("<a href=\"#\">Add</a>").click(function() {
        lastEmail++;
        $("[name='flag_attachments_customer']").closest("tr").before("<tr><td class=\"left_title\">eMail To:</td><td><input type=\"checkbox\" name=\"flag_email_customer[" + lastEmail + "]\" /> <select name=\"flag_email_bcc[" + lastEmail + "]\" style=\"width:auto;\"><option value=\"cc\">CC</option><option value=\"bcc\">BCC</option></select> <input type=\"text\" name=\"email_customer[" + lastEmail + "]\" size=\"48\" value=\"\" /></td></tr>");
        return false;
    });
    $("[name='flag_email_customer[0]']").closest("td").append($button);
});
//]]>
</script>
<script type="text/javascript">
    $(function(){

        //get the section name from hash
        var sectionName = window.location.hash.slice(1);

        //then show the section
        $('#' + sectionName ).show();
    })
</script>
<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionMemberNotes\');return false">Description</a>')?>
<div id="sectionMemberNotes">
    <a href="/webadmin/socialmedia/list.php?action=list&member_type=<?= $item['member_type'] ?>&category=<?= $item['category'] ?>&country=<?= $item['country'] ?>&active=y" target="_blank"><p style="    text-align: center; font-size: 15px;">Found <?php echo count($item_list) ?> members</p></a>
<form id="send_update_form" method="post" action="index.php?action=send_update"> 
<?php
    if(isset($item['id']) && !isset($_GET['makenew'])){ ?>
      <input type="hidden" name="id" value="<?= $item['id']?>" />
   <?php  }
?> 
<input type="hidden" name="member_type" value="<?php echo $item['member_type'] ?>" />  
<input type="hidden" name="category" value="<?php echo $item['category'] ?>" /> 
<input type="hidden" name="country" value="<?php echo $item['country'] ?>" /> 
<table class="table_fields" id="email_table">
    <tr>
        <th>Email Templates:</td>
        <td>
            <select onchange="javascript:emailChangeTemplate(this.value);" style="width:80%;" name="template_id">
                <option value=""></option>
                <?=formatHTMLOptions($email_names, $item['template_id'] ?? -1)?>
            </select>
        </td>
    </tr>
 
    <tr>
        <th>Subject</th>
        <td><input type="text" name="subject" style="width:98%;" value="<?= $item['subject'] ?>" class="wide" /></td>
    </tr>
    <tr>
        <th >Message</th>
        <td><textarea class="wide" name="content" rows="<?=max(10, count(explode("\n", $invoice['content'])))?>" onkeyup="javascript:textareaAutoSize(this,1,10);" onchange="if(this.value!='') emailChangeTemplate(); textChanged(this.value);"><?=$item['content']?></textarea>
            <br />
                <input type="button" value="Insert Link" onclick="javascript:link_insert('content');" />
                <input type="button" value="Bold" onclick="javascript:generic_insert('content','b');" />
                <input type="button" value="Italics" onclick="javascript:generic_insert('content','i');" />
                <input type="button" value="Underline" onclick="javascript:generic_insert('content','u');" />
                <!-- <input type="button" value="Text Color" onclick="javascript:applyTextColor('content');" /> -->
                
                <input style="width:96px;" type="button" value="Bullet Point" onclick="generic_insert('content','li');" />
                <input type="button" value="YouTube" onclick="javascript:insertYouTubeIframe('content');" />
                <span>
                    Text Color:
                    <input type="color" id="textColorPicker" name="data1[text_color]" value="#000000" 
                           onmousedown="saveSelection('content');" 
                           onchange="applyTextColorWithPicker('content','textColorPicker');">
                </span>
        </td>
    </tr>
    <tr>
          <?php
            $filedata = [];
                if($item['attached_file']){
                    $dir= socialMediaPartnerGetOptionList('screenshot_path');
                    foreach(explode(',',$item['attached_file']) as $key => $file) {
                         $fdata = explode('-', $file);
                         $filedata[] = [
                            'name' => basename($fdata[1]),
                            'size' => filesize($dir.$fdata[0]),
                            'type' => mime_content_type($dir.$fdata[0]),
                            'server' =>[
                                 'fileName' => basename($fdata[1]),
                                 'filePath' => 'https://www.arcooda.com/webadmin/socialmedia/screenshot/'.$fdata[0]
                            ]
                        ];
                      echo '<input type="hidden" name="attached_file[]" value="'.$fdata[0].'" />';   
                    }
                }
            ?>
        <th>Add Attachment</th>
        <td class="wide">
            <div style="width: 100%;margin-left: auto; margin-right: auto;margin-bottom: 20px;">
            <?php include 'addBulkImage.php'; ?>
            </div>
        </td>
      
    </tr>
</table>
<br /><br />
<div style="text-align: center;">
        <input style="color:#080;font-weight:bold;" type="button" value="Send media update" onclick="if(confirm('Are you sure you are ready to send this media update?')) sectionSendNewsletter()">
        <?php
        if($_GLOBALS['staff']['email'] != '')
        {
        ?>
            <input type="button" value="Send Preview to '<?php hecho($_GLOBALS['staff']['email']) ?>'" onclick="sectionPreview(this.form,true)" />
        <?php
            }
        ?>
    </div>
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionMemberNotes_hideNote">
    Member Notes can be viewed by Clicking "Member Notes" above<br />
</div>



<script type="text/javascript">
//<![CDATA[
var short_staff_list_options = <?=json_encode(formatHTMLOptions(invoiceGetOptionList('staff_id'), "", false))?>;
function addStaffEmail() {
    var table = getElement('staff_email');
    if (table === null || !table) return false;
    var newRow = addTableRow(table, '');
    var newCell = addTableCell(newRow, 'Message To');
    newCell.className = 'left_title';
    newCell = addTableCell(newRow, '<select name="email_target_staff[]"><option value=""></option>' + short_staff_list_options + '</select>');
    return false;
}
function generic_insert(name, tag) {
    var theElem = document.getElementsByName(name);
    if (theElem.length == 0) return;
    theElem = theElem[0];
    if (theElem.selectionStart != null && theElem.selectionEnd != null) {
        var a = theElem.selectionStart;
        var b = theElem.selectionEnd;
        theElem.value = theElem.value.substring(0, a) + '<' + tag + '>' + theElem.value.substring(a, b) + '</' + tag + '>' + theElem.value.substring(b);
        theElem.selectionStart = a + 2 + tag.length;
        theElem.selectionEnd = 2 + tag.length + b;
        theElem.focus();
    }
    else {
        var repltext = null;
        var seltext = (document.all) ? document.selection.createRange() : document.getSelection();
        var selit = (document.all) ? document.selection.createRange().text : document.getSelection();
        if (seltext != null && selit.length >= 1)
            seltext.text = '<' + tag + '>' + seltext.text + '</' + tag + '>';
        else
            theElem.value += '<' + tag + '>BOLDTEXT</' + tag + '>';
    }
}
function link_insert(name) {
    var theElem = document.getElementsByName(name);
    if (theElem.length == 0) return;
    theElem = theElem[0];
    if (theElem.selectionStart != null && theElem.selectionEnd != null) {
        var a = theElem.selectionStart;
        var b = theElem.selectionEnd;
        theElem.value = theElem.value.substring(0, a) + '<a href="http://www.WEBSITE" target="_blank">' + theElem.value.substring(a, b) + '</a>' + theElem.value.substring(b);
        theElem.selectionStart = a + 20;
        theElem.selectionEnd = a + 27;
        theElem.focus();
    }
    else {
        var repltext = null;
        var seltext = (document.all) ? document.selection.createRange() : document.getSelection();
        var selit = (document.all) ? document.selection.createRange().text : document.getSelection();
        if (seltext != null && selit.length >= 1)
            seltext.text = '<a href="http://www.WEBSITE" target="_blank">' + seltext.text + '</a>';
        else
            theElem.value += '<a href="http://www.WEBSITE" target="_blank">LINK</a>';
    }
}
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
function saveSelection(textareaId) {
    const textarea = document.querySelector(`[name="${textareaId}"]`);
    if (textarea) {
        savedSelectionStart = textarea.selectionStart;
        savedSelectionEnd = textarea.selectionEnd;
    }
}
function sectionSendNewsletter()
{
    $('form#send_update_form').eq(0).attr('action','index.php?action=send_update_to_partner').each(function() {this.submit();});
}
//]]>
</script>



<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionMessageStaff_hideNote">
    Messages to Staff section can be viewed by Clicking "Message To Staff" above<br />
</div>
<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionAdminNotes_hideNote">
    All Administration Notes can be viewed by Clicking "Administration Notes" above<br />
</div>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
