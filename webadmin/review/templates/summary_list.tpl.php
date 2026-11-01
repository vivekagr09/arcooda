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
		<th>ID</th>
		<th style="width:350px;">Product Name</th>
		<th>Total Reviews</th>
		<th>Average Rating</th>
		<th>Last Rating</th>
		<th style="width:50px;">&nbsp;</th>
	</tr>
    <?php
        // print_r($item_list); die;
        foreach($item_list as $item) {
            $product = productFromDatabase($item['product_id']);
            // $links = array();
	        // $links[] = '<a href="' . $edit_url . $review['id'] . '" target="_blank">Edit</a>';
	        // $links[] = '<a href="#" target="_blank">Reply</a>';
    ?>
    <tr style="text-align:center;" class="<?=$class?>">
        <td>#<?= $item['product_id'] ?></td>
        <td><a href="/buy/<?=$product['slug']?>-<?=$item['product_id']?>" target="_blank"><?= $product['product_name'] ?></a></td>
        <td><?= $item['total_reviews']?></td>
        <td>
            <div class="product-card__rating" style="display:flex;justify-content:center;">
                <div class="product-card__rating-stars">
                    <div class="rating__body">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?php 
                            // Determine the classes for the star
                            $star_classes = 'fas fa-star';  // Always show the star icon
                            $star_color = ($item['average_rating'] >= $i) ? 'gold' : '#bbbbbb'; // Gold for filled stars, light gold for others
                            ?>
                            <i class="<?= $star_classes ?>" style="color: <?= $star_color ?>;"></i>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </td>
        <td>
            <div class="product-card__rating" style="display:flex;justify-content:center;">
                <div class="product-card__rating-stars">
                    <div class="rating__body">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?php 
                            // Determine the classes for the star
                            $star_classes = 'fas fa-star';  // Always show the star icon
                            $star_color = ($item['latest_review'] >= $i) ? 'gold' : '#bbbbbb'; // Gold for filled stars, light gold for others
                            ?>
                            <i class="<?= $star_classes ?>" style="color: <?= $star_color ?>;"></i>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </td>
        <td style="white-space:nowrap;"><a href="/webadmin/products_new/edit.php?action=edit&amp;id=<?=$item['product_id']?>" target="_blank">Edit</a></td>
    </tr>
    <?php } ?>
</table>

<br />
<br />

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
