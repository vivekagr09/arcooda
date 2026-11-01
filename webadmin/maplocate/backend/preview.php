<?php
session_start();
$id_map_sel = $_SESSION['id_map_sel'];
?>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-2">
            <div class="card-body p-0">
                <div style="display: none;margin-bottom:-10px;" id="iframe_div"></div>
            </div>
        </div>
    </div>
</div>

<script>
    (function($) {
        "use strict";
        window.id_user = '<?php echo $id_user; ?>';
        window.id_map = '<?php echo $id_map_sel; ?>';
        $(document).ready(function () {
            var container_h = $('#content-wrapper').height() - 170;
            preview(window.id_map,container_h);
        });
        $(window).resize(function () {
            var container_h = $('#content-wrapper').height() - 170;
            $('#iframe_div iframe').attr('height',container_h+'px');
        });
    })(jQuery);
</script>