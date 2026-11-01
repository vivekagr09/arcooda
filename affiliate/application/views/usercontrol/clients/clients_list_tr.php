<?php
foreach($clientslist as $clients){ ?>
<tr>
    <td class="txt-cntr"><?php echo $clients['id'];?></td>
    <td>
        <?php 
            $clientFullName = $clients['firstname']." ".$clients['lastname'];
            echo strlen($clientFullName) > 20 ? substr($clientFullName,0,20)."..." : $clientFullName;
        ?>
    </td>
    <td><?php echo $clients['ref_user'];?></td>
    <td class="txt-cntr"><?php echo $clients['email'];?></td>
    <td class="txt-cntr"><?php echo $clients['phone'];?></td>
    <td class="txt-cntr"><?php echo $clients['username'];?></td>
    <td class="txt-cntr"><?php echo c_format($clients['amount']); ?></td>
    <td class="txt-cntr"><?php echo __('user.type_'. $clients['type']);?></td>
    <td class="txt-cntr"> 
    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#clientModel<?= $clients['id']?>">
        <i class="bi bi-info-circle"></i></button>

        <!-- Modal -->
        <div class="modal fade" id="clientModel<?= $clients['id']?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title"><?= __('user.client_info') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <ul class="list-group list-group-flush text-left">
                    <li class="list-group-item">
                        <strong><?= __('user.firstname') ?></strong>
                        <?php echo $clients['firstname'];?>
                    </li>
                    <li class="list-group-item">
                        <strong><?= __('user.lastname') ?></strong>
                        <?php echo $clients['lastname'];?>
                    </li>
                    <li class="list-group-item">
                        <strong><?= __('user.refer_user') ?></strong>
                        <?php echo $clients['ref_user'];?>
                    </li>
                    <li class="list-group-item">
                        <strong><?= __('user.email') ?></strong>
                        <?php echo $clients['email'];?>
                    </li>
                    <li class="list-group-item">
                        <strong><?= __('user.phone') ?></strong>
                        <?php echo $clients['phone'];?>
                    </li>
                    <li class="list-group-item">
                        <strong><?= __('user.username') ?></strong>
                        <?php echo $clients['username'];?>
                    </li>
                    <li class="list-group-item">
                        <strong><?= __('user.sales') ?></strong>
                        <?php echo c_format($clients['amount']); ?>
                    </li>
                    <li class="list-group-item">
                        <strong><?= __('user.type') ?></strong>
                        <?php echo __('user.type_'. $clients['type']);?>
                    </li>
                    <li class="list-group-item">
                        <strong><?= __('user.country') ?></strong>
                        <?php echo !empty($clients['country_name']) ? $clients['country_name'] : "<i class='text-muted'>".__('user.not_available')."</i>";?>
                    </li>
                    <li class="list-group-item">
                        <strong><?= __('user.state') ?></strong>
                        <?php echo !empty($clients['state_name']) ? $clients['state_name'] : "<i class='text-muted'>".__('user.not_available')."</i>";?>
                    </li>
                    <li class="list-group-item">
                        <strong><?= __('user.city') ?></strong>
                        <?php echo !empty($clients['City']) ? $clients['City'] : "<i class='text-muted'>".__('user.not_available')."</i>";?>
                    </li>
                    <li class="list-group-item">
                        <strong><?= __('user.postal_code') ?></strong>
                        <?php echo !empty($clients['Zip']) ? $clients['Zip'] : "<i class='text-muted'>".__('user.not_available')."</i>";?>
                    </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <a class="btn btn-info viewShipping" data-id="<?php echo $clients['id'];?>" href="#" title="Click to view Shiping detatils"><i class="bi bi-truck"></i></a>
    </td>
</tr>
<?php } ?>