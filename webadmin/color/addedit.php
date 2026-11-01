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
	'colour' => '',
	'code3' => '',
	'color_val' =>'',
);

if ($id > 0) {
	$data = $DB->query_first("SELECT * FROM `product_colour` WHERE `id` = " . $id);
}
$error = array();

if (!empty($_POST)) {
	//$data = array();

	if (isset($_POST['colour']) && !empty($_POST['colour'])) {
		$data['colour'] = trim($_POST['colour']);
	} else {
		$error['colour'] = "Please enter the colour name.";
	}

	if (isset($_POST['code3']) && !empty($_POST['code3'])) {
		$data['code3'] = trim($_POST['code3']);
	} else {
		$error['code3'] = "Please enter color code.";
	}

	if (isset($_POST['color_val']) && !empty($_POST['color_val'])) {
		$data['color_val'] = trim($_POST['color_val']);
	} else {
		$error['color_val'] = "Please enter color value.";
	}


	if (empty($error)) {
		if ($action == "do_add") {
			$DB->query(
				sprintf(
					"INSERT INTO `product_colour` (`colour`, `code3`, `color_val`) VALUES (%s, %s, %s)",
					$DB->escape($data['colour']),
					$DB->escape($data['code3']),
					$DB->escape($data['color_val'])
				)
			);

			echo "<script>window.location.href='/webadmin/color/'</script>";
		}else {
			$DB->query(
				sprintf(
					"UPDATE `product_colour` set `colour` = %s, `code3` = %s,`color_val` = %s WHERE `id` = %d",
					$DB->escape($data['colour']),
					$DB->escape($data['code3']),
					$DB->escape($data['color_val']),
					$id
				)
			);
		}
	}
}
?>
<br>

<form action="?action=<?= $action ?>&id=<?= $id ?>" method="post">
	<center>
		<table border="0" cellpadding="2">
			<tr>
				<td><b>Colour Name</b></td>
				<td>
					<input type="text" name="colour" value="<?= $data['colour'] ?>" size="20" style="width: 400px;" />
				<?php if (isset($error['colour'])) : ?>
						<br><small style="color: red;"><?= $error['colour'] ?></small>
					<?php endif; ?>
				</td>
			</tr>

			<tr><td colspan="2"><br/></td></tr>

			<tr>
				<td><b>Colour Code</b></td>
				<td>
					<input type="text" name="code3" value="<?= $data['code3'] ?>" size="20" style="width: 400px;" />
					<?php if (isset($error['code3'])) : ?>
						<br><small style="color: red;"><?= $error['code3'] ?></small>
					<?php endif; ?>
				</td>
			</tr>

			<tr><td colspan="2"><br/></td></tr>
			<tr>
				<td><b>Colour Vlaue</b></td>
				<td>
					<input type="color" name="color_val" value="<?= $data['color_val'] ?>" size="20" style="width: 400px;" />
					<?php if (isset($error['color_val'])) : ?>
						<br><small style="color: red;"><?= $error['color_val'] ?></small>
					<?php endif; ?>
				</td>
			</tr>

			<tr><td colspan="2"><br/></td></tr>
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
