<style type="text/css">
	.reply-comment-container a{
		color: blue!important;
	}
	.reply-comment-container a:hover{
		color: blue!important;
	}
	.comment-area {
	    display: none;
	}
	.form-group {
	    margin-bottom: 1rem;
	}
	.comments{
		margin-top: 5px;
	}
	.btn-primary{
		width: 10%!important;
	}
</style>

<?php
session_start();

/**
 * @var string[] $all_staff_list
 */
include_once __DIR__ . "/common.inc.php";

global $_GLOBALS;

moduleLoad('blog');

$id      = 0;
$replies = array();
$comment = array();
$blog    = array();

$message = "";

if (isset($_GET["id"]) && !empty($_GET["id"])) {
    $id = intval($_GET["id"]);
}

if ($comment = blogCommentFromDatabase($id)) {
    $replies = replyCommentsFromDatabase($id);
} else {
    die("404 - Not Found Error here.");
}

if ($blog = blogFromDatabase($comment["blog_id"])) {
} else {
    die("404 - Not Found Error here.");
}

$news_url = str_replace(' ', '-', $blog['title']). '-' .$blog['id'].'.html';

if (isset($_POST["reply"]) && isset($_POST["token"]) && $_POST["token"] == $_SESSION["token"]) {
    if (empty($_POST["reply"])) {
        $message = "Reply textarea is empty.";
    } else {
        $data = array(
            "member_id"        => $_GLOBALS["staff"]["staff_id"],
            "staff"            => 1,
            "blog_id"          => $comment["blog_id"],
            "reply_comment_id" => $id,
            "comment"          => trim($_POST["reply"]),
            "created"          => date("Y-m-d"),
        );

        $replyCommentId = blogCommentToDatabase($comment['blog_id'], $data);

        $replies = replyCommentsFromDatabase($id);

        $message = "Replied.";
    }
}

$_SESSION["token"] = md5(uniqid());

include_once __DIR__ . "/header.inc.php";
?>

<?php
if ("" != $message) {
?>
	<p style="color: red; text-align: center;"><?=$message?></p>
<?php
}
?>

<?=formatHTMLTitle('<a href="#" onclick="$(\'#sectionComment\').add(\'#sectionComment_hideNote\').slideToggle(\'slow\');return false">' . htmlspecialchars(_("Comment")) . "</a>")?>
<style>
	.comments-container {
		padding-left: 10px;
		border-left: 2px solid #e0e0e0;
	}
</style>
<div id="sectionComment">
	<table class="table_fields">
		<tr>
			<th>Blog</th>
			<td><a target="_blank" href="/webadmin/owner/blogs/edit.php?id=<?=$blog["id"]?>"><?=$blog["title"]?></a><span style="margin-left:15px;font-size:10px;font-weight:600;font-style:italic;"><a href="/owner-news/<?= htmlspecialchars($news_url) ?>" target="_blank">(Live)</a></td>
		</tr>

		<tr>
			<th>Comment</th>
			<td style="background: #eeeee0; margin: 5px 0;"><p style="padding: 5px;"><?=htmlspecialchars($comment["comment"])?></p></td>
			<!-- <td style="background: #eeeee0; margin: 5px 0;"><p style="padding: 5px;"><?= HTMLBlogComments($comment['blog_id'],0)?></p></td> -->
		</tr>

		<tr>
			<th>Comment Date</th>
			<td><?=date("d/m/Y", strtotime($comment["created"]))?></td>
		</tr>

	</table>
</div>

<?=formatHTMLTitle('<a href="#" onclick="$(\'#sectionReply\').add(\'#sectionReply_hideNote\').slideToggle(\'slow\');return false">' . htmlspecialchars(_("Reply")) . "</a>")?>

<div id="sectionReply" style="width: 95%; margin: auto;">
	<!-- <form action="" method="post">
		<textarea class="wide" rows="10" name="reply"></textarea>
		<br>
		<input type="hidden" name="token" value="<?=$_SESSION['token']?>" >

<?php
if (1 == $comment['visible']) {
?>
			<input type="submit" value="Reply">
<?php
} else {
?>
			<i style="color: red;">This comment is hidden</i>.
<?php
}
?>
	</form> -->
	<div class="comment-area-all">
		<div style="border-top: 1px solid #e0e0e0; margin-top: 10px; padding-top: 10px;">
			<?= HTMLBlogComments($comment['blog_id'],0,$comment['id']) ?>
		</div>
	</div>
	
</div>


<script type="text/javascript">
	function openComment(elem) {
		$(elem).parent().next().toggle();
		return false;
	}
	var staff_id ="<?= $_GLOBALS["staff"]["id"]?>";
	function comment(btn, bid, cid,product_id) {
		var comment = $(btn).parent().find("textarea[name=comment]").val();
		if (comment.length > 0) {
			$.ajax({
				url:        "/ajax/commentadmin.php",
				type:       "post",
				data:       { comment: comment, blog_id: bid, reply: cid ,product_id:product_id,staff_id:staff_id},
				dataType:   "json",
				beforeSend: function () {
					$(btn).attr("disabled", "disabled");
				},
				success:    function (r) {
					if (r["status"] == "200") {
						if (parseInt(cid) > 0) {
							$(btn).parents(".comments").after("<div class='comments-container'>" + r["response"] + "</div>");
							$(btn).parent().hide();
						} else {
							$(btn).next().children(".comments-container").prepend(r["response"]);
						}
						$(btn).removeAttr("disabled");
						$(btn).parent().find("textarea[name=comment]").val("");
						if ($(btn).prev().hasClass("alert")) {
							$(btn).prev().remove();
						}
					} else {
						alert("Comment error: Please try again.");
						location.reload();
					}
				}
			});
		} else {
			if ($(btn).prev().hasClass("alert")) {
				$(btn).prev().remove();
			}
			$(btn).before('<div class="alert alert-danger">Please say something to comment.</div>');
		}
	}
</script>