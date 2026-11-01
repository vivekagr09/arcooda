<?php
session_start();
$id_map = $_SESSION['id_map_sel'];
?>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-cog"></i> <?php echo _("Details"); ?></h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="name"><?php echo _("Name"); ?> *</label>
                            <input type="text" class="form-control" id="name" value="" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="id_category_parent"><?php echo _("Parent Category"); ?></label>
                            <select class="form-control" id="id_category_parent">
                                <option id="0">--</option>
                                <?php echo get_parent_categories($id_map,0); ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="default_selected"><?php echo _("Default selected"); ?></label><br>
                            <input type="checkbox" id="default_selected" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function($) {
        "use strict";
        window.id_category = 0;
        window.category_need_save = false;

        $("input").change(function(){
            window.category_need_save = true;
        });

        $(window).on('beforeunload', function(){
            if(window.category_need_save) {
                var c=confirm();
                if(c) return true; else return false;
            }
        });
    })(jQuery);
</script>