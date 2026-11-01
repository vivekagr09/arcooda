<?php
$form_action='list';
if($action=='edit') $form_action='do_edit';
if($action=='add') $form_action='do_add';

$post_url=$get_vars;
$post_url['action']=$form_action;
$post_url="index.php?".http_build_query($post_url);
?>
<!-- addedit_asset.tpl.php -->

<script type="text/javascript" src="asset_funcs.js.php"></script>
<script type="text/javascript">
function validateAsset()
{
	return true;
}
</script>

<form accept-charset="utf-8" enctype="multipart/form-data" autocomplete="off" method="post" action="<?= $post_url ?>">

<?php
switch($form_action)
{
case 'do_edit':
	break;
case 'do_add':
	include("add_asset.tpl.php");
	break;
default:
	break;
}
?>

<input type="button" value="Submit <?= $section_singular ?>" onclick="javascript:if(validateAsset()) this.form.submit();" />
</form>

<!-- /addedit_asset.tpl.php -->
