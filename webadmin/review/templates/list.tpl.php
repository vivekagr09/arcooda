<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
<?php
$activate_url           = $_GET;
$activate_url['action'] = 'activate';
unset($activate_url['id']);
$activate_url = '.?' . http_build_query($activate_url) . '&id=';

$deactivate_url           = $_GET;
$deactivate_url['action'] = 'deactivate';
unset($deactivate_url['id']);
$deactivate_url = '.?' . http_build_query($deactivate_url) . '&id=';

$sort_url = $get_vars;
unset($sort_url['order_by']);
unset($sort_url['order']);
$sort_url = '?' . http_build_query($sort_url);

$edit_url           = $get_vars;
$edit_url['action'] = 'edit';
unset($edit_url['id']);
$edit_url = 'index.php?' . http_build_query($edit_url) . '&id=';

$staff_id = $_GLOBALS['staff']['staff_id'];
?>
<table class="table_grid" style="width:95%" align="center">
	<tr>
		<th><a href="<?=$sort_url?>&order_by=created_at&order=<?=('created_at' == $order_by && 'asc' == $order) ? 'desc' : 'asc'?>">Date</a></th>
		<th>Invoice</th>
		<th>Member Name</th>
		<th style="width:350px;">Product</th>
		<th>Review</th>
		<th>Feedback</th>
		<th>Our Reply</th>
		<th style="width:80px;">Status</th>
		<th style="width:50px;">&nbsp;</th>
	</tr>
    <?php
        // print_r($item_list); die;
        foreach($item_list as $item) {
            $review = reviewFromDatabase($item['id']);
            $member = memberFromDatabase($review['user_id']);
            $product = productFromDatabase($review['product_id']);
            $urlopen = "editReviewReply.php?id=".$item['id']."&action=edit&data=".$review['our_reply']."";
            $links = array();
	        $links[] = '<a href="' . $edit_url . $review['id'] . '" target="_blank">Edit</a>';
	        $links[] = '<a href="#" onclick="window.open(\'' . $urlopen . '\',\'\',\'width=700,height=500\')">Edit Reply</a>';

            if($item['invoice_id'] != null) {
            $screenshot =  reviewGenericsFromDatabase($item['id'],'`product_reviews_images`','id','review_id');
            // print_r($screenshot); die;
            if(isset($screenshot) && is_array($screenshot)){
                foreach ($screenshot as $k => $value) {
                      $screenshot[$k]['url']= sprintf(
                            '/images/review/%s',
                            $value['name']
                        );  
                }
            }
    ?>
    <tr style="text-align:center;" class="<?=$class?>">
        <td><?= date('d/m/y', strtotime($review['created_at'])) ?></td>
        <td><a href="/webadmin/invoice/?search_for=<?=$item['invoice_id']?>&action=edit&site_id=39&invoice_id=<?=$item['invoice_id']?>"  target="_blank"><?= $item['invoice_id'] ?></a></td>
        <td><a href="/webadmin/member/members_edit.php?member_id=<?=$review['user_id']?>" target="_blank"><?= $member['full_name'] ?></a></td>
        <td><a href="/webadmin/products_new/edit.php?action=edit&amp;id=<?=$review['product_id']?>" target="_blank"><?= $product['product_name'] ?></a></td>
        <td style="text-align:center;">
            <div class="product-card__rating" style="display:flex;justify-content:center;text-align:center;">
                <div class="product-card__rating-stars">
                    <div class="rating__body">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?php 
                            // Determine the classes for the star
                            $star_classes = 'fas fa-star';  // Always show the star icon
                            $star_color = ($review['reviews'] >= $i) ? 'gold' : '#bbbbbb'; // Gold for filled stars, light gold for others
                            ?>
                            <i class="<?= $star_classes ?>" style="color: <?= $star_color ?>;"></i>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </td>
        <td>
            <?php if($review['comment'] != NULL) { ?>
                <span onmouseover="<?php hecho("tt_in(" . jecho($review['comment'], 1) . ")") ?>" onmouseout="tt_out()"><i class="fas fa-regular fa-globe" style="color:green;font-size:18px;"></i></span>
            <?php } ?>
            <?php if($review['feedback'] != NULL) { ?>
                <span onmouseover="<?php hecho("tt_in(" . jecho($review['feedback'], 1) . ")") ?>" onmouseout="tt_out()"><i class="fas fa-regular fa-globe" style="color:red;font-size:18px;"></i></span>
                
            <?php } 
            ?>
            <?php if(isset($screenshot) && is_array($screenshot)){ 
                foreach ($screenshot as $k => $value) {
                    ob_start(); ?>
                    <img src="<?php hecho($value['url'])?>?width=96&height=96" alt="" style="width:96px;height:96px;position:absolute;left:8px;" />
                <?php
                        $product_tooltip_image = ob_get_clean();
                ?>
                <span  onmouseover="<?php hecho('tt_in(' . jecho($product_tooltip_image, 1) . ');') ?>" onmouseout="tt_out();" >
                    <a href="/images/review/<?= $value['name'].'' ?>" target="_blank"><i class="fas fa-regular fa-camera" style="color:black;font-size:18px;"></i></a>
                </span>
            <?php } } ?>
        </td>
        <td>
            <?php if($review['our_reply'] != '') { ?>
                <img src="/img/favicon.ico" width="30" onmouseover="<?php hecho('tt_in(' . jecho($review['our_reply'], 1) . ');') ?>" onmouseout="tt_out();" >
            <?php } ?>
        </td>
        <td style="background:<?= $review['status'] == 'y' ? '#e0fec2' : '#ffdde8' ?>">
            <?php if($review['status'] == 'y') { ?>
                <a href="<?=$deactivate_url . $review['id']?>">Approved</a>
            <?php } else { ?>
                <a href="<?=$activate_url . $review['id']?>">Not Approved</a>
            <?php } ?>
        </td>
        <td style="white-space:nowrap;">
            <div style="display: none;">
                <a data-fancybox="gallery" href="image1.jpg"><img src="thumb1.jpg" alt="Image 1"></a>
            </div>
            <?php echo implode(' | ', $links) ?></td>
    </tr>
    <div style="display: none;">
      
    <?php } } ?>
</table>

<br />
<br />

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
