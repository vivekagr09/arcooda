<?php
include_once __DIR__ . '/common.inc.php';
moduleLoad('review');
moduleLoad('member');
$product_id      = intval($_REQUEST['product_id']);
$item_list        = invoiceProductReviews($product_id);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Review Data</title>
    <link rel="stylesheet" href="/webadmin/admin.css" type="text/css" />
    <link rel="stylesheet" href="style.css" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.1/js/all.min.js" integrity="sha256-IKPvaXm75eTeGvrsxwPh00y8Xjzqs203hTlQYydpLXI=" crossorigin="anonymous"></script>
</head>
<body>
<style type="text/css">
    .border-class-table  td {
  border: 1px solid black;
  text-align: center;
}
.border-class-table  th {
  border: 1px solid black;
  text-align: center;
}
</style>
<link rel="stylesheet" type="text/css" href="style.css" />
<div class="product_content">
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
            $urlopen = "/webadmin/review/editReviewReply.php?id=".$item['id']."&action=edit&data=".$review['our_reply']."";
            $review_edit_url = "/webadmin/review/edit.php?id=";
            $links = array();
	        $links[] = '<a href="' . $review_edit_url . $review['id'] . '" target="_blank">Edit</a>';
	        $links[] = '<a href="#" onclick="window.open(\'' . $urlopen . '\',\'\',\'width=700,height=500\')">Edit Reply</a>';

            if($item['invoice_id'] != null) {
            $screenshot =  reviewGenericsFromDatabase($item['id'],'`product_reviews_images`','id','review_id');
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
      
    <?php } } ?>
</table>
</div>
</body>
</html>