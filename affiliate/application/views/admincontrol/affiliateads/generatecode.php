<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-body">
			<h4 class="modal-title"><?php echo $title ?></h4>
          	<a href="<?php echo $link ?>">
				<?php if(isset($image)){ ?>
	                <img src="<?php echo $image ?>" class="img-fluid" alt="">
	            <?php } ?>
	            <div class="video">
	              <?php if(isset($video)){ echo $video; }  ?>
	              
	            </div>
          	</a>
            <p><?php echo $description ?></p>
		</div>
		<textarea style="opacity: 0;" name="" id="dummyInput"></textarea>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-bs-dismiss="modal"><?= __('admin.close') ?></button>
			<button type="button" onclick="copyCode()" class="btn btn-primary"><?= __('admin.copy_html') ?></button>
		</div>
	</div>
</div>
<script type="text/javascript">
	function copyCode() {
		var $html = $("<div>" +  $("#modal-image .modal-body").html() + "</div>");
		$html.find('[class]').removeAttr("class")
		$("#dummyInput").val($html.html());
	  	var copyText = document.getElementById("dummyInput");
	  	copyText.select();
	  	document.execCommand("copy");
	  	alert('<?= __('admin.html_code_copied') ?>');
	}
</script>
<script>
    <?php
        if(isset($_SESSION['setLocalStorageAffiliateAjax'])) {
            $setLocalStorageAffiliateAjax = json_decode($_SESSION['setLocalStorageAffiliateAjax']);
            $_SESSION['localStorageAffiliate'] = (int) $setLocalStorageAffiliateAjax[0];
            ?>
            var setLocalStorageAffiliateAjax = <?= $_SESSION['setLocalStorageAffiliateAjax'] ?>;
            setWithExpiry("affiliate_id", setLocalStorageAffiliateAjax[0], setLocalStorageAffiliateAjax[1]);
            <?php
            
            unset($_SESSION['setLocalStorageAffiliateAjax']);
        }
    ?>

    function setWithExpiry(key, value, ttl) {
    	const now = new Date()
    	const item = {
    		value: value,
    		expiry: now.getTime() + ttl,
    	}
    	localStorage.setItem(key, JSON.stringify(item))
    }
    
    function getWithExpiry(key) {
    	const itemStr = localStorage.getItem(key)
    
    	if (!itemStr) {
    		return 1
    	}
    
    	const item = JSON.parse(itemStr)
    	const now = new Date()
    
    	if (now.getTime() > item.expiry) {
    		localStorage.removeItem(key)
    		return 1
    	}
    	return item.value
    }
</script>
