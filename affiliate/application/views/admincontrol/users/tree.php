<div class="row">
  <div class="col-lg-12 col-md-12">
  	<div class="card m-b-30">
  		<div class="card-body">
        <div style="overflow-x: scroll; overflow-y:hidden;" class="dummyscroll"><div>&nbsp;</div></div>
        <div class="table-responsive orig-scroll">
          <?php 
            function buildTree($data,$clickable){
               foreach ($data as $key => $value) {
                  if($clickable)
                    $html .= '<li class="userlisttree-clickable"><span data-id="'.$value['id'].'">'. $value['name'] .'</span>';
                  else 
                    $html .= '<li><span>'. $value['name'] .'</span>';
                 
                    $t = buildTree($value['children'],true);
                    if($t) $html .= "<ul>{$t}</ul>";
                 $html .= '</li>';
               }
               return $html;
            }
            echo "<figure class='top-scroll'>";
            echo "<ul class='usertree'>". buildTree($userslist,false) ."</ul>";
            echo "</figure>";
          ?>
        </div>
  		</div>
  	</div>
  </div>
</div>
<script type="text/javascript">
  $(".dummyscroll > div").width($(".top-scroll")[0].scrollWidth)

  $(".dummyscroll").on('scroll',function(){
    $(".orig-scroll").scrollLeft($(".dummyscroll").scrollLeft());
  });
  $(".orig-scroll").on('scroll',function(){
    $(".dummyscroll").scrollLeft($(".orig-scroll").scrollLeft());
  });

  $(".userlisttree-clickable span").on('click',function(){
    var id = $(this).data('id');
    $("#userslisttree-deteil_"+id).modal('toggle');
  })
</script>

<?php $k=1; foreach($userslistDetail as $users){ ?>
  <?php 
    $valueStored = json_decode($users['value']); 
    $fieldsShown = [];
  ?>
<div class="modal fade" id="userslisttree-deteil_<?= $users['id'] ?>">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title mt-0"><?=  $users['username'].' '.__('admin.commissions') ?></h4>
        <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
         <div class="row">
          <div class='col-sm-6 col-md-6 m-b-10'><b><?= __('admin.user_level') ?>:</b> 
            <?php
              $userPlan = App\MembershipUser::select('award_level.level_number','award_level.sale_comission_rate')->join('membership_plans','membership_plans.id','=','membership_user.plan_id')->join('award_level','award_level.id','=','membership_plans.level_id','left')->where('is_active',1)->where('user_id',$users['id'])->first();
              
              $user_level;

              if($award_level['status']){
                if($membership['status'] && $userPlan->level_number){
                  $user_level = $userPlan->level_number;
                } else if($userdetails['level_id']){
                  foreach ($levels as $key => $value){
                    if($userdetails['level_id'] == $value['id']){
                      $user_level = $value['level_number'];
                    }
                  }
                } else {
                  $user_level = __('admin.default');
                }
              }

              echo $user_level;
            ?>
          </div>

          <div class='col-sm-6 col-md-6 m-b-10'><b><?= __('admin.clicks') ?>:</b> <?php echo (int)$users['click'] + (int)$users['external_click'] + (int)$users['form_click']+ (int)$users['aff_click']; ?> / <?php echo c_format($users['click_commission']) ?></div>

          <div class='col-sm-6 col-md-6 m-b-10'><b><?= __('admin.action_click') ?>:</b> <?= (int)$users['external_action_click'] ?> / <?= c_format($users['action_click_commission']) ?></div>

          <div class='col-sm-6 col-md-6 m-b-10'><b><?= __('admin.sales_commissions') ?>:</b> <?php echo c_format($users['amount'] + $users['external_sale_amount']); ?> / <?php echo c_format($users['sale_commission']); ?></div>

          <div class='col-sm-6 col-md-6 m-b-10'><b><?= __('admin.paid_comm') ?>:</b> <?php echo c_format($users['paid_commition']); ?></div>

          <div class='col-sm-6 col-md-6 m-b-10'><b><?= __('admin.in_request') ?>:</b>  <?php echo c_format($users['in_request_commiton']); ?></div>

          <div class='col-sm-6 col-md-6 m-b-10'><b><?= __('admin.total') ?> <?= __('admin.commissions') ?>:</b> <?php echo c_format($users['all_commition']); ?></div>

          <?php $fieldsShown = [];  ?>

          <?php             $mobile_validation_done = false;

          foreach ($data as $key => $value) { 

            if($value['type'] == 'header') continue; 

            $mobile_validation = (isset($value['mobile_validation']) && $value['mobile_validation'] ) ? $value['mobile_validation'] : '';

              if($mobile_validation == 'true' && $mobile_validation_done == false) {
                $printableValue = $users['phone'];
                $mobile_validation_done = true;
              } else {
                $printableValue = isset($valueStored->{'custom_'.$value['name']}) ? $valueStored->{'custom_'.$value['name']} : null;
              }

            ?>
            <div class='col-sm-6 col-md-6 m-b-10'>
              <b><?= $value['label'] ?>:</b> <?php 
              
              if( $value['type'] == 'file') {
                if(is_array($valueStored->{'custom_'.$value['name']})) {
                  foreach ($valueStored->{'custom_'.$value['name']} as $fileName) {
                    echo '<a target="_blank" href="'.base_url().'assets/user_upload/'.$fileName.'">'.$fileName.'</a>';
                  }
                } else {
                  echo '<a target="_blank" href="'.base_url().'assets/user_upload/'.$valueStored->{'custom_'.$value['name']}.'">'.$valueStored->{'custom_'.$value['name']}.'</a>';
                }

                if(!isset($valueStored->{'custom_'.$value['name']}) || (empty($valueStored->{'custom_'.$value['name']}) && $valueStored->{'custom_'.$value['name']} != 0)) {
                  echo __('admin.no_files_uploaded');
                }
              } else {
                if(empty($printableValue) && $printableValue !== 0) {
                  echo __('admin.not_available');
                } else {
                  echo $printableValue; 
                }
              }
              ?>
            </div>


          <?php 
          if(isset($valueStored->{'custom_'.$value['name']})) {
            array_push($fieldsShown, 'custom_'.$value['name']);
          }

        } 

        ?>


        <?php

        foreach ($valueStored as $key => $value) {
          if(!in_array($key, $fieldsShown)) {

            if(str_contains($key,'existing') || str_contains($key,'hidden') || !str_contains($key, 'custom')) continue;

            echo "<div class='col-sm-6 col-md-6 m-b-10'><b>".explode('-', $key)[0].":</b>";

            $filecheck = ['png', 'gif', 'jpeg', 'jpg', 'PNG', 'GIF', 'JPEG', 'JPG', 'ICO', 'ico', 'pdf', 'docx', 'doc', 'ppt', 'xls', 'txt'];

            if(is_array($value)) {
              foreach ($value as $v) {

                $v_explode = explode('.', $v);

                if(sizeof($v_explode) == 2 && in_array($v_explode[1], $filecheck)) {
                  echo '<a target="_blank" href="'.base_url().'assets/user_upload/'.$v.'">'.$v.'</a>';
                } else {
                  echo $v;
                }

                
              }
            } else {
              $v_explode = explode('.', $value);

              if(sizeof($v_explode) == 2 && in_array($v_explode[1], $filecheck)) {
                echo '<a target="_blank" href="'.base_url().'assets/user_upload/'.$value.'">'.$value.'</a>';
              } else {
                echo $value;
              }
            }

            if(empty($value) && $value != 0) {
              echo __('admin.no_files_uploaded');
            }

            echo "</div>";
          }
        }


        ?>
      </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?= __('admin.footer_close') ?></button>
      </div>
    </div>
  </div>
</div>
<?php $k++; } ?>
