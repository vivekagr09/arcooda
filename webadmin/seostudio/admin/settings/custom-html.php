<?php
require "../includes/init.php";
$page->setPath("../../")->requirePermission('admin-access')->setPage(3)->setTitle("Custom HTML")->header();

if (isset($_POST['head']) && !DEMO) {
    $head = $_POST['head'];
    $body = $_POST['body'];

    $studio->setopt("custom-head-html", $head);
    $studio->setopt("custom-body-html", $body);

    header("Location: custom-html.php?success=1");
    die;
}
?>

<div class="heading">
    <h1>Custom HTML</h1>
    <h2>Super charge your studio</h2>
</div>

<form action="" method="post">
    <div class="panel v2 back">
        <a href="../settings.php">
            <i class="material-icons">&#xE5C4;</i> Back
        </a>
    </div>
    <div class="save-container">
        <div class="saveable">
            <div class="panel v2">
                <div class="setting-group">
                    <h3>Code</h3>

                    <div class="setting textarea">
                        <label for="Ctl_TextBox_01">Head <span class="help tooltip" title="Enter code to add in the bottom of the <head></head> tag."><i class="material-icons">&#xE8FD;</i></span></label>

                        <div class="text">
                            <textarea id="Ctl_TextBox_01" name="head" rows="4" class="editor"><?php echo $studio->getopt("custom-head-html"); ?></textarea>
                        </div>
                    </div>

                    <div class="setting textarea">
                        <label for="Ctl_TextBox_02">Body <span class="help tooltip" title="Enter code to add in the bottom of the &lt;body&gt;&lt;/body&gt; tag."><i class="material-icons">&#xE8FD;</i></span></label>

                        <div class="text">
                            <textarea id="Ctl_TextBox_02" name="body" rows="4" class="editor"><?php echo $studio->getopt("custom-body-html"); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="save">
            <div class="save-box">
                <button type="submit">
                    <span>Save changes</span>
                    <span>Saved</span>
                    <img src="../../resources/images/load32.gif" width="16px" height="16px">
                </button>
            </div>
        </div>
    </div>
</form>

<script src="../../resources/scripts/codemirror.html.js"></script>
<script src="../../resources/scripts/codemirror.xml.js"></script>
<script src="../../resources/scripts/codemirror.js.js"></script>
<script src="../../resources/scripts/codemirror.css.js"></script>

<script>
	var textarea = $('.editor');

	var headEditor = CodeMirror.fromTextArea(textarea[0], {
		lineNumbers: true,
		mode: 'htmlmixed',
		lineSeparator: '\n',
		indentUnit: 4
	});

	var bodyEditor = CodeMirror.fromTextArea(textarea[1], {
		lineNumbers: true,
		mode: 'htmlmixed',
		lineSeparator: '\n',
		indentUnit: 4
	});

	headEditor.on('change', function () {
		textarea.eq(0).val(headEditor.doc.getValue());
	});

	bodyEditor.on('change', function () {
		textarea.eq(1).val(bodyEditor.doc.getValue());
	});
</script>

<?php
$page->footer();
?>
