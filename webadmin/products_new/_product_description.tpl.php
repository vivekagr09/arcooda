<!-- _product_description.tpl -->

<?php
/**
 * Product Description template
 *
 * Inputs:
 * - $item
 */

if (!isset($item) || !is_array($item)) {
    return;
}

?>
<script type="text/javascript">

//<![CDATA[
function productDescriptionAdd(title, description) {
    if (description == null) description = "";
    var theText = getValue("item[product_details]").split("\n");
    if (theText.length == 1 && theText[0] == "") theText = [];
    var added = false;
    var positions = {};
    for (var i = 0; i < theText.length; i++) {
        if (theText[i] == title) added = true;
        positions[theText[i]] = i;
    }
    var pos = theText.length;
    //This switch always ensures the order Features, Specifications, Includes
    switch (title) {
        case "Features:":
            if (positions["Includes:"] != null) pos = positions["Includes:"];
            if (positions["Specifications:"] != null) pos = positions["Specifications:"];
            break;
        case "Specifications:":
            if (positions["Includes:"] != null) pos = positions["Includes:"];
            break;
    }
    if (positions[title] != null) pos = positions[title] + 1;
    if (!added) theText.splice(pos, 0, title, "-" + description, "");
    else {
        for (; pos < theText.length; pos++)
            if (theText[pos].charAt(0) != '-') break;
        theText.splice(pos, 0, "-" + description);
    }
    setValue("item[product_details]", theText.join("\n"));
}

$(function() {
    $(".item_description_copy").click(function() {
        Product.set("long_desc", Product.get("short_desc"));
    }).fadeIn();
});
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
//modify for youtube
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
//]]>
</script>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionDescription&quot;);return false">Product Description</a>')?>

<div id="sectionDescription">
    <table class="table_fields">
        <tr>
            <th>Short Description :</th>
            <td><?=formatHTMLTextarea("item[short_desc]", $item["short_desc"], true, "", 7)?></td>
        </tr>
        <tr>
            <th>Long Description :</th>
            <td>
                <input class="item_description_copy" type="button" value="Copy Description from Above" style="display:none;" />
                <input style="width:96px;" type="button" value="Insert Link" onclick="generic_insert('item[short_desc]','a',' href=&quot;https://www.WEBSITE.com/&quot; target=&quot;_blank&quot;');" />
                <input style="width:96px;font-weight:bold;" type="button" value="Bold" onclick="generic_insert('item[short_desc]', 'b');" />
                <input style="width:96px;font-style:italic;" type="button" value="Italic" onclick="generic_insert('item[short_desc]', 'i');" />
                <input style="width:96px;text-decoration:underline;" type="button" value="Underline" onclick="generic_insert('item[short_desc]', 'u');" />
                <input style="width:96px;" type="button" value="Bullet Point" onclick="generic_insert('item[short_desc]','li');" />
                <input type="button" value="YouTube" onclick="javascript:insertYouTubeIframe('item[short_desc]');" />
                <span>
                    Text Color:
                    <input type="color" id="textColorPicker1" name="data1[text_color]" value="#000000" 
                           onmousedown="saveSelection('item[short_desc]');" 
                           onchange="applyTextColorWithPicker('item[short_desc]','textColorPicker1');">
                </span>
                <br />
                <?=formatHTMLTextarea("item[long_desc]", $item["long_desc"], true, "", 10)?>
                
            </td>
        </tr>
        <tr><td class="left_title">&nbsp;</td><td>
            <input style="width:96px;" type="button" value="Insert Link" onclick="generic_insert('item[long_desc]','a',' href=&quot;https://www.WEBSITE.com/&quot; target=&quot;_blank&quot;');" />
            <input style="width:96px;font-weight:bold;" type="button" value="Bold" onclick="generic_insert('item[long_desc]', 'b');" />
            <input style="width:96px;font-style:italic;" type="button" value="Italic" onclick="generic_insert('item[long_desc]', 'i');" />
            <input style="width:96px;text-decoration:underline;" type="button" value="Underline" onclick="generic_insert('item[long_desc]', 'u');" />
            <input style="width:96px;" type="button" value="Bullet Point" onclick="generic_insert('item[long_desc]','li');" />
            <input type="button" value="YouTube" onclick="javascript:insertYouTubeIframe('item[long_desc]');" />
                <!-- <span>Text Colour <input type="color" name="data1[text_color]" value="<?= $data1['font_color'] ?>"></span> -->
            <span>
                    Text Color:
                    <input type="color" id="textColorPicker" name="data1[text_color]" value="#000000" 
                           onmousedown="saveSelection('item[long_desc]');" 
                           onchange="applyTextColorWithPicker('item[long_desc]','textColorPicker');">
                </span>
        </td></tr>
        <tr>
            <th>Detailed Specifications :<br/><br/><span style="color:red;padding-top: 5px; font-weight: normal;font-size:9px;"><i>Each "-" will create a bullet point online</i></span></th>
            <td>
                <?=formatHTMLTextarea("item[product_details]", $item["product_details"], true, "", 10)?><br />
               <!--  <input style="width:32%;" type="button" value="Add Feature" onclick="productDescriptionAdd('__Features__:')" />
                <input style="width:32%;" type="button" value="Add Specification" onclick="productDescriptionAdd('__Specifications__:')" />
                <input style="width:32%;" type="button" value="Add Include" onclick="productDescriptionAdd('__Includes__:')" /><br /> -->
            </td>
        </tr>
        <tr>
            <th>Key Features :<br/><br/><span style="color:red;padding-top: 5px; font-weight: normal;font-size:9px;"><i>Only the first 5 features lines will be shown<i><br/><br/><i>Each "-" will create a bullet point online</i></span></th>
            <td>
                <?=formatHTMLTextarea("item[key_features]", $item["key_features"], true, "", 10)?>
            </td>
        </tr>
        <tr>
            <th>What is included :<br/><br/><span style="color:red;padding-top: 5px; font-weight: normal;font-size:9px;"><i>Each "-" will create a bullet point online</i></span></th>
            <td>
                <?=formatHTMLTextarea("item[included_item]", $item["included_item"], true, "", 10)?>
            </td>
        </tr>

        <!-- <tr>
            <th>Packaging :</th>
            <td><?=formatHTMLTextarea("item[notes]", $item["notes"], true, "", 5)?></td>
        </tr> -->
    </table>
    <br /><br />
</div>

<div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionDescription_hideNote">
    Product Description can be viewed by Clicking "Product Description" above<br />
</div>
<script type="text/javascript">
    $(function () {
        $('textarea').each(function () {
            adjustTextareaHeight(this);
        });
    });
</script>
<!-- /_product_description.tpl -->
