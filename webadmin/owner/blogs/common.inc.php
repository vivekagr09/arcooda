<?php
include_once __DIR__ . '/query.inc.php';

include_once $_SERVER["DOCUMENT_ROOT"] . "/webadmin/common.inc.php";

moduleOverride("metatag", __DIR__ . "/query.inc.php");
moduleLoad("metatag");


moduleOverride("blogcomment", __DIR__ . "/query_blogcomment.inc.php");
moduleLoad("blogcomment");

