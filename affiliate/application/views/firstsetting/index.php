<div class="card">
    <div class="card-header bg-secondary text-white">
        <h5><?= __('admin.first_settings') ?></h5>
    </div>
    <div class="card-body">
        <div class="container-fluid row justify-content-center">
            <div class="col-md-6">
                <!-- Step Tabs -->
                <div class="stepwizard-row setup-panel">
                    <ul class="nav nav-pills flex-column flex-sm-row tab-container" role="tablist" id="TabsSteps">
                        <li class="nav-item flex-sm-fill text-sm-center me-1">
                            <a class="nav-link active bg-secondary show" onclick="getStep(1)" data-step="1" data-bs-toggle="tooltip" title="General settings" role="tab">1</a>
                        </li>
                        <li class="nav-item flex-sm-fill text-sm-center me-1">
                            <a class="nav-link" onclick="getStep(2, 1)" data-step="2" data-bs-toggle="tooltip" title="Admin Email" role="tab">2</a>
                        </li>
                        <li class="nav-item flex-sm-fill text-sm-center me-1">
                            <a class="nav-link" onclick="getStep(3, 2)" data-step="3" data-bs-toggle="tooltip" title="Mail Email" role="tab">3</a>
                        </li>
                        <li class="nav-item flex-sm-fill text-sm-center me-1">
                            <a class="nav-link <?= isset($missing['currency']) ? 'step-alert' : '' ?>" onclick="getStep(4, 3)" data-step="4" data-bs-toggle="tooltip" title="Currency & Language" role="tab">4</a>
                        </li>
                        <li class="nav-item flex-sm-fill text-sm-center me-1">
                            <a class="nav-link" onclick="getStep(5, 4)" data-step="5" data-bs-toggle="tooltip" title="Change Password" role="tab">5</a>
                        </li>
                        <li class="nav-item flex-sm-fill text-sm-center me-1">
                            <a class="nav-link" onclick="getStep(6, 5)" data-step="6" data-bs-toggle="tooltip" title="Thank You" role="tab">6</a>
                        </li>
                    </ul>
                </div>

                <!-- Card -->
                <div class="card mt-3">
                    <!-- Card Header -->
                    <div class="card-header">
                        <h5>Step Content</h5>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="stepwizard-body-inner tab-content">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Steps script -->
<script type="text/javascript">
function setInitialTab() {
    let lastStep = localStorage.getItem('lastStep');
    if (lastStep) {
        getStep(lastStep, false);
    } else {
        getStep(1, false); // Your default tab
    }
}

function makeTabActive(number) {
    $('#TabsSteps .nav-link').removeClass('active bg-primary');
    $('#TabsSteps .nav-link').addClass('bg-secondary text-white');
    let targetTab = $(`#TabsSteps .nav-link[data-step="${number}"]`);
    targetTab.removeClass('bg-secondary');
    targetTab.addClass('active bg-primary text-white');
}

function getStep(number, save) {
    let formData = new FormData();
    if ($("#stepwizard-form").length) {
        formData = new FormData($("#stepwizard-form")[0]);
    }

    formData = formDataFilter(formData);
    formData.append("number", number);
    formData.append("save", save);

    $.ajax({
        url: '<?= base_url("firstsetting/get_step") ?>',
        type: 'POST',
        dataType: 'json',
        data: formData,
        processData: false,
        contentType: false,
        success: function(json) {
            if (json['html']) {
                $('.stepwizard-body-inner').html(json['html']);
            }

            let $container = $("#stepwizard-form");
            $container.find(".has-error").removeClass("has-error");
            $container.find("span.text-danger").remove();

            if (json['errors']) {
                $.each(json['errors'], function(i, j) {
                    let $ele = $container.find(`[name="${i}"]`);
                    if ($ele) {
                        $ele.parents(".form-group").addClass("has-error");
                        $ele.after(`<span class='text-danger'>${j}</span>`);
                    }
                });
            } else {
                // Make the tab active
                makeTabActive(number);

                // Save the last active step to localStorage
                localStorage.setItem('lastStep', number);
            }
        },
        error: function() {
            // Handle error appropriately
        }
    });
}

$(document).ready(function() {
    setInitialTab();
});



<?php if (isset($missing['currency'])) { ?>
    getStep(4);
<?php } ?>
</script>
