
<?php foreach($todolist as $index=>$list){ ?>
    <tr>
        <td class="txt-cntr"><?=$list['id'] ?></td>
        <td class="txt-cntr"><?=$list['title'] ?></td>
        <td class="txt-cntr"><?=$list['start'] ?></td>
        <td class="txt-cntr"><?=$list['is_done']==1 ? 'Complete ':'Processed' ?></td>
        <td class="txt-cntr"><?=$list['created_at'] ?></td>
        <td class="txt-cntr"><?=$list['updated_at'] ?></td>
    </tr>
  <?php  } ?>


  <?php if(empty($todolist)){ ?>
<tr>
    <td colspan="100%" class="text-center mt-5">
        <div class="d-flex justify-content-center align-items-center flex-column mt-5">
            <i class="fas fa-exchange-alt fa-5x text-muted"></i>
            <h3 class="text-muted"><?= __("admin.no_data_found") ?></h3>
        </div>
    </td>
</tr>
<?php } ?>