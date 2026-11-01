<?php

include_once __DIR__ . "/common.inc.php";

moduleLoad('blog');

$id  = 0;
$blog_id = 0;

$message = "";

if (isset($_GET["id"]) && !empty($_GET["id"])) {
    $id = intval($_GET["id"]);
}

if (isset($_GET["blog_id"]) && !empty($_GET["blog_id"])) {
    $blog_id = intval($_GET["blog_id"])."#comment";
}

if ($id > 0) {
    $comment = blogCommentDeleteFromDatabase($id);
    if($blog_id > 0){
        header("Location: /webadmin/owner/blogs/edit.php?id=".$blog_id);
    } else {
        header("Location: /webadmin/owner/blogs/comments.php");
    }
    exit();
} else {
    die("404 - Not Found Error here.");
}
