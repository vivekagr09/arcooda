

<?php

include_once __DIR__ . '/common.inc.php';
moduleLoad('product'); //productFromDatabase
moduleLoad('review'); //productFromDatabase
if (array_key_exists('action', $_REQUEST) && 'send' == $_REQUEST['action'] && $_GLOBALS['member']['id'] > 0) {
    // Prepare the insert array
    $insert_wanted = array();
    $issues = array();

    foreach ([
		'product_id',
		'user_name',
		'reviews',
		'comment',
	] as $k) {
		if ('' === trim($_REQUEST[$k] ?? '')) {
			$issues[$k] = '' . str_replace('_',' ', $k) . ' field is required';
		}
	}

    if(count($issues)==0){
        $posted = reviewFromPost($posted);
        $posted['item'] = $posted;
        $posted['action'] = 'do_add';
        $reviewId = reviewToDatabase($posted);
            if ($reviewId) {
                echo '<div style="text-align: center; font-size: 31px; background-color: #95db95; color: white; ">review inserted successfully</div>'; 
            } else {
                $issues['db_error'] = 'Database insertion failed: ' . $DB->error;
            }
        }
}

$reviews_list = [
    "1" => "1 Stars Rating",
    "2" => "2 Stars Rating",
    "3" => "3 Stars Rating",
    "4" => "4 Stars Rating",
    "5" => "5 Stars Rating",
];

$user_name = strtoupper(substr($_GLOBALS['member']['first_name'], 0, 1)) .'.'. strtoupper(substr($_GLOBALS['member']['last_name'], 0, 1));
include_once __DIR__ . '/header.inc.php';
?>
<html>
	<header>
		<title>Review</title>
        <link rel="stylesheet" href="/css/style.min.css" />
        <link href="/webadmin/products_new/ssi-uploader/styles/ssi-uploader.min.css" rel="stylesheet"/>       
        
        <!-- fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,400i,500,500i,700,700i" />

        <!-- css -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" integrity="sha256-UhQQ4fxEeABh4JrcmAJ1+16id/1dnlOEVCFOxDef9Lw=" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.3/photoswipe.min.css" integrity="sha256-LWdHSKWG7zv3DTpee8YAgoTfkj3gNkfauF624h4P2Nw=" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.3/default-skin/default-skin.min.css" integrity="sha256-Q9bBMw/rHRRag46GDWY84J3elDNc8JJjKXL9tIC4oe8=" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" integrity="sha256-FdatTf20PQr/rWg+cAKfl6j4/IY3oohFAJ7gVC3M34E=" crossorigin="anonymous" />
        <style type="text/css">
        li{
            font-size: 16px;
        }
        .color-red {
            color: #ff0000;
        }
        .review_form{
            background: #fafafa;
            padding: 15px;
            font-size: 15px;
        }
        .review_form .form-control{
            font-size: 15px;
            font-weight: 400;
            letter-spacing: .5px;
            border: 1px solid #a7a7a7;
            border-radius: 5px;
        }
        .review_form b{
            font-weight: 500;
            font-size: 15px;
            margin-bottom: 0px;
        }

        .review_form {
            background: #fafafa;
            padding: 20px; /* Overall padding for the form */
            max-width: 100%; /* Set the maximum width to 50% */
            margin: auto; /* Center the form */
        }

        .form-group {
            margin-bottom: 20px; /* Space between form groups */
        }

        .color-red {
            color: #ff0000;
        }

        .form-control {
            padding: 10px; /* Padding inside text areas and inputs */
        }
        .ssi-imgToUploadTable{
            width: auto !important;
        }
        select .form-control {
            background-image: none !important;
        }
        </style>
	</header>
	<body> 
<br />

<div class="container">
    <div class="row">
        <div class="col-sm-12 col-md-9 col-lg-7 col-xl-6 col-xxl-5" style="margin:auto;">
            <?php
                if(count($issues)>0){
                    echo '<ul style="color:red;">';
                    foreach ($issues as $key => $issue) {
                        echo '<li>'.$issue.'</li>';
                    }
                    echo '</ul>';
                }
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <form class="<?= htmlspecialchars($popup_class) ?> review_form" id="review_form" method="post" action="" onsubmit="return validateForm(this);">
                <h4 class="text-center" style="margin-bottom:50px;">Write A Review - <?= htmlspecialchars($_REQUEST['product_name'] ?? '') ?></h4>
                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($_REQUEST['product_id'] ?? ''); ?>" />
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_GLOBALS['member']['member_id'] ?? ''); ?>" />
                <input type="hidden" name="user_name" value="<?php echo htmlspecialchars($user_name ?? ''); ?>" />
                <input type="hidden" name="user_country" value="<?php echo htmlspecialchars($_GLOBALS['member']['country'] ?? ''); ?>" />
                <div class="form-group">
                    <label for="reviews"><b>Review Stars</b></label></br>
                    <select class="form-control form-control-lg" id="reviews" name="reviews" style="max-width:50%;background-image: none;" required>
                        <option value="" disabled selected>Select your rating</option>
                        <?= formatHTMLOptions($reviews_list) ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="comment" class="col-form-label"><b>Your Review</b> <span class="">(for online display)</span></label></br>
                    <textarea class="form-control <?= htmlspecialchars($popup_class) ?>" id="comment" name="comment" rows="3" placeholder="Please leave your comments." required><?php echo htmlspecialchars($_REQUEST['comment'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="feedback" class="col-form-label"><b>Private Feedback</b> <span class="">(Confidential, for Office Use Only)</span></label></br>
                    <textarea class="form-control <?= htmlspecialchars($popup_class) ?>" id="feedback" name="feedback" rows="3" placeholder="Please leave your feedback."><?php echo htmlspecialchars($_REQUEST['feedback'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="feedback" class="col-form-label"><b>Attachments</b></label></br>
                    <div style="width: 95%;margin-left: auto; margin-right: auto;margin-bottom: 20px;" id="review_form">
                        <input type="file" id="ssi-uploader" name="myInputName" multiple />
                    </div>
                </div>
                <div id="attached-file"></div>
                <button type="submit" class="btn btn-primary btn-lg" id="send-btn" name="action" value="send">Post Your Review</button>
            </form>
        </div>
    </div>
</div>


<br />

</body>
<footer>
    <script src="https://code.jquery.com/jquery-3.4.0.min.js" integrity="sha256-BJeo0qm959uMBGb65z40ejJYGSgR7REI4+CW1fNKwOg=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js" integrity="sha256-pTxD+DSzIwmwhOqTFN+DB+nHjO4iAsbgfyFq5K5bcE0=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/13.1.4/nouislider.min.js" integrity="sha256-6NGkvvdjRehdAHFa9mUmcVBX8gSDO1CFTnfNiLMrPNY=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.3/photoswipe.min.js" integrity="sha256-ePwmChbbvXbsO02lbM3HoHbSHTHFAeChekF1xKJdleo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.3/photoswipe-ui-default.min.js" integrity="sha256-UKkzOn/w1mBxRmLLGrSeyB4e1xbrp4xylgAWb3M42pU=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js" integrity="sha256-d/edyIFneUo3SvmaFnf96hRcVBcyaOy96iMkPez1kaU=" crossorigin="anonymous"></script>

    <!-- script for ReCaptch  -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="/webadmin/products_new/ssi-uploader/js/ssi-uploader.min.js"></script>  
    <script type="text/javascript">
        // $('#ssi-uploader').ssi_uploader({
            // 	  	url: '/webadmin/products_new/bulk_screenshot.php?id=' + productNumber,
            //         maxFileSize: 100, // Maximum file size in MB
            //         allowed: ['jpg', 'jpeg', 'png', 'bmp', 'gif'], // Allowed file types
            //         maxNumberOfFiles: 10, // Maximum number of files to upload
            //         onEachUpload: function(fileInfo) {
                //             console.log('Uploading:', fileInfo); // Log each file being uploaded
                //         },
                //         onComplete: function(result) {
                    //             window.location.reload()
                    //         }
                    // });
        $(function(){
            var productNumber = <?= json_encode($_GET["product_id"]); ?>;
            $('#ssi-uploader').ssi_uploader({
                url: '/contact/review_attachment.php?id=' + productNumber,
                maxFileSize: 100, // Maximum file size in MB
                allowed: ['jpg', 'jpeg', 'png', 'bmp', 'gif'], // Allowed file types
                maxNumberOfFiles: 5, // Maximum number of files to upload
                onEachUpload: function(fileInfo) {
                    if(fileInfo['uploadStatus'] == 'success'){
                        $('#attached-file').append('<input type="hidden" name="attachments[]" value="'+fileInfo['responseMsg']+'">');
                    }
                }
            });
            $('input, textarea').on('focus', function(){
                $(this).css("border-color", "#52D017");
            });
    
            $('input, textarea').on('blur', function(){
                $(this).css("border-color", "rgb(169, 169, 169)");
            })
    
            function validateForm(form) {
                // Clear previous error messages
                const errorMessages = document.querySelectorAll('.error-message');
                errorMessages.forEach(message => message.remove());
    
                // Validate review stars
                const reviewStars = form.reviews.value;
                if (!reviewStars) {
                    displayError(form.reviews, "Please select a rating.");
                    return false; // Prevent form submission
                }
    
                // Validate comment
                const comment = form.comment.value.trim();
                if (comment.length < 5) {
                    displayError(form.comment, "Your review must be at least 5 characters long.");
                    return false; // Prevent form submission
                }
    
                // Validate feedback
                const feedback = form.feedback.value.trim();
                if (feedback.length < 5) {
                    displayError(form.feedback, "Your feedback must be at least 5 characters long.");
                    return false; // Prevent form submission
                }
    
                // If all validations pass
                return true; // Allow form submission
            }
    
            function displayError(element, message) {
                const errorMessage = document.createElement('div');
                errorMessage.className = 'error-message text-danger'; // Add class for styling
                errorMessage.innerText = message;
                element.classList.add('is-invalid'); // Bootstrap styling for invalid input
                element.parentNode.insertBefore(errorMessage, element.nextSibling);
            }
        });
    </script>

</footer>
</html>
