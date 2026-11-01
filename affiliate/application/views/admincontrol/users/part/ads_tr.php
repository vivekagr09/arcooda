<?php $i=1; foreach($googleads as $list){  ?>

	<tr id="row_<?= $list['id'];?>">
        <td><?= $i; ?></td>
        <td><?= $list['client_key']; ?></td>
        <td><?= $list['unit_key']; ?></td>
        <td><?= ads_google_status($list['ad_section']); ?></td>
        <td>
        <a  class="btn btn-sm btn-primary"  href="javascript:void(0)" onclick="editAds(<?= $list['id'];?>,'<?= $list['client_key'];?>','<?= $list['unit_key'];?>',<?= $list['ad_section'];?>)">
            <i class="fa fa-edit cursors" aria-hidden="true"></i>
        </a>
        <button data-id="<?= $list['id'];?>" data-toggle="tooltip" data-original-title="<?= __('admin.delete') ?>" class="btn btn-sm btn-danger btn-delete2" >
        <i class="fa fa-trash-o cursors" aria-hidden="true"></i>
    </button>
        </td>
    </tr>

<?php $i++;} ?>