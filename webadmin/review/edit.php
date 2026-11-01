<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';
$review = reviewFromDatabase($posted['id']);
$member = memberFromDatabase($review['user_id']);
$product = productFromDatabase($review['product_id']);
 $screenshot =  reviewGenericsFromDatabase($posted['id'],'`product_reviews_images`','id','review_id');
if(isset($screenshot) && is_array($screenshot)){
    foreach ($screenshot as $k => $value) {
          $screenshot[$k]['url']= sprintf(
                '/reviewimage/%s-%s-%s-%d-%d.jpg',
                $product['slug'],
                'review',
                $review['user_country'] ? getCountryName($v['user_country']) : 'Australia',
                $review['product_id'],
                $value['id']
            );  
    }
}

$status_list = [
    'y' => 'Approved',
    'n' => 'Not Approved',
];

?>
<?= formatHTMLTitle('Edit Review') ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
<form accept-charset="utf-8" enctype="multipart/form-data" autocomplete="off" method="post" action=".?action=do_edit" id="dashboardReview">
	  <input type="hidden" id="product_id" name="product_id" value="<?= $review['product_id'] ?>" />
        <input type="hidden" id="id" name="id" value="<?= $review['id'] ?>" />
        <input type="hidden" name="user_id" value="<?= $review['user_id'] ?>" />
        <input type="hidden" name="user_name" value="<?= $review['user_name'] ?>" />
        <input type="hidden" id="product-image" value='<?= json_encode($screenshot) ?>'>
        <div id="attached-file"></div>
        <div id="removed-file"></div>
	<table class="table_fields">
		 <tr>
    <th>Review Stars :</th>
    <td><select name="reviews" style="width:100%;">
		<?php
		foreach ([5=>"5 Stars Rating",
				    4=> "4 Stars Rating",
				    3=> "3 Stars Rating",
				    2=> "2 Stars Rating",
				    1=> "1 Stars Rating"
					] as $k => $v) {
		?>
		      <option style="font-family:courier;" value="<?= htmlspecialchars($k) ?>"<?= ($review['reviews'] == $k) ? ' selected="selected"' : '' ?>><?= str_replace(' ', '&nbsp;', htmlspecialchars($v)) ?></option>
		<?php
		}
		?>
		    </select></td>
	</tr>
	 <tr>
	    <th>User Review:</th>
	    <td><textarea  style="width:100%" name="comment" value="<?= htmlspecialchars($review['comment']) ?>" row="5"><?= htmlspecialchars($review['comment']) ?></textarea></td>
	  </tr>
	 <tr>
	    <th>Private Feedback:</th>
	    <td><textarea  style="width:100%" name="feedback" value="<?= htmlspecialchars($review['feedback']) ?>" row="5"><?= htmlspecialchars($review['feedback']) ?></textarea></td>
	  </tr>
	  <tr>
	    <th>Attachments:</th>
	    <td>
	    	<div id="dropzone-container">
                <label for="image-upload">Upload Images:</label>
                <div class="dropzone" id="my-dropzone"></div>
            </div>
	    </td>
	  </tr>
	  <tr>
	    <th>Status :</th>
	    <td><select onchange="this.style.backgroundColor=((this.value=='n')?'#FF0000':'#00FF00');" style="width:100%" name="status"><?= formatHTMLOptions($status_list, $review['status']) ?></td>
	  </tr>
   </table>
    <div style="text-align:center;padding: 20px;">
		<input type="submit" value="Update" />
	</div>
</form>
<script type="text/javascript">
	$(document).ready(function(){
		var product_id = "<?= $review['product_id'] ?>"; 
        var elementId =  document.getElementById('my-dropzone');
        if(elementId && elementId.dropzone){
             elementId.dropzone.destroy();
        }
        var myDropzone = new Dropzone("#my-dropzone", {
        url: '/contact/review_attachment.php?id=' + product_id,
        maxFilesize: 2, // Maximum file size in MB
        acceptedFiles: 'image/*', // Accept only images
        addRemoveLinks: true, // Allow file removal
        dictRemoveFile: "Remove", // Customize the remove link text
        autoProcessQueue: false, // Disable automatic upload on file selection
        parallelUploads: 5, // Allow multiple files to be uploaded at once

        init: function() {
            var dropzoneInstance = this;

            // Preload already uploaded images
            var uploadedImages = Object.values(JSON.parse($('#product-image').val()));
            uploadedImages.forEach(function(image) {
                // Create the mock file
                var mockFile = {id:image.id ,name: image.name, size: image.size };

                // Add the mock file to the Dropzone
                dropzoneInstance.emit("addedfile", mockFile);

                // Set the file thumbnail (the second argument is the URL of the thumbnail)
                dropzoneInstance.emit("thumbnail", mockFile, image.url);

                // Mark the file as complete
                dropzoneInstance.emit("complete", mockFile);

                // Optionally add a remove link for each file
                dropzoneInstance.files.push(mockFile); // Add to Dropzone's files array
            });

            // Handling form submission
            $('#dashboardReview').on('submit', function(e) {
                e.preventDefault();

                // Check if there are files to upload
                if (dropzoneInstance.getQueuedFiles().length > 0) {
                     dropzoneInstance.on('queuecomplete', function() {
                        // Once files are processed, submit the form
                        $('#dashboardReview').off('submit'); // Remove the event handler to avoid an infinite loop
                        $('#dashboardReview').submit(); // Submit the form traditionally
                    });
                    dropzoneInstance.processQueue(); 
                } else {
                   $(this).off('submit'); // Remove the event handler to avoid infinite loop
                   $(this).submit(); // Submit the form traditionally
                }
            });

            this.on("success", function(file, response) {
                $('#attached-file').append('<input type="hidden" name="attachments[]" value="'+response+'">');
            });

            // Event triggered when a file is removed
            this.on("removedfile", function(file) {
                if(file.id){
                     $('#removed-file').append('<input type="hidden" name="attachmentsremoved[]" value="'+file.id+'">');
                }
                // Optionally handle the file removal on the server-side
            });
        }
    });
	})
</script>