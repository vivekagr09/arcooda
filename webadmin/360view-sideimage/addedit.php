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
	'img_left' =>'',
	'img_right' =>'',
	'active' => ''
);

if ($id > 0) {
	$data = $DB->query_first(sprintf(
		"SELECT * FROM %s WHERE `%s` = %d LIMIT 1",
		"`360_side_images`",
		"id",
		$id
	), MYSQLI_ASSOC);
}
$error = array();

if (!empty($_POST)) {
	//$data = array();

	if (isset($_POST['name']) && !empty($_POST['name'])) {
		$data['name'] = trim($_POST['name']);
	} else {
		$error['name'] = "Please enter the icon name.";
	}

	if (isset($_POST['active']) && !empty($_POST['active'])) {
		$data['active'] = ($_POST['active'] == 'y') ? 'y' : 'n';
	} else {
		$data['active'] = 'n';
	}
	if (empty($error)) {
		if (isset($_FILES['img_left']) && !empty($_FILES['img_left']['name'])) {
			$name = $_FILES['img_left']['name'];
			$nameChunk = explode('.', $name);
			$ext = end($nameChunk);
			$ext = strtolower($ext);

			if ($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg') {
				$old_img = $data['img_left'];

				$name = time().'_'.rand();

				$uploadName = $name . "." . $ext;

				$data['img_left'] = '/webadmin/assets/icon/image/' . $uploadName;

				$dest = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/icon/image/' . $uploadName;

				if (!move_uploaded_file($_FILES['img_left']['tmp_name'], $dest)) {
					$error['img_left'] = 'Failed to upload file.';
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
				$error['img_left'] = "Invalid image format.";
			}
		} else {
			if ($action == 'do_add') {
				$error['img_left'] = "Please choose image for left side.";
			}
		}
		if (isset($_FILES['img_right']) && !empty($_FILES['img_right']['name'])) {
			$name = $_FILES['img_right']['name'];
			$nameChunk = explode('.', $name);
			$ext = end($nameChunk);
			$ext = strtolower($ext);

			if ($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg') {
				$old_img = $data['img_right'];

				$name = time().'_'.rand();

				$uploadName = $name . "." . $ext;

				$data['img_right'] = '/webadmin/assets/icon/image/' . $uploadName;

				$dest = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/icon/image/' . $uploadName;

				if (!move_uploaded_file($_FILES['img_right']['tmp_name'], $dest)) {
					$error['img_right'] = 'Failed to upload file.';
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
				$error['img_right'] = "Invalid image format.";
			}
		} else {
			if ($action == 'do_add') {
				$error['img_right'] = "Please choose image for right side.";
			}
		}
	}

	if (empty($error)) {
		if ($action == "do_add") {
			$DB->query(sprintf(
				"INSERT INTO %s (`name`, `img_left`, `img_right`, `active`) VALUES (%s, %s, %s, %s)",
				"`360_side_images`",
				$DB->escape($data['name']),
				$DB->escape($data['img_left']),
				$DB->escape($data['img_right']),
				$DB->escape($data['active'])
			));

			echo "<script>window.location.href='/webadmin/360view-sideimage/'</script>";
		} else {
			$DB->query(sprintf(
				"UPDATE %s SET `name` = %s, `img_left` = %s, `img_right` = %s, `active` = %s WHERE `%s` = %d LIMIT 1",
				"`360_side_images`",
				$DB->escape($data['name']),
				$DB->escape($data['img_left']),
				$DB->escape($data['img_right']),
				$DB->escape($data['active']),
				"id",
				$id
			));
		}
	}
}
function url($name,$id)
{
    return sprintf(
        '/brand/%s-%d',
        urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $name), '-')),
        $id
    );
}
?>
<br>

<form action="?action=<?= $action ?>&id=<?= $id ?>" method="post" enctype="multipart/form-data">
	<center>
		<table border="0" cellpadding="2">
			<tr>
				<td><b>Name</b></td>
				<td>
					<input type="text" name="name" value="<?= $data['name'] ?>" size="20" style="width: 400px;" /> 
					<?php if (isset($error['name'])) : ?>
						<br><small style="color: red;"><?= $error['name'] ?></small>
					<?php endif; ?>
				</td>
			</tr>

			<tr><td colspan="2"><br/></td></tr>
			<tr><td colspan="2"><br/></td></tr>

			<tr>
				<td><b>Image Left</b></td>
				<td>
				<div>
					<?php if($action == 'do_edit') : ?>
	
						<img src="<?= $data['img_left'] ?>" width="96" /> <br/>
											
					<?php endif; ?>
					<span style="color:red"><i>Image to be 16*9 minimum 200 size (prefer over 500)</i></span>
				</div>
					<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
					<input type="file" name="img_left" />
					<?php if (isset($error['img_left'])) : ?>
						<br><small style="color: red;"><?= $error['img_left'] ?></small>
					<?php endif; ?>

				</td>
			</tr>
			<tr><td colspan="2"><br/></td></tr>
			<tr><td colspan="2"><br/></td></tr>

			<tr>
				<td><b>Image Right</b></td>
				<td>
				<div>
					<?php if($action == 'do_edit') : ?>
	
						<img src="<?= $data['img_right'] ?>" width="96" /> <br/>
											
					<?php endif; ?>
					<span style="color:red"><i>Image to be 16*9 minimum 200 size (prefer over 500)</i></span>
				</div>
					<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
					<input type="file" name="img_right" />
					<?php if (isset($error['img_right'])) : ?>
						<br><small style="color: red;"><?= $error['img_right'] ?></small>
					<?php endif; ?>

				</td>
			</tr>
			<tr><td colspan="2"><br/></td></tr>
			<tr>
				<td><b>Active</b></td>
				<td>
					<select name="active">
						<option value="y" <?= ($data['active'] == 'y') ? 'selected' : '' ?>>Yes</option>
						<option value="n" <?= ($data['active'] == 'n') ? 'selected' : '' ?>>No</option>
					</select>
					<?php if (isset($error['active'])) : ?>
						<br><small style="color: red;"><?= $error['active'] ?></small>
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
