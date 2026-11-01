<!-- First row -->
<div class="row">
    <?php
    $first_row_categories = ['clicks', 'action_clicks'];
    foreach ($first_row_categories as $category) {
    ?>
    <div class="col-sm-6 mb-5">
        <div class="card" id="<?= $category ?>-small-card">
            <div class="card-header bg-primary text-white">
                <h5 class="text-center mb-0">
                    <span class="badge bg-secondary float-start fs-6"><?= (int)$statistics[$category . '_count'] ?></span>
                    <?= __('admin.' . $category . '_by_country') ?>
                </h5>
            </div>
            <div class="card-body">
                <?php if ((int)$statistics[$category . '_count'] > 0) { ?>
                    <div id="<?= $category ?>-chart-small" class="w-100" style="height:300px;"></div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#<?= $category ?>-large-modal">
                        <?= __('admin.view_larger') ?>
                    </button>
                <?php } else { ?>
                    <div class="text-center">
                        <i class="fas fa-exchange-alt fa-5x text-muted"></i>
                        <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- Large modal -->
        <div class="modal fade" id="<?= $category ?>-large-modal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-body">
                        <div id="<?= $category ?>-chart-large" style="height:500px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<!-- Second row -->
<div class="row">
    <?php
    $second_row_categories = ['sale', 'affiliate_user', 'client_user'];
    foreach ($second_row_categories as $category) {
    ?>
    <div class="col-sm-4 mb-5">
        <div class="card" id="<?= $category ?>-small-card">
            <div class="card-header bg-primary text-white">
                <h5 class="text-center mb-0">
                    <span class="badge bg-secondary float-start fs-6"><?= (int)$statistics[$category . '_count'] ?></span>
                    <?= __('admin.' . $category . '_by_country') ?>
                </h5>
            </div>
            <div class="card-body">
                <?php if ((int)$statistics[$category . '_count'] > 0) { ?>
                    <div id="<?= $category ?>-chart-small" class="w-100" style="height:300px;"></div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#<?= $category ?>-large-modal">
                        <?= __('admin.view_larger') ?>
                    </button>
                <?php } else { ?>
                    <div class="text-center">
                        <i class="fas fa-exchange-alt fa-5x text-muted"></i>
                        <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- Large modal -->
        <div class="modal fade" id="<?= $category ?>-large-modal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-body">
                        <div id="<?= $category ?>-chart-large" style="height:500px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<script>
	var statistics = <?php echo json_encode($statistics); ?>;
</script>

<script>
$(document).ready(function() {
    var colors = ['#007bff', '#6c757d', '#28a745', '#17a2b8', '#ffc107'];

    function createMorrisDonut(elementId, data) {
        Morris.Donut({
            element: elementId,
            data: data,
            resize: true,
            colors: colors
        });
    }

    const categories = ['clicks', 'action_clicks', 'sale', 'affiliate_user', 'client_user'];
    categories.forEach(category => {
        // Check if statistics[category] exists
        if (statistics[category]) {
            var data = Object.keys(statistics[category]).map(function(country) {
                return { label: country, value: statistics[category][country] };
            });

            if ($("#" + category + "-chart-small").length) {
                createMorrisDonut(category + "-chart-small", data);
            }

            // Modal large charts
            $('#' + category + '-large-modal').on('shown.bs.modal', function() {
                createMorrisDonut(category + "-chart-large", data);
            });
        } else {
            // If no data, display "No Data" message in the chart area
            $("#" + category + "-chart-small").html('<i class="fas fa-exchange-alt fa-5x text-muted"></i><h3 class="text-muted">No Data Found</h3>');
        }
    });
});
</script>