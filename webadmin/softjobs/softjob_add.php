<?php
include_once('softjob_common.inc.php');
include_once('templates/softjob_header.tpl.php');

$softjob_id='';
$softjob=array();
$softjob['reporter']=$staff_id;
$softjob['time_added']=time();
$softjob_notes=array();
$softjob_assets=array();

$doadd_url=$get_vars;
$doadd_url['action']='do_add';
$doadd_url='index.php?'.http_build_query($doadd_url);

$action='add';
$refer=$posted['refer'];
?>
<form accept-charset="utf-8" action="<?= $doadd_url ?>" method="post">
<?php
include_once('templates/addedit_softjob.tpl.php');
?>
</form>
<?php
include_once('templates/softjob_footer.tpl.php');
?>