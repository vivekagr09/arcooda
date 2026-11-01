<?php
session_start();
require_once("functions.php");
$role = get_user_role($_SESSION['id_user']);
?>

<?php if($role!='administrator'): ?>
<div class="text-center">
    <div class="error mx-auto" data-text="401">401</div>
    <p class="lead text-gray-800 mb-5">Permission denied</p>
    <p class="text-gray-500 mb-0">It looks like you found a glitch in the matrix...</p>
    <a href="index.php?p=dashboard">← Back to Dashboard</a>
</div>
<?php die(); endif; ?>

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card shadow mb-12">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-shield"></i> <?php echo _("User's Role Permissions"); ?></h6>
            </div>
            <div class="card-body" style="line-height: 1.0;">
                <p><b><?php echo _("ADMINISTRATOR"); ?></b>: <?php echo _("manage users | manage maps of all users"); ?></p>
                <p><b><?php echo _("EDITOR"); ?></b>: <?php echo _("they only manage the maps that are associated with them"); ?></p>
                <p><b><?php echo _("CUSTOMER"); ?></b>: <?php echo _("they only manage their own maps"); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <button <?php echo ($demo) ? 'disabled':''; ?> data-toggle="modal" data-target="#modal_new_user" class="btn btn-block btn-success"><i class="fa fa-plus"></i> <?php echo _("ADD USER"); ?></button>
    </div>
</div>

<div class="row mt-2">
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-body">
                <table class="table table-bordered table-hover" id="users_table" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th><?php echo _("Username"); ?></th>
                        <th><?php echo _("Role"); ?></th>
                        <th><?php echo _("Active"); ?></th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="modal_new_user" class="modal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo _("New User"); ?></h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="username"><?php echo _("Username"); ?></label>
                            <input type="text" class="form-control" id="username" />
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="role"><?php echo _("Role"); ?></label>
                            <select class="form-control" id="role">
                                <option id="customer"><?php echo _("Customer"); ?></option>
                                <option id="administrator"><?php echo _("Administrator"); ?></option>
                                <option id="editor"><?php echo _("Editor"); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password"><?php echo _("Password"); ?></label>
                            <input type="password" class="form-control" id="password" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="repeat_password"><?php echo _("Repeat Password"); ?></label>
                            <input type="password" class="form-control" id="repeat_password" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button <?php echo ($demo) ? 'disabled':''; ?> onclick="add_user();" type="button" class="btn btn-success"><i class="fas fa-plus"></i> <?php echo _("Create"); ?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo _("Close"); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    (function($) {
        "use strict";
        $(document).ready(function () {
            $('#users_table').DataTable({
                "responsive": true,
                "scrollX": true,
                "processing": true,
                "searching": false,
                "serverSide": true,
                "ajax": "ajax/get_users.php",
                "language": {
                    "decimal":        "",
                    "emptyTable":     "<?php echo _("No data available in table"); ?>",
                    "info":           "<?php echo sprintf(_("Showing %s to %s of %s entries"),'_START_','_END_','_TOTAL_'); ?>",
                    "infoEmpty":      "<?php echo _("Showing 0 to 0 of 0 entries"); ?>",
                    "infoFiltered":   "<?php echo sprintf(_("(filtered from %s total entries)"),'_MAX_'); ?>",
                    "infoPostFix":    "",
                    "thousands":      ",",
                    "lengthMenu":     "<?php echo sprintf(_("Show %s entries"),'_MENU_'); ?>",
                    "loadingRecords": "<?php echo _("Loading"); ?>...",
                    "processing":     "<?php echo _("Processing"); ?>...",
                    "search":         "<?php echo _("Search"); ?>:",
                    "zeroRecords":    "<?php echo _("No matching records found"); ?>",
                    "paginate": {
                        "first":      "<?php echo _("First"); ?>",
                        "last":       "<?php echo _("Last"); ?>",
                        "next":       "<?php echo _("Next"); ?>",
                        "previous":   "<?php echo _("Previous"); ?>"
                    },
                    "aria": {
                        "sortAscending":  ": <?php echo _("activate to sort column ascending"); ?>",
                        "sortDescending": ": <?php echo _("activate to sort column descending"); ?>"
                    }
                }
            });
            $('#users_table tbody').on('click', 'td', function () {
                var user_id = $(this).parent().attr("id");
                location.href = 'index.php?p=edit_user&id='+user_id;
            });
        });
    })(jQuery);
</script>