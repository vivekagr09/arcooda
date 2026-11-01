<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h4><?= __('admin.manage_group') ?></h4>
				<a class="btn btn-primary" href="<?= base_url('admincontrol/usergroup/')  ?>"><?= __('admin.cancel') ?></a>
			</div>
			<div class="card-body">
				<form id="admin-form">
					<input type="hidden" name="group_id" value="<?= (!empty($group)?(int)$group->id:'') ?>">
					<div class="row justify-content-center">
						<div class="col-sm-6">
							<div class="form-group">
								<label><?= __('admin.group_name') ?></label>
								<input placeholder="<?= __('admin.enter_your_group_name') ?>" name="group_name" value="<?= !empty($group)?$group->group_name:''; ?>" class="form-control" type="text">
							</div>
						</div>
					</div>
					<div class="row justify-content-center">
						<div class="col-sm-6">
							<div class="form-group">
								<label><?= __('admin.group_description') ?></label>
								<textarea rows="8" placeholder="<?= __('admin.enter_group_description') ?>" name="group_description" class="form-control"><?= !empty($group)?$group->group_description:''; ?></textarea>
							</div>
						</div>
					</div>
					<div class="row justify-content-center">
						<div class="col-sm-6">
							<div class="form-group">
								<label><?= __('admin.group_image') ?></label>
								<div class="mb-3">
									<input class="form-control" id="uploadBtn" name="avatar" type="file">
								</div>
								<?php $avatar = $group->avatar != '' ? 'site/'.$group->avatar : 'no_image_available.png' ; ?>
								<img src="<?= base_url();?>assets/images/<?= $avatar; ?>" id="group_img" class="img-thumbnail" border="0" width="220px">
								<input type="hidden" name="oldfile" value="<?= !empty($group)?$group->avatar :''; ?>">
							</div>
						</div>
					</div>
					<div class="row justify-content-center">
						<div class="col-sm-12">
							<div class="form-group">
								<button type="button" class="btn btn-primary btn-submit"><?= __('admin.submit') ?></button>
								<span class="loading-submit"></span>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div> 
	</div> 
</div>


<script type="text/javascript">
document.querySelector("#uploadBtn").addEventListener("change", function(e) {
    if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.querySelector('#group_img').src = e.target.result;
        }
        reader.readAsDataURL(this.files[0]);
    }
});

// Handle form submission
document.querySelector(".btn-submit").addEventListener('click', function(evt){
    evt.preventDefault();
    
    // Initial setup for loading state
    setupLoadingState();
    
    // Fetch form and form data
    var form = document.querySelector("#admin-form");
    var formData = new FormData(form);

    // Perform the AJAX request
    var xhr = new XMLHttpRequest();
    configureXhr(xhr, form);

    xhr.open('POST', '<?= base_url('admincontrol/admin_group_form') ?>', true);
    xhr.send(formData);
});


// Setup initial loading state
function setupLoadingState() {
    var submitBtn = document.querySelector(".btn-submit");
    var loadingSubmit = document.querySelector('.loading-submit');
    
    submitBtn.innerHTML = "Loading...";
    loadingSubmit.style.display = "block";
}


// Configure the XMLHttpRequest
function configureXhr(xhr, form) {
    xhr.upload.onprogress = updateProgress;
    xhr.onloadstart = function() { 
        document.querySelector('.loading-submit').innerHTML = "Saving";
    };
    xhr.onloadend = resetLoadingState;
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            handleResponse(JSON.parse(xhr.responseText), form);
        }
    };
}

// Update progress
function updateProgress(e) {
    var percentComplete = Math.round((e.loaded * 100) / e.total);
    document.querySelector('.loading-submit').innerHTML = `${percentComplete}% Loading`;
}

// Reset loading state
function resetLoadingState() {
    var submitBtn = document.querySelector(".btn-submit");
    var loadingSubmit = document.querySelector('.loading-submit');
    
    submitBtn.innerHTML = "Submit";
    loadingSubmit.style.display = "none";
}

// Handle the response from the server
function handleResponse(result, form) {
    if (result['location']) {
        window.location = result['location'];
        return;
    }

    if (result['errors']) {
        displayErrors(result['errors'], form);
    }
}

// Display form errors
function displayErrors(errors, form) {
    Object.entries(errors).forEach(([key, value]) => {
        var element = form.querySelector(`[name="${key}"]`);
        if (element) {
            element.parentNode.classList.add("has-error");
            
            // Convert HTML to text
            var tempDiv = document.createElement("div");
            tempDiv.innerHTML = value;
            var plainText = tempDiv.textContent || tempDiv.innerText;

            // Create and append the error message
            var errorText = document.createElement("span");
            errorText.textContent = plainText;
            errorText.classList.add("text-danger");
            element.parentNode.appendChild(errorText);
        }
    });
}


</script>
