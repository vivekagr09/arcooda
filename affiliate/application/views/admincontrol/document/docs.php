<?php if(false){ ?>
    <div class="page-content-wrapper ">
    	<div class="container_">
    		<?php echo $doc_config['content']; ?>
    	</div>
    </div>
<?php } ?>	

<style>

.responsive{
    width:75%;
    height:75%;

}

.accordion {
    background-color: #ffffff;
    color: #000;
    cursor: pointer;
    padding: 6px;
    margin-top:20px;
    width: 100%;
    border: none;
    text-align: left;
    outline: none;
    font-size: 14px;
    transition: 0.4s;
}


.accordion.active, .accordion:hover {
    background-color: #F0F8FF; 
}

.panel {
    padding: 0 18px;
    display: none;
    background-color: white;
    overflow: hidden;
    margin-top: 3px;
    margin-bottom: 10px;
}

.icon-bar {
    width: 100%;
    background-color: #555;
    overflow: auto;
}

.icon-bar a {
    float: right;
    width: 20%;
    text-align: center;
    padding: 12px 0;
    transition: all 0.3s ease;
    color: white;
    font-size: 36px;
}

.img{
    width:50%;
    height:50%;
}

</style>

<br>
<h2><strong><?= __('admin.how_i_can_questions') ?></strong></h2>
<button class="accordion"><?= __('admin.how_do_i_integrate') ?> <strong><?= __('admin.woo_commerce') ?></strong> <?= __('admin.with_affiliate_script') ?><i class="fa fa-plus" style="float:right"></i></button>
<div class="panel">
<div class="embed-responsive embed-responsive-16by9">
<object data="https://www.youtube.com/embed/onOLJoT7EEQ"></object>
</div>
</div>


<button class="accordion"><?= __('admin.how_do_i_integrate') ?> <strong><?= __('admin.general_lead') ?></strong> <?= __('admin.with_affiliate_script') ?><i class="fa fa-plus" style="float:right"></i></button>
<div class="panel">
<div class="embed-responsive embed-responsive-16by9">
<object data="https://www.youtube.com/embed/vxL05j0SFZ4"></object>
</div>
</div>

<button class="accordion"><?= __('admin.how_do_i_integrate') ?> <strong><?= __('admin.general_click') ?></strong> <?= __('admin.with_affiliate_script') ?><i class="fa fa-plus" style="float:right"></i></button>
<div class="panel">
<div class="embed-responsive embed-responsive-16by9">
<object data="https://www.youtube.com/embed/RrV1_4cuH3Y"></object>
</div>
</div>


<button class="accordion"><?= __('admin.how_do_i_integrate') ?> <strong><?= __('admin.open_cart') ?></strong> <?= __('admin.with_affiliate_script') ?><i class="fa fa-plus" style="float:right"></i></button>
<div class="panel">
<div class="embed-responsive embed-responsive-16by9">
<object data="https://www.youtube.com/embed/8m4nPOZCMdU"></object>
</div>
</div>


<button class="accordion"><?= __('admin.how_i_can_use') ?> <strong><?= __('admin.opay_payment') ?></strong> <?= __('admin.gateway_in_digital_store') ?><i class="fa fa-plus" style="float:right"></i></button>
<div class="panel">
<div class="embed-responsive embed-responsive-16by9">
<object data="https://www.youtube.com/embed/MxicvCpyvtM"></object>
</div>
</div>

<button class="accordion"><?= __('admin.how_i_can') ?> <strong><?= __('admin.disable') ?></strong> <?= __('admin.the_digital_store') ?><i class="fa fa-plus" style="float:right"></i></button>
<div class="panel">
<div class="embed-responsive embed-responsive-16by9">
<object data="https://www.youtube.com/embed/360eI_KOnvE"></object>
</div>
</div>

<button class="accordion"><?= __('admin.how_i_can') ?> <strong><?= __('admin.disable') ?></strong> <?= __('admin.the_registration_form') ?><i class="fa fa-plus" style="float:right"></i></button>
<div class="panel">
<div class="embed-responsive embed-responsive-16by9">
<object data="https://www.youtube.com/embed/u9tSYaEgvpQ"></object>
</div>
</div>


<button class="accordion"><?= __('admin.how_i_can') ?> <strong><?= __('admin.disable') ?></strong> <?= __('admin.the_mlm_system') ?><i class="fa fa-plus" style="float:right"></i></button>
<div class="panel">
<div class="embed-responsive embed-responsive-16by9">
<object data="https://www.youtube.com/embed/AwZQbALt_Co"></object>
</div>
</div>

<button class="accordion"><?= __('admin.how_i_can') ?> <strong><?= __('admin.set') ?></strong> <?= __('admin.no_mlm_level_system') ?><i class="fa fa-plus" style="float:right"></i></button>
<div class="panel">
<div class="embed-responsive embed-responsive-16by9">
<object data="https://www.youtube.com/embed/qp0GFZy4S90"></object>
</div>
</div>

<button class="accordion"><?= __('admin.how_i_can') ?> <strong><?= __('admin.test') ?></strong> <?= __('admin.server_status') ?><i class="fa fa-plus" style="float:right"></i></button>
<div class="panel">
<div class="embed-responsive embed-responsive-16by9">
<object data="https://www.youtube.com/embed/hV9zIFw5W0Q"></object>
</div>
</div>

<button class="accordion"><?= __('admin.how_i_can') ?> <strong><?= __('admin.add_multiple_bank_account') ?></strong> <?= __('admin.to_the_digital_store') ?><i class="fa fa-plus" style="float:right"></i></button>
<div class="panel">
<div class="embed-responsive embed-responsive-16by9">
<object data="https://www.youtube.com/embed/TH5GnR7j8i4"></object>
</div>
</div>


<button class="accordion"><?= __('admin.how_i_can') ?> <strong><?= __('admin.invite_anyone') ?></strong> <?= __('admin.to_be_my_affiliate') ?><i class="fa fa-plus" style="float:right"></i></button>
<div class="panel">
<div class="embed-responsive embed-responsive-16by9">
<object data="https://www.youtube.com/embed/RDAQp91G19w"></object>
</div>
</div>



<script>
var acc = document.getElementsByClassName("accordion");
var i;
for (i = 0; i < acc.length; i++) {
    acc[i].addEventListener("click", function() {
        this.classList.toggle("active");
        var panel = this.nextElementSibling;
        if (panel.style.display === "block") {
            panel.style.display = "none";
        } else {
            panel.style.display = "block";
        }
    });
}
</script>

