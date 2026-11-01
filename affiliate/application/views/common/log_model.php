<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title m-0">
                <?php if(isset($title2)){ ?>
                    <ul>
                        <li target='.tab1' class="active"><?= $title ?> <small>(<?= count($data) ?>)</small></li>
                        <li target='.tab2'><?= $title2 ?> <small>(<?= count($data2) ?>)</small></li>
                    </ul>
                <?php } else { 
                    
                    echo  $title. " <small>(". count($data) .")</small>";
                } ?>
            </h6>
            <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body m-0 p-0">

            <?php if($type == 'members'){ ?>
                <div style="height: 400px;overflow: auto;overflow:scroll;-webkit-overflow-scrolling:touch;">
                    <?php  if(empty($data)){ ?>
                        <div class="text-center m-4 empty-data" style="">
                            <div class="d-flex justify-content-center align-items-center flex-column mt-5">
                                 <i class="fas fa-exchange-alt fa-5x text-muted"></i>
                                 <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="table-responsive">
                        <ul class="log-data">
                            <li>    
                                <div class="log-index">...</div>
                                <div><b><?= __('admin.username')?></b></div>
                                <div><b><?= __('admin.first_last')?></b></div>
                                <div><b><?= __('admin.email')?></b></div>
                                <div><b><?= __('admin.country')?></b></div>
                                <div><b><?= __('admin.date')?></b></div>
                            </li>
                            <?php $index = 0; foreach ($data as $key => $value) { ?>
                                <li>    
                                    <div class="log-index"><?= ++$index ?></div>
                                    <div><?= $value['username'] ?></div>
                                    <div><?= $value['name'] ?></div>
                                    <div><?= $value['email'] ?></div>
                                    <div><img width="30px" src="<?= $value['flag'] ?>"></div>
                                    <div><?= date("d-m-Y h:i A",strtotime($value['created_at'])) ?></div>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            <?php } else { ?>
                <div class="-tab tab1" style="height: 400px;overflow: auto;overflow:scroll;-webkit-overflow-scrolling:touch;">
                    <?php  if(empty($data)){ ?>
                        <div class="text-center m-4 empty-data" style="">
                            <div class="d-flex justify-content-center align-items-center flex-column mt-5">
                                 <i class="fas fa-exchange-alt fa-5x text-muted"></i>
                                 <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="table-responsive">
                        <ul class="log-data">
                            <?php $index = 0; foreach ($data as $key => $value) { ?>
                                <li>    
                                    <div class="log-index"><?= ++$index ?></div>
                                    <div class="log-date"><?= date("d-m-Y h:i A",strtotime($value['created_at'])) ?></div>
                                    <div class="log-comment"><?= parseLogMessage($value['comment'],$value) ?></div>
                                    <div class="log-status"><?= isset($status[$value['status']]) ? $status[$value['status']] : $value['status'] ?></div>
                                    <div class="log-amount"><?= c_format($value['amount']) ?></div>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>

                <?php if(isset($title2)) { ?>
                    <div class="-tab tab2" style="height: 400px;overflow: auto;overflow:scroll;-webkit-overflow-scrolling:touch;display: none;">
                        <?php  if(empty($data2)){ ?>
                            <div class="text-center m-4 empty-data" style="">
                                <div class="d-flex justify-content-center align-items-center flex-column mt-5">
                                     <i class="fas fa-exchange-alt fa-5x text-muted"></i>
                                     <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="table-responsive">
                            <ul class="log-data" >
                                <?php $index = 0; foreach ($data2 as $key => $value) { ?>
                                    <li>    
                                        <div class="log-index"><?= ++$index ?></div>
                                        <div class="log-date"><?= date("d-m-Y h:i A",strtotime($value['created_at'])) ?></div>
                                        <div class="log-comment"><?= parseLogMessage($value['comment'],$value) ?></div>
                                        <div class="log-status"><?= isset($status[$value['status']]) ? $status[$value['status']] : $value['status'] ?></div>
                                        <?php if(isset($value['amount'])) { ?>
                                            <div class="log-amount"><?= c_format($value['amount']) ?></div>
                                        <?php } ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
</div>

<script type="text/javascript">
    $("#log-widzard .modal-title ul li").on('click',function(){
        $("#log-widzard .modal-body .-tab").hide();
        var t = $(this).attr("target");
        
        $("#log-widzard .modal-body").find(t).show();

        $("#log-widzard .modal-title ul li").removeClass('active');
        $(this).addClass('active');
    })
</script>