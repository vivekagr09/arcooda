<script>
    <?php
        if(isset($_SESSION['setLocalStorageAffiliate'])) {
            $setLocalStorageAffiliate = json_decode($_SESSION['setLocalStorageAffiliate']);
            $_SESSION['localStorageAffiliate'] = (int) $setLocalStorageAffiliate[0];
            ?>
            var setLocalStorageAffiliate = <?= $_SESSION['setLocalStorageAffiliate'] ?>;
            setWithExpiry("affiliate_id", setLocalStorageAffiliate[0], setLocalStorageAffiliate[1]);
            window.location.replace("<?= $redirect ?>");

            <?php
            
            unset($_SESSION['setLocalStorageAffiliate']);
            

        } else {
            
            ?>
            
            var xhttp = new XMLHttpRequest();
    
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                  window.location.replace("<?= $redirect ?>");
                }
            };
    
            xhttp.open("POST", "<?= base_url('store');?>", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("localStorageAffiliate="+getWithExpiry('affiliate_id'));
            
            <?php
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