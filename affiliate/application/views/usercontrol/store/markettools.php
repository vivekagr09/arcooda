<?php
$db =& get_instance();
$userdetails=$db->userdetails();
?>

<style type="text/css">

    #product-list tr td {

        vertical-align: middle;
    } 
.market-heade>ul>li:first-child {
    width: 45px;
}
.market-heade>ul>li {
    font-size: 14px;
    font-weight: 500;
    width: 8%;
    color: #fff;
    font-weight: 500;
}
.market-heade>ul>li:nth-child(2) {
    font-size: 14px;
    font-weight: 500;
    width: 15%;
    color: #fff;
    font-weight: 500;
}
.market-heade>ul>li:nth-child(3) {
    font-size: 14px;
    font-weight: 500;
    width: 25%;
    color: #fff;
    font-weight: 500;
}
.market-heade>ul>li:nth-child(4) {
    font-size: 14px;
    font-weight: 500;
    width: 3%;
    color: #fff;
    font-weight: 500;
}
.market-heade>ul>li:last-child {
    width: 40%;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
}
.list-user-action input {
    color: #A8A8A8;
    font-size: 13px !important;
    padding-right: 10px;
    white-space: nowrap;
    width: auto !important;
    overflow: hidden;
    text-overflow: ellipsis;
}

</style>
<script src="<?= base_url('assets/plugins/qrcode.min.js') ?>"></script>
 
<div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-xl-3 col-lg-3 col-md-3">
                <div class="form-group">
                  <label class="form-label"><?= __('user.search_by_vendor') ?></label>
                  
                  <select class="form-select user_id" name="user_id">
                    <option value=""><?= __('user.all_ads') ?></option>
                    <?php foreach ($vendors_list as $key => $value) { ?>
                        <option value="<?= $value['id'] ?>"><?= $value['username'] ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="col-xl-3 col-lg-3 col-md-3">
                <div class="form-group">
                  <label class="form-label"><?= __('user.search_by_campaign_category') ?></label>
                  
                  <select class="form-select category_id" >
                    <option value=""><?= __('user.all_categories') ?></option>
                    <?php 
                        if(count($categories)>0)
                        {
                            $parentcategoyrid=0;
                                foreach ($categories as $key => $value)
                                {
                                    if($parentcategoyrid!=0 && $parentcategoyrid!=$value['pid'])
                                    { 
                                        ?>
                                        <?php        
                                    }
                                    if($parentcategoyrid!=$value['pid'])
                                    {
                                        ?>
                                      <option value="<?= $value['value'] ?>"><?= $value['label'] ?></option>  
                                        <?php
                                    }
                                    else 
                                    {
                                        ?>
                                        <option value="<?= $value['value'] ?>">--<?= $value['label'] ?></option>
                                        <?php 
                                    }
                                        $parentcategoyrid=$value['pid'];

                                } ?>
                            
                            <?php
                        }
                       ?>
                     
                  </select>
                </div>
              </div>
              <div class="col-xl-3 col-lg-3 col-md-3">
                <div class="form-group">
                  <label class="form-label"><?= __('user.search_by_campaign') ?></label>
                  <input class="table-search form-control ads_name" placeholder="Search" type="search">
                  
                </div>
              </div>
              <div class="col-xl-3 col-lg-3 col-md-3">
                <div class="form-group">
                  <label class="form-label"><?= __('user.search_by_store_category') ?></label>
                  <select class="form-select market_category_id" >
                    <option value=""><?= __('user.all_categories') ?></option>
                    <?php foreach ($store_categories as $key => $value) { ?>
                        <option value="<?= $value['value'] ?>"><?= $value['label'] ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12 col-xl-12">
            <div class="card-body mrketplacece">
              <div class="accordin-market">
                
                <div class="market-heade">
                  <ul>
                    
                    <li><?= __('user.image') ?></li>
                    <li class="name-text"><?= __('user.name') ?></li>
                    <li style="color: #fff !important;"><?= __('user.commission') ?></li>
                    <li class="offer-text"><?= __('user.view') ?></li>
                    <li class="offer-text"><?= __('user.ratio') ?></li>
                    <li ><?= __('user.actions') ?></li>
                  </ul>
                </div>
                <div class="accordion" id="product-list">
                  
                </div>
              </div>
            </div>
          </div>
          <div></div>
        </div>

</div>

<!--<div class="modal" id="model-codemodal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
</div>
</div>
</div>-->

<div class="modal fade" id="model-codemodal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered  scanner">
        <div class="modal-content ">
          <div class="modal-header">
            <h5 class="modal-title exampleModalLabel2" id="modal-title"><?= __('user.scanner') ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body py-4">
            
            
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary text-center btn-green" data-bs-dismiss="modal" aria-label="Close"><?= __('user.footer_close') ?></button>
          </div>
        </div>
      </div>
  </div>
<div class="modal" id="model-codeformmodal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close"><?= __('user.close') ?></button>
    </div>
</div>
</div>
</div>
<div class="modal fade" id="slugtting" data-backdrop="static" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content ">
          <form action="<?= base_url('/usercontrol/create_slug') ?>" method="post">
          <div class="modal-header">
            <h5 class="modal-title exampleModalLabel1"><?= __('user.create_slug'); ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="slug-wrapp">
            <div class="form-group">
              
              <label class="form-label" for="exampleInputText1"><?= __('user.slug'); ?></label>
              <input type="text" name="slug" class="form-control"  placeholder="<?= __('user.enter_slug_here') ?>">
              <input type="hidden" name="type" />
              <input type="hidden" name="related_id" />
              <input type="hidden" name="target" />
            
            </div>
            <div class="link-area align-items-center slug-url">
              <input type="text" readonly="readonly" class="form-control" >
              <a class="bt-all btn-warning" href="javascript:void(0)" title="<?= __('user.copied'); ?>" style="margin-left: 5px;">
                <span class="btn-inner">
                  <i class="far fa-copy" alt="<?= __('user.copy') ?>"></i>
                </span>
              </a> 
            </div>
          </div>
        </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-secondary"><?= __('user.create'); ?></button>
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal" aria-label="Close"><?= __('user.close'); ?></button>
           <button type="button" class="btn btn-primary btn-delete-slug"><?= __('user.delete'); ?></button>
          </div>
        </form>
        </div>
      </div>
    </div>
<div class="modal fade" id="integration-code"><div class="modal-dialog"><div class="modal-content"></div></div></div>
<?= $social_share_modal ?>
<script type="text/javascript">

    var xhr;
    function getPage(url,page){
        $this = $(this);

        if(xhr && xhr.readyState != 4) xhr.abort();
        xhr = $.ajax({
            url:url,
            type:'POST',
            dataType:'json',
            data:{
                market_category_id: $(".market_category_id").val(),
                category_id: $(".category_id").val(),
                ads_name: $(".ads_name").val(),
                vendor_id: $(".user_id").val(),
                dvl: 1,
                page:page,
            },
            beforeSend:function(){$(".btn-search").btn("loading");},
            complete:function(){$(".btn-search").btn("reset");},
            success:function(json){
                if(json['view']){
                    $("#product-list").html(json['view']);
                    $("#product-list").show();
                    $(".empty-div").addClass("d-none");

                } else {
                    $(".empty-div").removeClass("d-none");
                    $("#product-list").hide();
                }
            },
        })
    }

    $(".user_id,.category_id,.market_category_id, .display-vendor-links").on("change",function(){
        getPage('<?= base_url("usercontrol/store_markettools/") ?>',1);
    });
    $(".ads_name").on("keyup",function(){
        getPage('<?= base_url("usercontrol/store_markettools/") ?>',1);
    });
    
    getPage('<?= base_url("usercontrol/store_markettools/") ?>',1);



    $("#product-list").delegate(".get-code",'click',function(){
        $this = $(this);
        $.ajax({
            url:'<?= base_url("integration/tool_get_code/usercontrol") ?>',
            type:'POST',
            dataType:'json',
            data:{id:$this.attr("data-id")},
            beforeSend:function(){ $this.btn("loading"); },
            complete:function(){ $this.html("<i class='bi bi-code-slash'></i>"); },
            success:function(json){
                if(json['html']){
                    
                    $("#integration-code .modal-content").html(json['html']);
                    $("#integration-code").modal("show");
                }
            },
        })
    });

    $("#product-list").delegate(".get-terms",'click',function(){
        $this = $(this);
        $.ajax({
            url:'<?= base_url("integration/tool_get_terms/usercontrol") ?>',
            type:'POST',
            dataType:'json',
            data:{id:$this.attr("data-id")},
            beforeSend:function(){ $this.btn("loading"); },
            complete:function(){ $this.html("<i class='bi bi-info-square'></i>"); },
            success:function(json){
                if(json['html']){
                    
                    $("#integration-code .modal-content").html(json['html']);
                    $("#integration-code").modal("show");
                }
            },
        })
    });

    $("#product-list").delegate(".toggle-child-tr",'click',function(){
        $tr = $(this).parents("tr");
        $ntr = $tr.next("tr.detail-tr");

        if($ntr.css("display") == 'table-row'){
            $ntr.hide();
            $(this).find("i").attr("class","bi bi-plus-circle");
        }else{
            $(this).find("i").attr("class","bi bi-dash-circle");
            $ntr.show();
        }
    })
    $("#product-list").delegate(".show-more",'click',function(){
        $(this).parents("tfoot").remove();
        $("#product-list tr.d-none").hide().removeClass('d-none').fadeIn();
    });

    function generateCode(affiliate_id,t){
        $this = $(t);
        $.ajax({
            url:'<?php echo base_url();?>usercontrol/generateproductcode/'+affiliate_id,
            type:'POST',
            dataType:'html',
            beforeSend:function(){
                $this.btn("loading");
            },
            complete:function(){
             $this.html("<i class='fa-solid fa-code'></i>");
         },
         success:function(json){
            $("#modal-title").text('<?= __('user.copy_html')?>');
            $('#model-codemodal .modal-body').html(json);
            $("#model-codemodal").modal("show");
        },
    })
    }

    function generateCodeForm(form_id,t){ 
        $this = $(t);
        $.ajax({
            url:'<?php echo base_url();?>usercontrol/generateformcode/'+form_id,
            type:'POST',
            dataType:'html',
            beforeSend:function(){
                $this.btn("loading");
            },
            complete:function(){
                $this.html("<i class='fa-solid fa-code'></i>");
            },
            success:function(json){
                $("#modal-title").text('<?= __('user.copy_html')?>');
                $('#model-codeformmodal .modal-body').html(json);
                $("#model-codeformmodal").modal("show");
            },
        })
    }

    function downloadCode(t, id, type){
        $this = $(t);
        $.ajax({
            url:'<?php echo base_url();?>usercontrol/downloadToolCode/'+id+'/'+type,
            type:'POST',
            dataType:'html',
            beforeSend:function(){
                $this.btn("loading");
            },
            complete:function(){
                $this.html("<i class='fa-solid fa-download'></i>");
            },
            success:function(res){
                window.location.href = res;
            },
        })
    }

    $("#show_my_id").change(function(){
        if($(this).prop("checked")){
            $(".show-mega-link").removeClass("d-none");
            $(".show-tiny-link").addClass("d-none");
        } else {
            $(".show-mega-link").addClass("d-none");
            $(".show-tiny-link").removeClass("d-none");
        }
    })
    $(document).on('click','.qrcode',function(){
        $("#modal-title").text('<?= __('user.scanner')?>');
        $('#model-codemodal .modal-body').html("<span id='QRDataModal'></span>");
        $("#model-codemodal").modal("show");
        var qrdata = $(this).attr('data-id');
        var qrcode = new QRCode(document.getElementById("QRDataModal"), {
            text: qrdata,
            width: 128,
            height: 128,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });

    })
    $(document).on('click','.pagination-div ul li a',function(e){
        e.preventDefault();
       

        let page = $(this).data('ci-pagination-page');

        if(page)
          getPage('<?= base_url("usercontrol/store_markettools/") ?>',page);
      })
</script>