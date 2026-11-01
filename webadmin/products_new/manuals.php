<?php
include_once __DIR__ . "/common.inc.php";

global $_GLOBALS;

//Override _GET with _POST in _REQUEST
$_REQUEST = array_merge($_GET, $_POST);

$action     = $_REQUEST["action"];
$staff_id   = $_GLOBALS["staff"]["id"];
$id         = intval($_REQUEST["id"]);
$product_id = intval($_REQUEST["product_id"]);

$section_name = "manuals";

//Update Actions
switch ($action) {
    case "do_delete":
        $section_handler->manualsDelete($id);

        $action = "json_true";
        break;
}
$result = array("delete" => true, "id" => $id);
?>
<script type="text/javascript">
//<![CDATA[
window.opener.setManuals(<?=json_encode($result)?>);
window.close();
//]]>
</script>

