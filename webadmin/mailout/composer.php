<?php
/***********************************************************************
 ** Title.........:
 ** Version.......:
 ** Author........:    Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
 ** Filename......:
 ** Last changed..:    Bede Pavan 26/2/03
 ** Notes.........:
 ***********************************************************************/

include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

include_once 'email_functions.php';
include_once 'auto_contents.php';
include_once 'uploader.php';

$email_vars = get_mailout_vars();

?>
<form accept-charset="utf-8" name="mailout_admin" method="post" action="composer.php" enctype="multipart/form-data">
<?php

function do_new_email()
{
    global $DB, $email_vars;
    $DB->query("DELETE FROM mailout_vars");
    include_once 'composer.inc.php';
}

function showPreview()
{
?>
	<script language="javascript">
		window.open('preview.php');
	</script>
<?php
}

if (isset($action2)) {
    switch ($action2) {
        case 'preview':
            generate_email();
            showPreview();
            include_once 'composer.inc.php';
            break;
        case 'new_email':
            do_new_email();
            break;
    }
}
?>
</form>
<?php
include_once '../footer.inc.php';
?>