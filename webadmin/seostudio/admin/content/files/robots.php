<?php
require "../../includes/init.php";
$page->setPath("../../../")->requirePermission('admin-access')->setPage(11)->header('content');

$targetFile = $studio->basedir . DIRECTORY_SEPARATOR . 'robots.txt';
$content = file_exists($targetFile) ? file_get_contents($targetFile) : '';
$errors = [];

function checkFile($path) {
	global $errors;

	$dir = dirname($path);

	if (!file_exists($dir)) {
		$dir = dirname($dir);
	}

	if (file_exists($path)) {
		if (!is_readable($path)) {
			$errors[] = "File $path is not readable";
		}
		else if (!is_writable($path)) {
			$errors[] = "File $path is not writable";
		}
	}
	else if (file_exists($dir)) {
		if (!is_readable($dir)) {
			$errors[] = "Directory $dir is not readable";
		}
		else if (!is_writable($dir)) {
			$errors[] = "Directory $dir is not writable";
		}
	}
}

checkFile($targetFile);

function saveFile($saveFileName, $content) {
	if (file_put_contents($saveFileName, $content) === false) {
		echo "<div class='error'>Failed to write file: $saveFileName</div>";
		return false;
	}

	return true;
}

if (isset($_POST['isPostback']) && !DEMO) {
	$content = trim($_POST['content']);
	$success = saveFile($targetFile, $content);

	if ($success) {
		header("Location: robots.php?success=1");
		die;
	}
}

function permalink($link) {
	global $studio;
	return $studio->usePermalinks() ? $studio->permalinks->getLink($link)->getPermalink() : $link;
}

if (isset($_GET['import']) && !DEMO) {
	$lines = [
		'User-agent: *'
	];

	$content = implode("\n", $lines) . "\n";
	$success = saveFile($targetFile, $content);

	if ($success) {
		header("Location: robots.php?success=1");
		die;
	}
}
?>

<script src="../../../resources/ckeditor/ckeditor.js"></script>

<section>
	<h1>
		<a class="back" href="index.php">
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle" viewBox="0 0 16 16">
				<path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-4.5-.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z"/>
			</svg>
		</a>
		Edit robots.txt
	</h1>

	<form action="" method="post">
		<input type="hidden" name="isPostback" value="1" />

		<div class="save-container">
			<div class="saveable">
				<div class="panel v2" style="padding: 0;">
					<div class="replaceable">
						<?php if (!empty($errors)) { ?>
						<div class="error-box" style="margin-top: 0;">
							<h3>Issues detected</h3>
							<p>
								We identified the following file permission issues which will prevent this feature from
								working. Please use a file manager or FTP to change the <code>chmod</code> or ownership of the
								below paths.
							</p>

							<ul>
								<?php foreach ($errors as $error) { ?>
								<li><?php echo $error; ?></li>
								<?php } ?>
							</ul>
						</div>
						<?php } ?>

						<?php if (empty($content)) { ?>
						<div class="import-translations" style="margin-top: 0;">
							<h3>
								<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-import" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
									<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
									<path d="M14 3v4a1 1 0 0 0 1 1h4" />
									<path d="M5 13v-8a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2h-5.5m-9.5 -2h7m-3 -3l3 3l-3 3" />
								</svg>
								Default content available
							</h3>
							<p>
								Your website doesn't have a robots.txt file yet. Click the button below to automatically
								create a file with some default content.
							</p>

							<a class="btn" href="?import">Create</a>
						</div>
						<?php } ?>
					</div>

					<textarea class="editor" name="content" rows="20"><?php echo $content; ?></textarea>
				</div>
			</div>

			<div class="save">
				<div class="save-box">
					<button type="submit">
						<span>Save changes</span>
						<span>Saved</span>
						<img src="../../../resources/images/load32.gif" width="16px" height="16px">
					</button>
				</div>
			</div>
		</div>
	</form>
</section>

<script>
	var textarea = $('.editor');
	var replaceable = $('.replaceable');

	var editor = CodeMirror.fromTextArea(textarea[0], {
		lineNumbers: true,
		mode: 'plain',
		lineSeparator: '\n',
		indentUnit: 4
	});

	editor.on('change', function () {
		textarea.val(editor.doc.getValue());
	});
</script>

<?php
$page->footer();
?>
