<style>
	.ssi-imgToUploadTable{
		width: auto !important;
	}
</style>
<link href="ssi-uploader/styles/ssi-uploader.min.css" rel="stylesheet"/>
<script src="ssi-uploader/js/ssi-uploader.min.js"></script>
<input type="file" id="ssi-uploader" name="myInputName" multiple >
<script type="text/javascript">
	// $('#ssi-uploader').ssi_uploader();
	var productNumber = <?= json_encode($_GET["id"]); ?>;
	$('#ssi-uploader').ssi_uploader({
		  	url: '/webadmin/products_new/bulk_screenshot.php?id=' + productNumber,
	        maxFileSize: 100, // Maximum file size in MB
	        allowed: ['jpg', 'jpeg', 'png', 'bmp', 'gif'], // Allowed file types
	        maxNumberOfFiles: 10, // Maximum number of files to upload
	        onEachUpload: function(fileInfo) {
	            console.log('Uploading:', fileInfo); // Log each file being uploaded
	        },
	        onComplete: function(result) {
	            window.location.reload()
	        }
	});
</script>