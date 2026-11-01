<?php
include_once('softjob_common.inc.php');

moduleLoad('asset'); //assetSearch

include_once('templates/softjob_header.tpl.php');

$softjob_id=intval($posted['id']);
$softjob=softjobFromDatabase($softjob_id);
$softjob_notes=softjobNotesFromDatabase($softjob_id);
$softjob_assets=assetSearch('softjob',array('softjob_id'=>$softjob_id));

$doedit_url=$get_vars;
$doedit_url['action']='do_edit';
$doedit_url='index.php?'.http_build_query($doedit_url);

$action='edit';
?>
<form accept-charset="utf-8" action="<?= $doedit_url ?>" method="post">
<?php
include_once('templates/addedit_softjob.tpl.php');
?>
</form>
<?php
include_once('templates/softjob_footer.tpl.php');
?>