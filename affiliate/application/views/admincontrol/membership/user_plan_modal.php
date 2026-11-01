<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title m-0"><?= __('admin.edit_user_membership') ?></h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <?php if ($MembershipSetting['status']) { ?>
      <nav>
        <div class="nav nav-pills nav-justified" id="TabsNav" role="tablist">
          <li role="presentation" class="active nav-item">
            <a class="nav-link active bg-primary text-white show" id="mmu-currentplan" href="#nav-home" aria-controls="nav-home" role="tab" data-bs-toggle="tab"><?= __('admin.current_plan') ?></a>
          </li>
          <li role="presentation" class="nav-item">
            <a class="nav-link bg-secondary text-white show" id="mmi-newplan" href="#nav-profile" aria-controls="nav-profile" role="tab" data-bs-toggle="tab"><?= __('admin.change_plan') ?></a>
          </li>
        </div>
      </nav>

      <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="mmu-currentplan">
          <?php if (isset($is_lifetime_plan) && $is_lifetime_plan) { ?>
            <div class="card-body">
              <h4 class="text-center text-success"><?= __('admin.lifetime_free_membership') ?></h4>
              <p class="text-center text-muted"><?= __('admin.user_have_lifetime_free_membership_info') ?></p>
            </div>
          <?php } else if (isset($plan) && $plan) { ?>
            <div class="card-body">
              <h4 class="text-success"><?= __('admin.plan') ?>: <?= $plan->plan ? $plan->plan->name : '' ?></h4>
            </div>
            <ul class="list-group list-group-flush">
              <li class="list-group-item">
                <?= __('admin.plan_date') ?>
                <span class="float-end text-primary">
                  <?= dateFormat($plan->started_at, 'd F Y') . " to " . $plan->expire_text ?>
                </span>
              </li>
              <li class="list-group-item">
                <?= __('admin.remain_days') ?>
                <span class="float-end text-primary">
                  <?php
                  $remain = $plan->remainDay();
                  if ($remain === 'lifetime') {
                    echo '<span class="fs-1">&infin;</span>';
                  } else {
                    echo $remain;
                  }
                  ?>
                </span>
              </li>
              <li class="list-group-item">
                <?= __('admin.plan_status') ?>
                <span class="float-end text-primary">
                  <?= $plan->status_text ?>
                </span>
              </li>
              <li class="list-group-item">
                <?= __('admin.active') ?>
                <span class="float-end text-primary">
                  <?= $plan->active_text ?>
                </span>
              </li>
            </ul>
            <div class="card-body">
            </div>
          <?php } else { ?>
            <div class="modal-body">
              <p class="text-center"><?= __('admin.user_have_no_any_membership_plan') ?></p>
            </div>
          <?php } ?>
        </div>

        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="mmi-newplan">
          <form class="change-plan-form">
            <ul class="list-group">
              <?php foreach ($plan_lists as $key => $p) { ?>
                <?php if (($p->user_type == 1 && $is_vendor == 0) || ($p->user_type == 2 && $is_vendor == 1)) { ?>
                  <li class="list-group-item">
                    <label class="m-0">
                      <input <?= $plan->plan_id == $p->id ? 'checked' : '' ?> value="<?= $p->id ?>" type="radio" name="new_planid">
                      <?= $p->name ?>
                    </label>
                  </li>
                <?php } ?>
              <?php } ?>
            </ul>
            <div class="modal-body">
              <input type="hidden" name="user_id" value="<?= $user->id ?>">
              <div class="mb-3">
                <label class="form-label"><?= __('admin.status') ?></label>
                <select class="form-select" name="status_id">
                  <option value=""><?= __('admin.select_status') ?></option>
                  <?php foreach (App\MembershipPlan::$status_list as $key => $value) { ?>
                      <option value="<?= $key ?>">
                        <?php   
                        if ($value == 'Received') {
                          echo __('admin.received');
                        } elseif ($value == 'Complete') {
                          echo __('admin.complete');
                        } elseif ($value == 'Total not match') {
                          echo __('admin.total_not_match');
                        } elseif ($value == 'Denied') {
                          echo __('admin.denied');
                        } elseif ($value == 'Expired') {
                          echo __('admin.expired');
                        } elseif ($value == 'Failed') {
                          echo __('admin.failed');
                        } elseif ($value == 'Processed') {
                          echo __('admin.processed');
                        } elseif ($value == 'Refunded') {
                          echo __('admin.refunded');
                        } elseif ($value == 'Reversed') {
                          echo __('admin.reversed');
                        } elseif ($value == 'Voided') {
                          echo __('admin.voided');
                        } elseif ($value == 'Canceled Reversal') {
                          echo __('admin.cancel_reversal');
                        } elseif ($value == 'Waiting For Payment') {
                          echo __('admin.waiting_for_payment');
                        } elseif ($value == 'Pending') {
                          echo __('admin.pending');
                        } elseif ($value == 'Active') {
                          echo __('admin.active');
                        } else {
                          echo $value;
                        }
                        ?>
                      </option>
                      <?php } ?>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label"><?= __('admin.comment') ?></label>
                <textarea class="form-control" name="comment"></textarea>
              </div>
            </div>
          </form>
          <div class="modal-footer">
            <button class="btn btn-primary btn-change-plan"><?= __('admin.change_plan') ?></button>
          </div>
        </div>
      </div>
    <?php } else { ?>
      <div class="modal-body">
        <p class="text-center"><?= __('admin.membership_is_not_active') ?></p>
      </div>
    <?php } ?>
  </div>
</div>

<script type="text/javascript">
  $(".btn-change-plan").click(function(){
    $this = $(this);
    $.ajax({
      url: '<?= base_url("membership/user_plan_modal") ?>',
      type: 'POST',
      dataType: 'json',
      data: $(".change-plan-form").serialize(),
      beforeSend: function() {
        $this.addClass("disabled").attr("aria-disabled", true).button('loading');
      },
      complete: function() {
        $this.removeClass("disabled").attr("aria-disabled", false).button('reset');
      },
      success: function(json) {
        $container = $('.change-plan-form');
        $container.find(".is-invalid").removeClass("is-invalid");
        $container.find("span.invalid-feedback").remove();

        if (json['reload']) {
          window.location.reload();
        }

        if (json['errors']) {
          $.each(json['errors'], function(i, j) {
            $ele = $container.find('[name="' + i + '"]');
            if ($ele) {
              $ele.addClass("is-invalid");
              if ($ele.parent(".input-group").length) {
                $ele.parent(".input-group").after("<span class='invalid-feedback'>" + j + "</span>");
              } else {
                $ele.after("<span class='invalid-feedback'>" + j + "</span>");
              }
            }
          });
        }
      },
    });
  });
</script>

