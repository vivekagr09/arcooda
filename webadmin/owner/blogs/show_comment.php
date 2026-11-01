<?php

include_once __DIR__ . "/common.inc.php";

moduleLoad('blog');

$id      = 0;
$comment = array();

$message = "";

if (isset($_GET["id"]) && !empty($_GET["id"])) {
    $id = intval($_GET["id"]);
}

if ($id > 0) {
    $comment = blogCommentFromDatabase($id);
}

if (count($comment) > 0) {
    blogCommentShow($id);

    header("Location: /webadmin/blog/comments.php");
    exit();
} else {
    die("404 - Not Found Error here.");
}
