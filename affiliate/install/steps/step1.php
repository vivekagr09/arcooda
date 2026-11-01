<div class="row bs-wizard" style="border-bottom:0;">
    <div class="col-xs-6 bs-wizard-step active -complete">
        <div class="text-center bs-wizard-stepnum">Step 1</div>
        <div class="progress"><div class="progress-bar"></div></div>
        <a href="#" class="bs-wizard-dot"></a>
        <div class="bs-wizard-info text-center">Installation</div>
    </div>

    <div class="col-xs-6 bs-wizard-step disabled">
        <div class="text-center bs-wizard-stepnum">Step 2</div>
        <div class="progress"><div class="progress-bar"></div></div>
        <a href="#" class="bs-wizard-dot"></a>
        <div class="bs-wizard-info text-center">Installation complete</div>
    </div>
</div>

<div class="row">
    <div class="col-sm-1"></div>
    <div class="col-sm-10">
<?php 
    function getData($key,$default = ''){
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }

    function getError($key,$error){
        return isset($error[$key]) ? '<div class="text-danger">'. $error[$key] .'</div>'  : '';
    }

    $allow_installed = true;

    $serverReq = checkReq();
    foreach ($serverReq as $key => $value) {
        //echo "<div class='alert alert-danger'>". $value ."</div>";
    }
?>
    </div>
    <div class="col-sm-1"></div>
</div>
  
<div class="row">
    <div class="col-sm-1"></div>
    <div class="col-sm-6">
        <div class="main-body">
            <form id="register_form">
                <?php if($checkIsInstall){  ?>
                    <div class="alert alert-info">
                        <ul>
                        <li>Re-type your codecanyon mail account, Codecanyon license, Current Database details, and press continue.</li>
                        <li>If you don't have a codecanyon license for this domain,<br> Please <a class="badge badge-info" href="https://codecanyon.net/item/affiliate-management-system/25393355If" target="_blank">BUY</a> a new license .</li>
                    </ul>
                    </div>
                <?php } ?>

                <div class="form-group">
                    
                    <input type="text" name="purchase_code" class="form-control" placeholder="Codecanyon Purchase Code">
                </div>

                <div class="row">
                    <div class="form-group col-sm-6">
                        
                        <input type="text" name="email" class="form-control" placeholder="Codecanyon account mail">
                    </div>
                    <div class="form-group col-sm-6">
                        
                        <input type="text" name="username" class="form-control" placeholder="Username" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group required col-sm-6">                  
                        <div class="">
                            <input type="text" name="db_hostname" class="form-control" placeholder="Database Hostname" 
                            value="<?= getData('db_hostname', 'localhost') ?>" id="input-db-hostname" class="form-control">
                            <?= isset($error) ? getError('db_hostname', $error) : '' ?>
                        </div>
                    </div>
                        <div class="form-group required col-sm-6">
                            <div class="">
                                <input type="text" name="db_port" class="form-control" placeholder="Database Port" value="<?= getData('db_port', '3306') ?>" id="input-db-port" class="form-control">
                                <?= isset($error) ? getError('db_port', $error) : '' ?>
                            </div>
                        </div>
                </div>

                <div class="form-group required">
                    <div class="">
                        <input type="text" name="db_username" value="<?= getData('db_username') ?>" id="input-db-username" placeholder="Database Username" class="form-control">
                        <?= isset($error) ? getError('db_username', $error) : '' ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="">
                        <input readonly="" onfocus="this.removeAttribute('readonly');" onblur="this.setAttribute('readonly','readonly');" type="password" name="db_password" value="<?= getData('db_password') ?>" id="input-db-password" placeholder="Database Password" class="form-control">
                    </div>
                </div>
                <div class="form-group required">
                    <div class="">
                        <input type="text" name="db_database" value="<?= getData('db_database') ?>" id="input-db-database" placeholder="Database Name" class="form-control">
                        <?= isset($error) ? getError('db_error', $error) : '' ?>
                        <?= isset($error) ? getError('db_database', $error) : '' ?>
                    </div>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Continue</button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-sm-4"> 
        <div class="main-body">
            <div class="right-details">
                <h4>Server Requirement</h4>
                <div>
                    <ul class="server-reqirement">
                        <li class="<?= (array_key_exists('php', $serverReq) || version_compare(PHP_VERSION, '7.2.0', "<=")) ? 'error' : 'success' ?>" >PHP Version <?= PHP_VERSION; ?> <?= (version_compare(PHP_VERSION, '7.2.0', "<=")) ? '(PHP > 7.2.0)' : '' ?></li>
                        <li class="<?= array_key_exists('curl', $serverReq) ? 'error' : 'success' ?>" >Curl</li>
                        <li class="<?= array_key_exists('openssl_encrypt', $serverReq) ? 'error' : 'success' ?>" >Openssl Encrypt</li>
                        <li class="<?= array_key_exists('mysqli', $serverReq) ? 'error' : 'success' ?>" >Mysqli</li>
                        <li class="<?= array_key_exists('ipapi', $serverReq) ? 'error' : 'success' ?>" >IP API</li>
                        <li class="<?= array_key_exists('ziparchive', $serverReq) ? 'error' : 'success' ?>" >ZipArchive</li>
                        <li class="<?= array_key_exists('gzip', $serverReq) ? 'error' : 'success' ?>" >Gzip compression</li>
                        <li class="<?= array_key_exists('allow_url_fopen', $serverReq) ? 'error' : 'success' ?>" >allow_url_fopen</li>
                        <li class="<?= is_ssl() ? 'success' : 'error' ?>" ><?= is_ssl() ? 'SSL' : 'Non SSL' ?></li>
                        <li class="<?= extension_loaded('gd') ? 'success' : 'error' ?>" ><?= extension_loaded('gd') ? 'GD Library Installed' : 'No GD Library Installed' ?></li>
                            <li class="<?= array_key_exists('max_input_vars', $serverReq) ? 'error' : 'success' ?>" >Max Input Vars <?= ini_get('max_input_vars'); ?> <?= (ini_get('max_input_vars') < 1000) ? '(Should be >= 1000)' : '' ?>
                            </li>
                            <li class="<?= array_key_exists('upload_max_filesize', $serverReq) ? 'error' : 'success' ?>" >
                                Upload Max Filesize <?= ini_get('upload_max_filesize'); ?> 
                                <?= (intval(ini_get('upload_max_filesize')) < 128) ? '(Should be >= 128M)' : '' ?>
                            </li>
                            <li class="<?= array_key_exists('post_max_size', $serverReq) ? 'error' : 'success' ?>" >Post Max Size <?= ini_get('post_max_size') ?> <?= (intval(str_replace('M', '', ini_get('post_max_size'))) < 128) ? '(Should be >= 128M)' : '' ?>
                            </li>
                    </ul>
                </div>
            </div>
        </div>              
    </div>
</div>
    
<script type="text/javascript">
    $("#register_form").submit(function(){
        $this = $(this);
        $.ajax({
            url:'proccess.php',
            type:'POST',
            dataType:'json',
            data:$this.serialize()+'&page=step2',
            beforeSend:function(){$this.find("button[type=submit]").btn("loading");},
            complete:function(){$this.find("button[type=submit]").btn("reset");},
            success:function(json){
                if(json['html']){
                    $("#main").html(json['html']);
                }

                $this.find(".has-error").removeClass("has-error");
                $this.find("span.text-danger").remove();                
                if(json['errors']){
                    $.each(json['errors'], function(i,j){
                        $ele = $this.find('[name="'+ i +'"]');
                        if($ele){
                            $ele.parents(".form-group").addClass("has-error");
                            $ele.after("<span class='text-danger'>"+ j +"</span>");
                        }
                    })
                }
            },
        })

        return false;
    })
    $('[name="purchase_code"]').change(function(){
        $this = $(this);
        $form = $("#register_form");
        $.ajax({
            url:'codecanyon.php',
            type:'POST',
            dataType:'json',
            data:{
                code: $this.val()
            },
            success:function(json){
                $form.find(".has-error").removeClass("has-error");
                $form.find("span.text-danger").remove();                
                if(json['errors']){
                    $('[name="username"]').val('');
                    $.each(json['errors'], function(i,j){
                        $ele = $form.find('[name="'+ i +'"]');
                        if($ele){
                            $ele.parents(".form-group").addClass("has-error");
                            $ele.after("<span class='text-danger'>"+ j +"</span>");
                        }
                    })
                }else{
                    if(json.response.buyer){
                        $('[name="username"]').val(json.response.buyer);
                    }
                }
            },
        })

        return false;
    })
</script>