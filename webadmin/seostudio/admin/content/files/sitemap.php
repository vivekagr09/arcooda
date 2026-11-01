<?php

use Studio\Util\Sitemap\Sitemap;

require "../../includes/init.php";
$page->setPath("../../../")->requirePermission('admin-access')->setPage(11);

if (isset($_POST['isPostback']) && isset($_GET['success'])) {
	unset($_GET['success']);
}

$neededReview = false;
if ($studio->getopt('sitemap-needs-review') === '1') {
	$neededReview = true;
	$studio->setopt('sitemap-needs-review', '0');
}

$page->header('content');

$targetFile = $studio->basedir . DIRECTORY_SEPARATOR . 'sitemap.xml';
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
	if (empty($content)) {
		if (file_exists($saveFileName)) {
			if (!unlink($saveFileName)) {
				echo "<div class='error'>Failed to delete file: $saveFileName</div>";
				return false;
			}
		}

		return true;
	}

	if (file_put_contents($saveFileName, $content) === false) {
		echo "<div class='error'>Failed to write file: $saveFileName</div>";
		return false;
	}

	return true;
}

$invalid = false;

if (isset($_POST['isPostback']) && !DEMO) {
	$content = trim($_POST['content']);

	$dom = new DOMDocument();
	libxml_use_internal_errors(true);

	if (empty($content) || @$dom->loadXML($content)) {
		if (empty($content) || @$dom->schemaValidate($studio->bindir . '/sitemap.xsd')) {
			$success = saveFile($targetFile, $content);

			if ($success) {
				header("Location: sitemap.php?success=1");
				die;
			}
		}
		else {
			$invalid = true;
		}
	}
	else {
		$invalid = true;
	}

}

if (isset($_GET['import']) && !DEMO) {
	Sitemap::reset();
	$content = file_exists($targetFile) ? file_get_contents($targetFile) : '';
	header("Location: sitemap.php?success=1");
	die;
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
		Edit sitemap.xml
	</h1>

	<form action="" method="post">
		<input type="hidden" name="isPostback" value="1" />

		<div class="save-container">
			<div class="saveable">
				<div class="panel v2" style="padding: 0;">
					<div class="intro-text">
						<p>
							The code editor below will directly modify the <code>sitemap.xml</code> file for your website.
							You generally don't need to modify this yourself. Any changes made through the admin panel
							(such as changing permalinks or disabling tools) will be applied automatically.
						</p>
					</div>

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
								<li><?php echo sanitize_html($error); ?></li>
								<?php } ?>
							</ul>
						</div>
						<?php } ?>

						<?php if ($invalid) { ?>
						<div class="error-box" style="margin-top: 0;">
							<h3>Issues detected</h3>
							<p style="margin-bottom: 0 !important;">
								We found some errors with your code, so we didn't save your changes. Please fix them and try again:
							</p>
							<ul>
								<?php foreach (libxml_get_errors() as $error) { ?>
								<li><strong>Line <?php echo $error->line; ?>:</strong> <?php echo sanitize_html(str_replace('{http://www.sitemaps.org/schemas/sitemap/0.9}', '', $error->message)); ?></li>
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
								Generate a sitemap file
							</h3>
							<p>
								Your website doesn't have a sitemap yet. Click the button below to automatically generate a
								new sitemap file. After that, we'll automatically update it whenever you make changes to your
								website.
							</p>

							<a class="btn" href="?import">Generate</a>
						</div>
						<?php } ?>

						<?php if ($neededReview) { ?>
							<div class="alert alert-primary d-flex align-items-center">
								<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-circle me-2" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
									<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
									<circle cx="12" cy="12" r="9" />
									<line x1="12" y1="8" x2="12" y2="12" />
									<line x1="12" y1="16" x2="12.01" y2="16" />
								</svg>
								<div>
									Your sitemap was automatically modified by recent changes in other areas of the script.
									Please review the sitemap below and make sure it still looks good.
								</div>
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

<script src="../../../resources/scripts/codemirror.xml.js"></script>
<script>
	var textarea = $('.editor');
	var replaceable = $('.replaceable');

	var editor = CodeMirror.fromTextArea(textarea[0], {
		lineNumbers: true,
		mode: 'xml',
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
