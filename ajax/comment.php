<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('blogcomment');
global $_GLOBALS;

if (!empty($_POST) && (isset($_GLOBALS["member"]) && !empty($_GLOBALS["member"]) && $_GLOBALS["member"]["member_id"] > 0)) {
    if (isset($_POST['comment']) && !empty($_POST['comment']) && isset($_POST['blog_id']) && !empty($_POST['blog_id'])) {
        $comment = trim($_POST['comment']);
        $blog_id = intval($_POST['blog_id']);
        $blog = blogFromDatabase($blog_id);
        $product_id =  intval($_POST['product_id']);
        $news_url = str_replace(' ', '-', $blog['title']). '-' .$product_id. '-' .$blog['id'].'.html';
        if (!is_null($blog)) {
            $reply = 0;

            $name = strtoupper(substr($_GLOBALS["member"]["first_name"], 0, 1)) . strtoupper(substr($_GLOBALS["member"]["last_name"], 0, 1)) . " - " . $_GLOBALS["member"]["country"];

            if (isset($_POST['reply']) && !empty($_POST['reply'])) {
                $reply = intval($_POST['reply']);
            }

            $data = array(
                "member_id"        => $_GLOBALS["member"]["id"],
                "blog_id"          => $blog_id,
                "reply_comment_id" => $reply,
                'product_id'       => $product_id,
                "comment"          => $comment,
                "created"          => date("Y-m-d"),
            );

            $comment_id = blogCommentToDatabase($blog_id, $data);

            if ($comment_id > 0) {
                $message = "
					<html>
					<head>
						<title>HTML email</title>
					</head>
					<body>
						<table>
							<tr style='line-height: 30px;'>
								<th>Blog</th>
								<td>:</td>
								<td>{$blog['title']} (<a href='https://www.arcooda.com/owner-news/".$news_url."' target='_blank'>Live</a>) (<a href='https://www.arcooda.com/webadmin/owner/blogs/edit.php?id=" . $blog["id"] . "' target='_blank'>Webadmin</a>)</td>
							</tr>

							<tr style='line-height: 30px;'>
								<th>Comment</th>
								<td>:</td>
								<td>{$comment} (<a href='https://www.arcooda.com/webadmin/owner/blogs/view_comments.php?id=" . $comment_id . "' target='_blank'>Webadmin</a>)</td>
							</tr>
                            <tr style='line-height: 30px;'>
                                <th>Member Name</th>
                                <td>:</td>
                                <td>".$_GLOBALS["member"]["first_name"].' '.$_GLOBALS["member"]["last_name"]." (<a href='https://www.arcooda.com/webadmin/member/members_edit.php?member_id=" . $_GLOBALS["member"]["member_id"] . "' target='_blank'>Webadmin</a>)</td>
                            </tr>
						</table>

					</body>
					</html>
					";   
                // Always set content-type when sending HTML email
                $headers = "From: sales@arcooda.com" . "\r\n";
                $headers .= "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

                // Temporary email
                mail("sales@arcooda.com", "Owner Comment: " .$name . " has commented on blog: " . $blog["title"], $message, $headers);

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
