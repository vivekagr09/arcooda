<div class="row">
    <div class="col-xl-12">
        <div class="card mb-3">
            <div class="card-header py-1">
                <span class="d-none bg-success ms-0 mini-stat-icon float-start"><i class="fa fa-bell"></i></span>
                <h2 class="h5 mb-0 text-center text-uppercase"><?= __('admin.contact_us_details') ?></h2>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <ul class="list-inline row mb-0">
                        <li class="col-12">
                            <p class="mb-0 text-start text-muted"><?= $notification_details['notification_description']; ?></p>
                            <br>
                            <p class="mb-0 text-start text-muted">
                                <?php 
                                    $a = nl2br(str_replace(' ', '&nbsp;', htmlentities($notification_details['store_contactus_description'])), true); 
                                    echo $a;
                                ?>
                            </p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>