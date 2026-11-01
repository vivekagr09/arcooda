<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title m-0"><?= __('admin.products') ?> (<?= $category['name'] ?> <?= count($products) ?>)</h6>
            <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body m-0 p-0">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?= __('admin.image') ?></th>
                            <th width="220px"><?= __('admin.product_name') ?></th>
                            <th><?= __('admin.price') ?></th>
                            <th><?= __('admin.sku') ?></th>
                            <th><?= __('admin.display') ?></th>
                            <th><?= __('admin.action') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $key => $product) { ?>
                            <tr>
                                <td><img width="30px" height="30px" src="<?php echo base_url('assets/images/product/upload/thumb/'. $product['product_featured_image']) ?>" ></td>
                                <td><?php echo $product['product_name'];?></td>
                                <td><?php echo c_format($product['product_price']); ?></td>
                                <td><?php echo $product['product_sku'];?></td>
                                <td class="txt-cntr"><?= $product['on_store'] == '1' ? __('admin.yes') : __('admin.no') ?></td>
                                <td class="txt-cntr">
                                    <?php if($product['is_campaign_product'] == 1) { ?>
                                        <a class="btn btn-sm btn-primary" onclick="return confirm('<?= __('admin.are_you_sure_to_edit') ?>');" href="<?php echo base_url();?>Productsales/update/<?php echo $product['product_id'];?>"><i class="fa fa-edit cursors" aria-hidden="true"></i></a>
                                    <?php } else { ?>
                                        <a class="btn btn-sm btn-primary" onclick="return confirm('<?= __('admin.are_you_sure_to_edit') ?>');" href="<?php echo base_url();?>admincontrol/updateproduct/<?php echo $product['product_id'];?>"><i class="fa fa-edit cursors" aria-hidden="true"></i></a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>

                        <?php if(!$products){ ?>
                            <tr>
                                <td colspan="100%" class="text-center"><?= __('admin.no_products_on_this_category') ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?= __('admin.close') ?></button>
        </div>
    </div>
</div>