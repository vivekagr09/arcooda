<style type="text/css">
    .cke_notification_warning{
        display: none;
    }
</style>
<?php
// @todo replace the html editor with a markdown editor

include_once __DIR__ . '/common.inc.php';

global $DB;

include_once __DIR__ . '/header.inc.php';

$action = "do_add";
$id     = 0;

if (isset($_GET['action']) && !empty($_GET['action'])) {
    if ('do_add' == $_GET['action']) {
        $action = "do_add";
    } else if ('do_edit' == $_GET['action']) {
        $action = "do_edit";

        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = intval($_GET['id']);
        }
    } else {
        die("Incorrect action");
    }
}

$data = brandDefault();
if ($id > 0) {
    $data = brandFromDatabase($id);
}
$error = array();

if (!empty($_POST)) {
    if (isset($_POST['name']) && !empty($_POST['name'])) {
        $data['name'] = trim($_POST['name']);
    } else {
        $error['name'] = "Please enter the icon name.";
    }

    if (isset($_POST['text']) && !empty($_POST['text'])) {
        $data['text'] = trim($_POST['text']);
    } else {
        $error['text'] = "Please enter text for brand.";
    }

    if (isset($_POST['show_front']) && !empty($_POST['show_front'])) {
        $data['show_front'] = ('y' == $_POST['show_front']) ? 'y' : 'n';
    } else {
        $data['show_front'] = 'n';
    }

    if (isset($_POST['description']) && !empty($_POST['description'])) {
        $data['description'] = trim($_POST['description']);
    } else {
        $data['description'] = '';
    }

    if (isset($_POST['website']) && !empty($_POST['website'])) {
        $data['website'] = trim($_POST['website']);
    } else {
        $data['website'] = '';
    }

    if (empty($error)) {
        if (isset($_FILES['img']) && !empty($_FILES['img']['name'])) {
            $name      = $_FILES['img']['name'];
            $file_temp = $_FILES['img']['tmp_name'];

            $nameChunk = explode('.', $name);
            $ext       = end($nameChunk);
            $ext       = strtolower($ext);

            if ('png' == $ext || 'jpg' == $ext || 'jpeg' == $ext) {
                $old_img     = $data['img'];
                $name        = time();
                $uploadName  = $name . "." . $ext;
                $data['img'] = $uploadName;
                $dest        = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/brand/img/' . $uploadName;

                if (!move_uploaded_file($_FILES['img']['tmp_name'], $dest)) {
                    $error['img'] = 'Failed to upload file.';
                }
            } else {
                $error['img'] = "Invalid image format.";
            }
        } else {
            if ('do_add' == $action) {
                $error['img'] = "Please choose image for icon.";
            }
        }
    }

    if (empty($error)) {
        if ("do_add" == $action) {
            $id = brandToDatabase($data);
            deleteMysqlCacheData('brand',$id);
            echo "<script>window.location.href='/webadmin/brand/'</script>";
        } else {
            brandToDatabase($data);
            deleteMysqlCacheData('brand',$data['id']);
        }
    }
}

function url($name, $id)
{
    return sprintf(
        '/brand/%s-%d',
        urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $name), '-')),
        $id
    );
}
?>
<script src="https://cdn.ckeditor.com/4.13.1/standard/ckeditor.js"></script>
<script type="text/javascript">
//<![CDATA[
$(function() {
    content_editor = CKEDITOR.replace('item_content', {
        height         : 100,
        width          : 600,
        language       : 'en',
        baseHref       : <?=json_encode($_GLOBALS["site_settings"]["home_uri"])?>,
        allowedContent : true,
        format_h2      : {
            element    : "h2",
            attributes : {
                style : "background-color:#eee; border:1px solid #ccc; padding:5px 10px; color:#080; font-size:24px; font-weight:bold;"
            }
        },
        contentsCss    : "https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css",
        bodyClass      : "page_body",
        bodyId         : "bd"
    });
});
//]]>
</script>
<br />

<form action="?action=<?=$action?>&id=<?=$id?>" method="post" enctype="multipart/form-data">
    <table border="0" cellpadding="2" style="margin:auto">
        <tr>
            <td><b>Brand Name</b></td>
            <td>
                <input type="text" name="name" value="<?=$data['name']?>" size="20" style="width: 400px;" />
<?php
if ($data['website']) {
    if (substr($data['website'], 0, 4) != "http") {
        $website = 'https://' . $data['website'];
    } else {
        $website = $data['website'];
    }
    echo '<a href="' . $website . '" style="float:right;margin-left: 28px;" target="_blank">Online Link</a>';
}

if (isset($error['name'])) {
?>
                    <br />
                    <small style="color: red;"><?=$error['name']?></small>
<?php
}
?>
            </td>
        </tr>
        <tr><td colspan="2"><br /></td></tr>
        <tr>
            <td><b>Brand Text</b></td>
            <td>
                <input type="text" name="text" value="<?=$data['text']?>" size="20" style="width: 400px;"/>
                <?php if (isset($error['text'])): ?>
                    <br /><small style="color: red;"><?=$error['text']?></small>
                <?php endif?>
            </td>
        </tr>
        <tr><td colspan="2"><br /></td></tr>
        <tr><td colspan="2"><br /></td></tr>
        <tr>
            <td><b>Image</b></td>
            <td>
            <div>
<?php
if ('do_edit' == $action) {
?>
                    <a href="/webadmin/uploads/brand/img/<?=$data['img']?>" target="_blank"><img src="/webadmin/uploads/brand/img/<?=$data['img']?>" width="108" height="90"/></a> <br />
<?php
}
?>
                <span style="color:red"><i>Image to be 16*9 minimum 200 size (prefer over 500)</i></span>
            </div>
            <input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
            <input type="file" name="img" />
<?php
if (isset($error['img'])) {
?>
            <br />
            <small style="color: red;"><?=$error['img']?></small>
<?php
}
?>
            </td>
        </tr>

        <tr><td colspan="2"><br /></td></tr>
        <tr>
            <td><b>Description</b></td>
            <td>
                <textarea id="item_content" name="description" value="<?=$data['description']?>" size="20" style="width: 600px;"><?=$data['description']?></textarea>
                <!-- <?php if (isset($error['text'])): ?>
                    <br /><small style="color: red;"><?=$error['text']?></small>
                <?php endif?> -->
            </td>
        </tr>
        <tr><td colspan="2"><br /></td></tr>
        <tr>
            <td><b>Website Link</b></td>
            <td>
                <input type="text" name="website" value="<?=$data['website']?>" size="150" style="width: 400px;" maxlength="150"/> Character limit 150
                <?php if (isset($error['website'])): ?>
                    <br /><small style="color: red;"><?=$error['website']?></small>
                <?php endif?>
            </td>
        </tr>
        <tr><td colspan="2"><br /></td></tr>
        <tr>
            <td><b>Show on front page</b></td>
            <td>
                <select name="show_front">
                    <option value="y" <?=('y' == $data['show_front']) ? 'selected' : ''?>>Yes</option>
                    <option value="n" <?=('n' == $data['show_front']) ? 'selected' : ''?>>No</option>
                </select>
                <?php if (isset($error['show_front'])): ?>
                    <br /><small style="color: red;"><?=$error['show_front']?></small>
                <?php endif?>
            </td>
        </tr>
        <tr><td colspan="2"><br /></td></tr>
        <tr>
            <td align="center" colspan="2">
                <input type="submit" value="Save" />
            </td>
        </tr>
    </table>
</form>

<script type="text/javascript" src="//code.jquery.com/jquery-1.9.0.min.js"></script>

<?php

include_once __DIR__ . '/footer.inc.php';
