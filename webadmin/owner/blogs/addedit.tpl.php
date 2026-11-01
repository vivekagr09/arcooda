<style type="text/css">
	.cke_notification_warning{
		display: none;
	}
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
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script type="text/javascript">
//<![CDATA[
$(function() {
	 content_editor = CKEDITOR.replace('item_content', {
			height:300,
			language : 'en',
			baseHref : <?php jecho($_GLOBALS["site_settings"]["home_uri"])?>,
			allowedContent: true,
			format_h2 : {
				element : "h2",
				attributes : {
					style : "background-color:#eee; border:1px solid #ccc; padding:5px 10px; color:#080; font-size:24px; font-weight:bold;"
				}
			},
			contentsCss : "https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css",
			bodyClass : "page_body",
			bodyId : "bd"
		});
	 	 content_editor = CKEDITOR.replace('long_description', {
			height:300,
			language : 'en',
			baseHref : <?php jecho($_GLOBALS["site_settings"]["home_uri"])?>,
			allowedContent: true,
			format_h2 : {
				element : "h2",
				attributes : {
					style : "background-color:#eee; border:1px solid #ccc; padding:5px 10px; color:#080; font-size:24px; font-weight:bold;"
				}
			},
			contentsCss : "https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css",
			bodyClass : "page_body",
			bodyId : "bd"
		});
    $("[name='header_image']").change(function() {
        var reader = new FileReader();
        reader.onload = function (e) {
            $("#header_image_img").attr("src", e.target.result);
            $("#header_image_a").attr("href", e.target.result);
        };
        reader.readAsDataURL(this.files[0]);
    });
    $("[name='item_thumb']").change(function() {
        var reader = new FileReader();
        reader.onload = function (e) {
            $("#item_thumb_img").attr("src", e.target.result);
            $("#item_thumb_a").attr("href", e.target.result);
        };
        reader.readAsDataURL(this.files[0]);
    });
	}).change();
$(function () {
$("#linkallproduct").change(function () {
    var isChecked = $("#linkallproduct").is(":checked");
    if (isChecked) {
        $('.linkproduct').attr('checked',true);
    } else {
        $('.linkproduct').attr('checked',false);
    }
});
$("#linkallproductSoftware").change(function () {
    var isChecked = $("#linkallproductSoftware").is(":checked");
    if (isChecked) {
        $('.linkproductSoftware').attr('checked',true);
    } else {
        $('.linkproductSoftware').attr('checked',false);
    }
});
});
function approveComment(commentId, status, blog_id) {
    window.location.href = '/webadmin/owner/blogs/approve.php?id=' + commentId + '&status=' + status + '&blog_id=' + blog_id;
}
//]]>
</script>
<?php
moduleLoad('product');
$news_url = str_replace(' ', '-', $blog['title']). '-' .$blog['id'].'.html';
$member_url['action'] = 'edit';
unset($member_url['member_id']);
$member_url = '/webadmin/member/?' . http_build_query($member_url) . '&member_id=';
?>
<div align="center" style="position:relative;">
	<?= formatHTMLTitle('Title');?>
	<span style="font-style: italic;margin-left: 15px;font-size: 9px;position: absolute;left: 90px;top: 15px;"><a href="/owner-news/<?= htmlspecialchars($news_url) ?>" target="_blank" style="color:black;">(view)</a></span>
</div>

<table class="wide" align="center" style="width:98%">
	<tr>
		<td><input type="text" placeholder="Blog Title" name="blog[title]" value="<?= $blog['title'] ?>" style="width:100%" /></td>
	</tr>
</table>
<?php 
$attached_product=explode(',',$blog['products'] );
$products=array_chunk($product_list, 3);
$is_checked=(count($attached_product)==count($product_list))?"checked":"";
?>
<div align="center">
	<?= formatHTMLTitle('Linked To Machines','<input type="checkbox" id="linkallproduct" '.$is_checked.'> Linked All Product');?>
</div>
<table class="wide" align="center" style="width:98%">
	<tr>
		
		<td style="width:100%">
			<table style="width:100%">
			<?php 
				foreach ($products as $key => $chunkvalue) {
					echo '<tr>';
					foreach ($chunkvalue as $key => $product) {
					?>
					<td>
					<input type="checkbox" class="linkproduct" name="blog[products][]" value="<?= $product['id']?>" <?= in_array($product['id'],$attached_product)?'checked':''?> /> <?= $product['name'] ?></td>
				<?php

					}
					echo '</tr>';
				}


			?>
		</table>
		</td>
	</tr>
</table>
<?php 
$attached_product=explode(',',$blog['products'] );
$products=array_chunk($product_list_software, 3);
$is_checked=(count($attached_product)==count($product_list))?"checked":"";
?>
<div align="center">
	<?= formatHTMLTitle('Linked To Software','<input type="checkbox" id="linkallproductSoftware" '.$is_checked.'> Linked All Product');?>
</div>
<table class="wide" align="center" style="width:98%">
	<tr>
		
		<td style="width:100%">
			<table style="width:100%">
			<?php 
				foreach ($products as $key => $chunkvalue) {
					echo '<tr>';
					foreach ($chunkvalue as $key => $product) {
					?>
					<td>
					<input type="checkbox" class="linkproductSoftware" name="blog[products][]" value="<?= $product['id']?>" <?= in_array($product['id'],$attached_product)?'checked':''?> /> <?= $product['name'] ?></td>
				<?php

					}
					echo '</tr>';
				}


			?>
		</table>
		</td>
	</tr>
</table>
<div align="center">
	<?= formatHTMLTitle('Short Description');?>
</div>
<table class="wide" align="center" style="width:98%">
	<tr>
		<td><textarea id="item_content" name="blog[blog]" style="width:100%" rows=5><?= $blog['blog']?></textarea></td>
	</tr>
</table>

<div align="center">
	<?= formatHTMLTitle('Long Description');?>
</div>
<table class="wide" align="center" style="width:98%">
	<tr>
		<td><textarea id="long_description" name="blog[long_description]" style="width:100%" rows=5><?= $blog['long_description']?></textarea></td>
	</tr>
</table>
<div align="center">
	<?= formatHTMLTitle('Image');?>
</div>
<table class="table_fields" align="center" style="width:98%">
            
        <tr>
            <th>Thumbnail :</th>
            <td>
                <div>
					<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
                    <input name="item_thumb" type="file" accept="image/*" />
                </div>
                <div>
                    <a href="/webadmin/assets/owner/blog/<?php hecho((array_key_exists('thumb_url', $blog)) ? $blog['thumb_url'] : '#')?>" target="_blank" id="item_thumb_a"><img id="item_thumb_img" src="/webadmin/assets/owner/blog/<?php hecho((array_key_exists('thumb_url', $blog)) ? $blog['thumb_url'] : '/_.gif')?>" style="vertical-align:middle;max-width:96px;max-height:96px;" /></a>
					<?php
					if (array_key_exists('thumb_url', $blog)) {
					?>
					                    <br />
					                    <small style="color:#f00;"><i>NB: Uploading new image will overwrite existing image</i></small>
					<?php
					}
					?>
                </div>
            </td>
        </tr>
	    <tr>
	        <th>Header Image:</th>
            <td>
                <div id="productPictureRemote">
					<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
                    <input style="width:272px;" name="header_image" type="file" accept="image/*" />
                    <!-- <input type="button" value="Link Thumbnail" class="unfinished" onclick="window.open(&quot;.?action=image&amp;return_message=product_image_local&quot;);" /> -->
                </div>
                <div id="sectionItemImage">
                    <a href="/webadmin/assets/owner/blog/<?php hecho(array_key_exists('header_image', $blog) ? $blog['header_image'] : '#')?>" target="_blank" id="header_image_a"><img id="header_image_img" src="/webadmin/assets/owner/blog/<?php hecho((array_key_exists('header_image', $blog)) ? $blog['header_image'] : '/_.gif')?>" style="vertical-align:middle;max-width:96px;max-height:96px;" /></a>
					<?php
					if (array_key_exists('header_image', $blog)) {
					?>
					                    <br />
					                    <small style="color:#f00;"><i>NB: Uploading new image will overwrite existing image</i></small>
					<?php
					}
					?>	
                </div>
            </td>
        </tr>
</table>
<div align="center" id="comment">
	<?= formatHTMLTitle('Owner Comments');?>
</div>
<?php 
	$comments = allBlogCommentsFromDatabase($blog['id']);
	$commentCount = blogCommentsCount($blog['id']);
	$staff_list = [];
	$query = $DB->query(sprintf(
                    "SELECT `id`, `first_name`, `last_name` FROM `highwaygroup`.`users`  ORDER BY `first_name` ASC, `last_name` ASC"
                ));
    while ($row = $DB->fetch_assoc($query)) {
        $staff_list[$row['id']] = trim($row['first_name'] . ' ' . $row['last_name']);
    }
?>
<table class="table_grid" align="center" style="width:98%">
	<tr>
		<th style="text-align:center">Owner Comment</th>
		<th style="text-align:center;width:15%;">Member Name</th>
		<th style="text-align:center">Product</th>
		<th style="text-align:center;width:15%;">Approved</th>
		<th style="text-align:center;width:15%;">For Approval</th>
		<th style="text-align:center"></th>
	</tr>
	<?php 
	foreach($comments as $comment)
	{ 
		$product_name = '';
		if($comment['product_id'] > 0){
			$product = productFromDatabase($comment['product_id']);
			if($product){
				$product_name = '<a href="/webadmin/products_new/edit.php?id='.$comment['product_id'].'" target="_blank">'.$product['name'].'<a>';
			}
		}
		?>
	<tr onmouseover="this.style.backgroundColor='#9DD6D2';" onmouseout="this.style.backgroundColor='#fff';">
		<td><?= htmlspecialchars($comment['comment']) ?></td>
		<td>
			<?php if($comment['staff'] == 1){ ?>
				<img src="/img/favicon.ico" alt="logo" style="width:17px;"/> <?= $staff_list[$comment['member_id']] ?>
			<?php }else{ ?>
				<a href="<?php hecho($member_url . $comment['member_id'])?>" target="_blank"><?= memberFromDatabase($comment['member_id'])['full_name'] ?></a>
			<?php } ?>
		</td>
		<td><?= $product_name ?></td>
		<td style="<?= $comment['is_approve'] == 1 ? "background:#96d996;" : '' ?>">
			<input type="radio" class="radio-round" name="approveStatus_<?= $comment['id'] ?>" <?= $comment['is_approve'] == 1 ? 'checked' : '' ?> onclick="approveComment(<?= $comment['id'] ?>, 1, <?=$blog['id']?>)"/>
		</td>
		<td style="<?= $comment['is_approve'] == 0 ? "background:pink;" : '' ?>">
			<input type="radio" class="radio-round" name="approveStatus_<?= $comment['id'] ?>" <?= $comment['is_approve'] == 0 ? 'checked' : '' ?> onclick="approveComment(<?= $comment['id'] ?>, 0, <?=$blog['id']?>)"/>
		</td>
		<td>
			<a  href="/webadmin/owner/blogs/view_comments.php?id=<?=$comment['id']?>" target="_blank">Reply</a> |
			<a onclick="return confirm(\'Are you sure you want to delete this COMMENT?\')" href="/webadmin/owner/blogs/comment_delete.php?id=<?=$comment['id']?>&blog_id=<?=$blog['id']?>">Delete</a> | 
			<?php
	if (1 == $comment["visible"]) {
?>
						<a href="/webadmin/owner/blogs/hide_comment.php?id=<?=$comment['id']?>">Deactive</a></td>
<?php
	} else {
?>
						<a href="/webadmin/owner/blogs//show_comment.php?id=<?=$comment['id']?>">Active</a></td>
<?php
	}
?>
	</tr>
	<?php } ?>
</table>
<br />
<div align="center">
	<?= formatHTMLTitle('Status');?>
</div>

<table class="table_fields" align="center" style="width:98%">
         <tr>
            <th>Status:</th>
            <td>
	              <select class="form-control col-sm-9" id="status" name="blog[status]"  onchange="this.style.backgroundColor=((this.value=='n')?'rgb(255 0 0)':'#00FF00');" style="width:90%;background:<?=('y' == $blog['status']) ?  '#00FF00;':'rgb(255 0 0);';?>">
	                <?= formatHTMLOptions(['n'=>'In Active','y'=>'Active'], $blog['status']) ?>
	            </select>
            </td>
        </tr>
        
	</table>
<br />
<center>
	<input type="hidden" name="id" value="<?= $blog['id']?>">
	<input type="button" value="<?= ($add)?'Add':'Update'?>" onclick="javascript:this.form.submit();" />
</center>