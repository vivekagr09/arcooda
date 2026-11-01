<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Localstorage extends MY_Controller{
	public function setLocaleStorage() {
		$current_link = $this->session->flashdata('current_link');
		$data = $this->session->flashdata('setLocalStorage');
		?>
		<script type="text/javascript">
			<?php foreach ($data as $key => $value) { ?>
				localStorage.setItem('<?= $key ?>', '<?= $value ?>');
			<?php  } ?>
			window.location.replace("<?= $current_link ?>");
		</script>
		<?php
	}

	public function getLocaleStorage() {
		$current_link = $this->session->flashdata('current_link');
		?>
		<script type="text/javascript">
			
			function allStorage() {
			    let values = "",
			        
		        keys = Object.keys(localStorage),
		    
		        i = keys.length;

			    while ( i-- ) {
			        values += "&"+keys[i]+"="+localStorage.getItem(keys[i]);
			    }

			    return values;
			}


			let localStorageData = allStorage();

			window.location.replace("<?= $current_link ?>"+localStorageData);
		</script>
		<?php
	}

	
}