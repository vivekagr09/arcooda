<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('blogcomment');
global $_GLOBALS;

if (!empty($_POST)) {
    if (isset($_POST['comment']) && !empty($_POST['comment']) && isset($_POST['blog_id']) && !empty($_POST['blog_id'])) {
        $comment = trim($_POST['comment']);
        $blog_id = intval($_POST['blog_id']);
        $blog = blogFromDatabase($blog_id);
        $product_id =  intval($_POST['product_id']);
        $news_url = str_replace(' ', '-', $blog['title']). '-' .$blog['id'].'.html';
        if (!is_null($blog)) {
            $reply = 0;

             $name = '<img src="/img/favicon.ico" alt="logo" style="width:17px;"/>';

            if (isset($_POST['reply']) && !empty($_POST['reply'])) {
                $reply = intval($_POST['reply']);
            }
            $data = array(
                "member_id"        => $_POST['staff_id'],
                "staff"            => 1,
                "blog_id"          => $blog_id,
                "reply_comment_id" => $reply,
                'product_id'       => $product_id,
                "comment"          => $comment,
                "created"          => date("Y-m-d"),
                "is_approve"       => 1
            );

            $comment_id = blogCommentToDatabase($blog_id, $data);

            if ($comment_id > 0) {
                echo json_encode(array(
                    "status"   => "200",
                    "response" => HTMLBlogComment($blog_id, $comment_id, $name, $comment, "Now",$product_id),
                ));
            } else {
                echo json_encode(array(
                    "status" => "500",
                ));
            }
        }
    }
} else {
    echo json_encode(array(
        "status" => "404",
    ));
}
