<!-- _product_review.tpl -->

<?php
/**
 * Product Review Template
 *
 * - $item_review_count
 * - $item_reviews
 */

if (!isset($item_reviews) || !is_array($item_reviews)) {
    return;
}

?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionProductReview&quot;);return false">Product Review</a>');?>

<div id="sectionProductReview">
    <table class="table_fields">
        <tr>
            <th>Total Reviews :</th>
            <td><?= $item_review_count['total_reviews'] ?></td>
        </tr>
        <tr>
            <th>Average Rating :</th>
            <td><?= number_format($item_review_count['average_rating'],1) ?></td>
        </tr>
    </table>
    <br />

    <table class="table_grid">
        <tr>
        <!-- <tr style="background:#E3EBF7;"> -->
            <td width="5%"><b>Date</b></td>
            <td width="10%"><b>Invoice</b></td>
            <td width="20%"><b>Member Name</b></td>
            <td width="10%"><b>Review</b></td>
            <td width="15%"><b>Feedback</b></td>
            <td width="10%"><b>Our Reply</b></td>
            <td width="10%"><b>Status</b></td>
            <td width="10%">&nbsp;</td>
        </tr>
<?php
if (count($item_reviews)) {
    $temp = $item_reviews;
    if (count($item_reviews) > 5 && 'admin_notes' !== $action) {
        $temp = array_chunk($item_reviews, 5);
        $temp = reset($temp);
    }
    $alt = false;
    foreach ($temp as $itemReview) {
        $review = reviewFromDatabase($itemReview['id']);
        $member = memberFromDatabase($review['user_id']);
        $product = productFromDatabase($review['product_id']);
        $urlopen = "/webadmin/review/editReviewReply.php?id=".$itemReview['id']."&action=edit&data=".$review['our_reply']."";
        $review_edit_url = "/webadmin/review/edit.php?id=";
        $links = array();
        $links[] = '<a href="' . $review_edit_url . $review['id'] . '" target="_blank">Edit</a>';
        $links[] = '<a href="#" onclick="window.open(\'' . $urlopen . '\',\'\',\'width=700,height=500\')">Edit Reply</a>';

        if($itemReview['invoice_id'] != null) {
        $screenshot =  reviewGenericsFromDatabase($itemReview['id'],'`product_reviews_images`','id','review_id');
        if(isset($screenshot) && is_array($screenshot)){
            foreach ($screenshot as $k => $value) {
                  $screenshot[$k]['url']= sprintf(
                        '/reviewimage/%s-%s-%s-%d-%d.jpg',
                        $product['slug'],
                        'review',
                        $review['user_country'] ? getCountryName($v['user_country']) : 'Australia',
                        $review['product_id'],
                        $value['id']
                    );  
            }
        }
?>
<tr style="text-align:center;" class="<?=$class?>">
    <td><?= date('d/m/y', strtotime($review['created_at'])) ?></td>
    <td><a href="/webadmin/invoice/?search_for=<?=$itemReview['invoice_id']?>&action=edit&site_id=39&invoice_id=<?=$itemReview['invoice_id']?>"  target="_blank"><?= $itemReview['invoice_id'] ?></a></td>
    <td><a href="/webadmin/member/members_edit.php?member_id=<?=$review['user_id']?>" target="_blank"><?= $member['full_name'] ?></a></td>
    <!-- <td><a href="/webadmin/products_new/edit.php?action=edit&amp;id=<?=$review['product_id']?>" target="_blank"><?= $product['product_name'] ?></a></td> -->
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
            
        <?php } ?>
        <?php if(isset($screenshot) && is_array($screenshot)){ 
            foreach ($screenshot as $k => $value) {
                ob_start(); ?>
                <img src="<?php hecho($value['url'])?>" alt="" style="max-width:96px;max-height:96px;position:absolute;left:8px;" />
            <?php
                    $product_tooltip_image = ob_get_clean();
            ?>
            <span  onmouseover="<?php hecho('tt_in(' . jecho($product_tooltip_image, 1) . ');') ?>" onmouseout="tt_out();" >
                <a href="<?= $value['url'].'?width=700&hight=700' ?>" target="_blank"><i class="fas fa-regular fa-camera" style="color:black;font-size:18px;"></i></a>
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
            Approved
        <?php } else { ?>
            Not Approved
        <?php } ?>
    </td>
    <td style="white-space:nowrap;">
        <div style="display: none;">
            <a data-fancybox="gallery" href="image1.jpg"><img src="thumb1.jpg" alt="Image 1"></a>
        </div>
        <?php echo implode(' | ', $links) ?></td>
</tr>
<div style="display: none;">
  
<?php } } 
    unset($temp);
    unset($alt);
}
?>
    </table>
    <div style="text-align:center">
        <input type="button" value="View All (<?=count($item_reviews)?>)" onclick="window.open('productAllReviews.php?product_id=<?=$_GET['id']?>','','width=700,height=500')" style="margin-top:10px;"/>
    </div>
    <br />
    <br />
    <input type="hidden" name="item_admin_notes_deleted" value="" />
</div>

<!-- <div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionAdminNotes_hideNote">
    Admin Notes can be viewed by Clicking "Admin Notes" above<br />
</div> -->

<!-- /_product_review.tpl -->
