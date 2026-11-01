<style>
	.ssi-imgToUploadTable{
		width: auto !important;
	}
	.ssi-previewBox {
    	width: 98%!important;
	}
</style>
<link href="https://www.arcooda.com/webadmin/products_new/ssi-uploader/styles/ssi-uploader.min.css" rel="stylesheet"/>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://www.arcooda.com/webadmin/products_new/ssi-uploader/js/ssi-uploader.min.js"></script>
<div id="colour-div">
	<div id="content-div"></div>

<div id="upload-wrapper">
    <div id="uploader-container-file">
	   <input type="file" id="ssi-uploader" name="myInputName" multiple >
    </div>
</div>
</div>
<script type="text/javascript">
	// $('#ssi-uploader').ssi_uploader();
    let uploaderInitialized = false;
    function initializeUploader(){
        if (uploaderInitialized) {
            $('.ssi-uploader').remove();
            $('#uploader-container-file').append('<input type="file" id="ssi-uploader" name="myInputName" multiple >');
        }
        var invoiceid = "<?= isset($invoice['id']) ? $invoice['id'] : 0 ?>";
    	var uploader = $('#ssi-uploader').ssi_uploader({
            url: '/webadmin/invoice/templates/bulk_screenshot.php?id=' + invoiceid,
            maxFileSize: 100, // Maximum file size in MB
            allowed: ['jpg', 'jpeg', 'png'], // Allowed file types
            maxNumberOfFiles: 20, // Maximum number of files to upload
            onEachUpload: function(fileInfo) {
                $('#colour-div').css('border', '5px solid #9ed39e');
                $('#content-div').html('You need to save this page for the images to be saved to this invoice.');
                $('#content-div').css({
                    'padding': '20px',
                    'background-color': '#9ed39e'
                });
            },
            onComplete: function(result) {
                window.location.reload();
            }

        });
        uploaderInitialized = true;
    }
    initializeUploader();
    $('#image-type').change(function(){
        initializeUploader();
    });
$('#upload-wrapper').on('drop', function(event) {
    $('#colour-div').css('border', '5px solid pink');
   $('#content-div').html('You need to press the upload button for the below images to be uploaded to this invoice.')
   $('#content-div').css({
	    'padding': '20px',
	    'background-color': 'pink'
	});
});

$('#ssi-uploader').on('change', function() {
    $('#colour-div').css('border', '5px solid pink');
   $('#content-div').html('You need to press the upload button for the below images to be uploaded to this invoice.')
   $('#content-div').css({
	    'padding': '20px',
	    'background-color': 'pink'
	});
});
</script>