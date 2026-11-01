<style type="text/css">
    .cke_notification_warning{
        display: none;
    }
</style>
<?php
include_once __DIR__ . '/common.inc.php';

global $DB;

include_once __DIR__ . '/header.inc.php';

$action = "do_add";
$id = 0;

if (isset($_GET['action']) && !empty($_GET['action'])) {
	if ($_GET['action'] == 'do_add') {
		$action = "do_add";
	} else if ($_GET['action'] == 'do_edit') {
		$action = "do_edit";

		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$id = intval($_GET['id']);
		}

	} else {
		die("Incorrect action");
	}
}

$data = array(
	'name' => '',
	'text' => '',
	'img' =>'',
	'show_front' => ''
);

if ($id > 0) {
	$data = $DB->query_first("SELECT * FROM `icons` WHERE `id` = " . $id);
}
$error = array();

if (!empty($_POST)) {
	//$data = array();

	if (isset($_POST['name']) && !empty($_POST['name'])) {
		$data['name'] = trim($_POST['name']);
	} else {
		$error['name'] = "Please enter the icon name.";
	}

	if (isset($_POST['text']) && !empty($_POST['text'])) {
		$data['text'] = trim($_POST['text']);
	} else {
		$error['text'] = "Please enter text for icon.";
	}

	if (isset($_POST['show_front']) && !empty($_POST['show_front'])) {
		$data['show_front'] = ($_POST['show_front'] == 'y') ? 'y' : 'n';
	} else {
		$data['show_front'] = 'n';
	}
	if (isset($_POST['description']) && !empty($_POST['description'])) {
		$data['description'] = trim($_POST['description']);
	} else {
		$data['description'] = '';
	}
	if (empty($error)) {
		if (isset($_FILES['img']) && !empty($_FILES['img']['name'])) {
			$name = $_FILES['img']['name'];
			$nameChunk = explode('.', $name);
			$ext = end($nameChunk);
			$ext = strtolower($ext);

			if ($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg') {
				$old_img = $data['img'];

				$name = time();

				$uploadName = $name . "." . $ext;

				$data['img'] = '/webadmin/assets/icon/image/' . $uploadName;

				$dest = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/icon/image/' . $uploadName;

				if (!move_uploaded_file($_FILES['img']['tmp_name'], $dest)) {
					$error['img'] = 'Failed to upload file.';
				}

				$destination = imagecreatetruecolor(96, 96);

				if ($ext == 'jpg' || $ext == 'jpeg') {
					$source = imagecreatefromjpeg($dest);
				} else {
					$source = imagecreatefrompng($dest);
				}

				list($width, $height) = getimagesize($dest);

				if ($ext == 'png') {
					imagealphablending($destination, false);
					imagesavealpha($destination, true);
					$transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
					imagefilledrectangle($destination, 0, 0, 96, 96, $transparent);
				}

				imagecopyresampled($destination, $source, 0, 0, 0, 0, 96, 96, $width, $height);

				if ($ext == 'jpg' || $ext == 'jpeg') {
					imagejpeg($destination, $dest, 100);
				} else {
					imagepng($destination, $dest);
				}

				// Remove previouse image if edit
				if ($action == 'do_edit') {
					unlink($_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/icon/image/' . $old_img);
				}
			} else {
				$error['img'] = "Invalid image format.";
			}
		} else {
			if ($action == 'do_add') {
				$error['img'] = "Please choose image for icon.";
			}
		}
	}

	if (empty($error)) {
		if ($action == "do_add") {
			$DB->query(
				sprintf(
					"INSERT INTO `icons` (`name`, `text`, `img`,`description`, `show_front`) VALUES (%s, %s, %s, %s, %s)",
					$DB->escape($data['name']),
					$DB->escape($data['text']),
					$DB->escape($data['img']),
					$DB->escape($data['description']),
					$DB->escape($data['show_front'])
				)
			);

			echo "<script>window.location.href='/webadmin/icon/'</script>";
		} else {
			$DB->query(
				sprintf(
					"UPDATE `icons` set `name` = %s, `text` = %s, `img` = %s,`description` = %s, `show_front` = %s WHERE `id` = %d",
					$DB->escape($data['name']),
					$DB->escape($data['text']),
					$DB->escape($data['img']),
					$DB->escape($data['description']),
					$DB->escape($data['show_front']),
					$id
				)
			);
			deleteMysqlCacheData('icons',$id);
		}
	}
}
function url($name,$id)
{
    return sprintf(
        '/icon/%s-%d',
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
			height:100,
			width:600,

			language : 'en',
			baseHref : <?php jecho($_GLOBALS["site_settings"]["home_uri"])?>,
			allowedContent: true,
			format_h2 : {
				element : "h2",
				attributes : {
					style : "background-color:#eee; border:1px solid #ccc; padding:5px 10px; color:#080; font-size:24px; font-weight:bold;"
				}
			},
			contentsCss : "https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css",
			bodyClass : "page_body",
			bodyId : "bd"
		});

});
//]]>
</script>
<br>

<form action="?action=<?= $action ?>&id=<?= $id ?>" method="post" enctype="multipart/form-data">
	<center>
		<table border="0" cellpadding="2">
			<tr>
				<td><b>Icon Name</b></td>
				<td>
					<input type="text" name="name" value="<?= $data['name'] ?>" size="20" style="width: 400px;" maxlength="20"/> Character limit 20
<?php
						if ($id > 0) {
							echo '<a href="'.url($data['name'],$id).'.html" style="float:right;margin-left: 28px;" target="_blank">Online Link</a>';
						}
?>
					<?php if (isset($error['name'])) : ?>
						<br><small style="color: red;"><?= $error['name'] ?></small>
					<?php endif; ?>
				</td>
			</tr>

			<tr><td colspan="2"><br/></td></tr>

			<tr>
				<td><b>Icon Text</b></td>
				<td>
					<input type="text" name="text" value="<?= $data['text'] ?>" size="20" style="width: 400px;" maxlength="60"/>
					Character limit 60
					<?php if (isset($error['text'])) : ?>
						<br><small style="color: red;"><?= $error['text'] ?></small>
					<?php endif; ?>
				</td>
			</tr>

			<tr><td colspan="2"><br/></td></tr>

			<tr><td colspan="2"><br/></td></tr>

			<tr>
				<td><b>Image</b></td>
				<td>
					<?php if($action == 'do_edit') : ?>
						<img src="<?= $data['img'] ?>" width="96" /> <br/>
					<?php endif; ?>

					<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
					<input type="file" name="img" />
					<?php if (isset($error['img'])) : ?>
						<br><small style="color: red;"><?= $error['img'] ?></small>
					<?php endif; ?>
				</td>
			</tr>

			<tr><td colspan="2"><br/></td></tr>
			<tr>
				<td><b>Description</b></td>
				<td>
					<textarea id="item_content" name="description" value="<?= $data['description'] ?>" size="20" style="width: 600px;"><?= $data['description'] ?></textarea>
					<!-- <?php if (isset($error['text'])) : ?>
						<br><small style="color: red;"><?= $error['text'] ?></small>
					<?php endif; ?> -->
				</td>
			</tr>

			<tr><td colspan="2"><br/></td></tr>
			<tr>
				<td><b>Show on front page</b></td>
				<td>
					<select name="show_front">
						<option value="y" <?= ($data['show_front'] == 'y') ? 'selected' : '' ?>>Yes</option>
						<option value="n" <?= ($data['show_front'] == 'n') ? 'selected' : '' ?>>No</option>
					</select>
					<?php if (isset($error['show_front'])) : ?>
						<br><small style="color: red;"><?= $error['show_front'] ?></small>
					<?php endif; ?>
				</td>
			</tr>

			<tr><td colspan="2"><br/></td></tr>

			<tr>
				<td align="center" colspan="2">
					<input type="submit" value="Save" />
				</td>
			</tr>
		</table>
	</center>
</form>

<script type="text/javascript" src="//code.jquery.com/jquery-1.9.0.min.js"></script>

<?php
include_once __DIR__ . '/footer.inc.php';
