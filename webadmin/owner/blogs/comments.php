<?php
/**
 * @var string[] $all_staff_list
 */
include_once __DIR__ . "/common.inc.php";

moduleLoad('product');

moduleLoad('staff');

include_once __DIR__ . "/header.inc.php";
// print_r($all_staff_list); die;
// $criteria = array(
//     "search" => array(
//         "staff" => 0,
//     ),
// );
$criteria = array();
if (isset($_GET["approve"]) && !empty($_GET["approve"])) {
    $is_approve = 0;
	$criteria = array(
		"search" => array(
			"is_approve" => $is_approve,
		),
	);
}

$member_url['action'] = 'edit';
unset($member_url['member_id']);
$member_url = '/webadmin/member/?' . http_build_query($member_url) . '&member_id=';

$comments = blogCommentsSearchFromDatabase($criteria);
$blogs    = blogsFromDatabase();

if (!isset($all_staff_list) || !is_array($all_staff_list)) {
    $all_staff_list = array();
    $query          = $DB->query("SELECT `id`, `first_name`, `last_name` FROM `highwaygroup`.`users`");
    while ($row = $DB->fetch_assoc($query)) {
        $all_staff_list[$row['id']] = trim($row['first_name'] . ' ' . $row['last_name']);
    }

    $DB->free_result($query);
}

?>
<style type="text/css">
	.radio-round {
		width: 1.3em;
		height: 1.3em;
		background-color: white;
		border-radius: 50%;
		vertical-align: middle;
		border: 1px solid #ddd;
		appearance: none;
		-webkit-appearance: none;
		outline: none;
		cursor: pointer;
	}

	.radio-round:checked {
		background-color: #ed1c24;
	}
</style>
<div style="text-align:left;display:none;">
	<a href="#" onclick="javascript:toggleSection('sectionBlogs');return false;"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16"> <b class="unfinished">Blogs</b></a>
	<br><br>
	<div id="sectionBlogs" class="child" style="display: none; background: yellow;">
<?php
foreach ($blogs as $b) {
    $un = blogCommentsFromDatabase($b["id"]);
	$an = answeredBlogCommentsFromDatabase($b["id"]);
?>

			<b><?=$b["title"]?></b> [<a href="?blog_id=<?=$b['id']?>&list=unanswered">Unanswered(<?=count($un)?>)</a>, <a href="?blog_id=<?=$b['id']?>&list=unanswered">Answered(<?=count($an)?>)</a>, <a href="?blog_id=<?=$b['id']?>&list=all">All</a>] <br>
<?php
}
?>
	</div>
</div>

<?=formatHTMLTitle('<a href="#" onclick="$(\'#sectionComment\').add(\'#sectionComment_hideNote\').slideToggle(\'slow\');return false">' . htmlspecialchars(_("Comments")) . "</a>")?>

<div id="sectionBlogList">
	<table class="table table-bordered table_grid spacetable table-condensed" style="width:95%;">
		<tr>
			<th style="width: 5%;"></th>
			<th style="width: 10%;">Blog</th>
			<th style="width: 20%;">Comment</th>
			<th style="width: 10%;">Product</th>
			<th style="width: 10%;">Staff Reply</th>
			<th style="width: 5%;">Approved</th>
			<th style="width: 5%;">For Approval</th>
			<th style="width: 10%;">Comment Date</th>
			<th style="width: 15%;"></th>
		</tr>

<?php
foreach ($comments as $n => $comment) {
    $blog = blogFromDatabase($comment["blog_id"]);
	if (is_null($blog)) {
		continue;
	}
	$reply = replyCommentsFromDatabase($comment["id"], 1, 0);
	$class = ($n & 1) ? "class='alt'" : "";

	$product_name = '';
	if($comment['product_id'] > 0){
		$product = productFromDatabase($comment['product_id']);
		if($product){
			$product_name = '<a href="/webadmin/products_new/edit.php?id='.$comment['product_id'].'" target="_blank">'.$product['name'].'<a>';
		}
	}
?>
			<tr <?=$class?> onmouseover="this.style.backgroundColor='#9DD6D2';" onmouseout="this.style.backgroundColor='';">
				<td><?=$n + 1?></td>
				<td style="width: 20%;"><a target="_blank" href="/webadmin/owner/blogs/edit.php?id=<?=$blog['id']?>"><?=htmlspecialchars($blog["title"])?></a></td>
				<td><?=htmlspecialchars($comment["comment"])?></td>
				<td><?= $product_name ?></td>
				<td style="text-align: center; width: 10%;">
					<?php if($comment['staff'] == 1){ ?>
						<img src="/img/favicon.ico" alt="logo" style="width:17px;"/> <?= $all_staff_list[$comment['member_id']] ?>
					<?php }else{ ?>
						<a href="<?php hecho($member_url . $comment['member_id'])?>" target="_blank"><?= memberFromDatabase($comment['member_id'])['full_name'] ?></a>
					<?php } ?>
				</td>
				<td style="text-align: center; width: 10%;<?= $comment['is_approve'] == 1 ? "background:#96d996;" : '' ?>" >
					<input type="radio" class="radio-round" name="approveStatus_<?= $comment['id'] ?>" <?= $comment['is_approve'] == 1 ? 'checked' : '' ?> onclick="approveComment(<?= $comment['id'] ?>, 1)" />
				</td>
				<td style="text-align: center; width: 10%;<?= $comment['is_approve'] == 0 ? "background:pink;" : '' ?>">
					<input type="radio" class="radio-round" name="approveStatus_<?= $comment['id'] ?>" <?= $comment['is_approve'] == 0 ? 'checked' : '' ?> onclick="approveComment(<?= $comment['id'] ?>, 0)" />
				</td>
				<td style="text-align: center; width: 10%;"><?=date("d/m/Y", strtotime($comment["created"]))?></td>
				<td style="text-align: center; width: 20%;">
					<a href="/webadmin/owner/blogs/view_comments.php?id=<?=$comment['id']?>">Reply</a> |
<?php
	if (1 == $comment["visible"]) {
?>
						<a href="/webadmin/owner/blogs/hide_comment.php?id=<?=$comment['id']?>">Deactivate</a> |
<?php
	} else {
?>
						<a href="/webadmin/owner/blogs//show_comment.php?id=<?=$comment['id']?>">Activate</a> |
<?php
	}
?>
					<a onclick="return confirm(\'Are you sure you want to delete this COMMENT?\')" href="/webadmin/owner/blogs/comment_delete.php?id=<?=$comment['id']?>">Delete</a> 
				</td>
			</tr>
<?php
}
?>
	</table>
</div>
<script type="text/javascript">
function approveComment(commentId, status) {
    window.location.href = '/webadmin/owner/blogs/approve.php?id=' + commentId + '&status=' + status;
}
</script>