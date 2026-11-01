<link rel="stylesheet" href="<?= base_url('assets/css') ?>/jquery.dataTables.min.css">
<script src="<?= base_url('assets/js') ?>/jquery.validate.min.js" type="text/javascript" ></script>
<script src="<?= base_url('assets/js') ?>/jquery.dataTables.min.js" type="text/javascript" ></script>
<div class="row">
    <div class="col-xl-12 mt-0 p-0 dashboard-middle">
        <div class="card">
            <div class="card-body">
                <div class="d-md-flex justify-content-between align-items-center">
                    <div class="col-xl-2">
                        <div class="mini-stat clearfix bg-white">
                            <div class="mini-stat-info text-center">
                                <h6 class="mt-0 header-title"><?php echo __( 'admin.total_tickets') ?> (<span id="total_tickets">0</span>)</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2">
                        <div class="mini-stat clearfix bg-white">
                            <div class="mini-stat-info text-center">
                                <h6 class="mt-0 header-title"><?php echo __( 'admin.total_open_tickets') ?> (<span id="total_open_tickets">0</span>)</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2">
                        <div class="mini-stat clearfix bg-white">
                            <div class="mini-stat-info text-center">
                                <h6 class="mt-0 header-title"><?php echo __( 'admin.total_close_tickets') ?> (<span id="total_close_tickets">0</span>)</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2">
                        <div class="mini-stat clearfix bg-white">
                            <div class="mini-stat-info text-center">
                                <h6 class="mt-0 header-title"><?php echo __( 'admin.total_tickets_subject') ?> (<span id="total_tickets_subject">0</span>)</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
        <div class="row col-md-12 mb-3  adddons-btn">
            <div class="col-md-2"> 
             <a href="<?= base_url('admincontrol/ticketcreate') ?>">
                <button class="btn btn-primary" type="submit"><?=__('admin.add_new_ticket')?></button>
            </a>
            </div>
            <div class="col-md-2">
                <a href="<?= base_url('admincontrol/ticketssubject') ?>">
                    <button class="btn btn-primary" type="submit"><?=__('admin.add_ticket_subject')?></button>
                </a>
            </div>
            <div class="col-md-2">
                <select name="" id="tickets_status" class="form-control">
                    <option value=""><?=__('admin.tickets_user_select_status')?></option>
                    <?php foreach ($status as $key => $value): $isSelected = $tickets_filter_status == $key ? 'selected':''; ?>
                        <option value="<?=$key?>" <?=$isSelected?> ><?=$value?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="" id="ticket_subject" class="form-control">
                    <option value=""><?=__('admin.ticket_subject_selection')?></option>
                    <?php foreach ($subjects as $key => $subj):  ?>
                        <option value="<?=$subj['id']?>" <?=$isSelected?> ><?=$subj['subject']?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-md-2">
                <input autocomplete="off" type="text" name="date" value="" id="date_filter" placeholder="<?= __('admin.date') ?>" class="form-control daterange-picker">
            </div>
        </div>
</div> 
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="table-responsive b-0" data-pattern="priority-columns">
            <table id="tbl_tickets_listing" class="table  table-striped">
                <thead class="bg-blue">
                    <tr>
                        <th><?= __('admin.ticket_id') ?></th>
                        <th><?= __('admin.ticket_date') ?></th>
                        <th><?= __('admin.ticket_client') ?></th>
                        <th><?= __('admin.ticket_subject') ?></th>
                        <th><?= __('admin.ticket_status') ?></th>
                        <th><?= __('admin.ticket_last_update') ?></th>
                        <th><?= __('admin.action') ?></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <script src="<?= base_url('assets/plugins/datatable') ?>/moment.js"></script>
    <script type="text/javascript" src="<?= base_url('assets/plugins/datatable') ?>/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/plugins/datatable') ?>/daterangepicker.css" />
    <script type="text/javascript">
      $(document).ready(function() {
        function ticketslistingDatables() {
            $("#tbl_tickets_listing").dataTable({
                pageLength: 25,
                lengthMenu:[[ 25, 50, -1], [ 25, 50, "All"]],
                processing: true,
                serverSide: true,
                scrollY: false,
                serverMethod: "post",
                // aoColumnDefs: [{ targets: [0], orderable: false }],
                oLanguage: {
                    sProcessing: "Loading...",
                },
                ajax: {
                    url: '<?=base_url()?>' + "tickets/getAlltickets",
                    type: "POST",
                    data: {
                        range: $("#date_filter").val(),
                        status: $('#tickets_status').val(),
                        subject: $('#ticket_subject').val(),
                    },
                    cache: true,
                },
                order: [[5, "DESC"]],
                columns: [
                { data: "ticket_id", targets: 0,  },
                { data: "created_at", targets: 1 },
                { data: "username", targets: 2 },
                { data: "subjectName", targets: 3 },
                { data: "status_ids", targets: 4 },
                { data: "updated_at", targets: 5 },
                { data: "action", targets: 6,orderable:false }
                ], 
                "language": 
                        {
                            "decimal":        "",
                            "emptyTable":     "<?php echo __( 'admin.no_data_available_in_table'); ?>",
                            "info":           "<?php echo __( 'admin.showing'); ?> _START_ to _END_ of _TOTAL_ <?php echo __( 'admin.entries'); ?>",
                            "infoEmpty":      "<?php echo __( 'admin.showing'); ?> 0 to 0 of 0 <?php echo __( 'admin.entries'); ?>",
                            "infoFiltered":   "(filtered from _MAX_ total entries)",
                            "infoPostFix":    "",
                            "thousands":      ",",
                            "lengthMenu":     "<?php echo __( 'admin.show'); ?> _MENU_ <?php echo __( 'admin.entries'); ?>",
                            "loadingRecords": "<?php echo __( 'admin.loading'); ?>",
                            "processing":     "<?php echo __( 'admin.processing'); ?>",
                            "search":         "<?php echo __( 'admin.search'); ?>",
                            "zeroRecords":    "<?php echo __( 'admin.no_records_found'); ?>",
                            "paginate": {
                            "first":      "<?php echo __( 'admin.first'); ?>",
                            "last":       "<?php echo __( 'admin.last_p'); ?>",
                            "next":       "<?php echo __( 'admin.next'); ?>",
                            "previous":   "<?php echo __( 'admin.previous'); ?>"
                            },
                            "aria": {
                                "sortAscending":  ": activate to sort column ascending",
                                "sortDescending": ": activate to sort column descending"
                            }
                        }
            });
        }
        ticketslistingDatables();

        function getStaticData() {
            $.ajax({
                url:'<?= base_url('tickets/getStaticeData') ?>',
                type:'POST',
                dataType:'json',
                async:false,
                success:function(data){
                    if(data.length!=0) {
                        $("#total_tickets").html(data.total)
                        $("#total_open_tickets").html(data.totalopen)
                        $("#total_close_tickets").html(data.totalclose)
                        $("#total_tickets_subject").html(data.totalsubject)
                    }
                },
            })
        }
        getStaticData();
        $("#tickets_status").change(function(event) {
            $("#tbl_tickets_listing").DataTable().destroy()
            ticketslistingDatables();
        }); 
        $("#ticket_subject").change(function(event) {
            $("#tbl_tickets_listing").DataTable().destroy()
            ticketslistingDatables();
        }); 

        $('.daterange-picker').daterangepicker({
            opens: 'left',
            autoUpdateInput: false,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            locale: {
                cancelLabel: 'Clear',
                format: 'DD-MM-YYYY'
            }
        });
        $('.daterange-picker').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY') + ' to ' + picker.endDate.format('DD-MM-YYYY'));
            $("#tbl_tickets_listing").DataTable().destroy()
            ticketslistingDatables();
        });
        $('.daterange-picker').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $("#tbl_tickets_listing").DataTable().destroy()
            ticketslistingDatables();
        });
        $(document).on('click','.btnremove',function(e){
            e.preventDefault();
            var id = $(this).data('id');
            if(confirm('<?=__('admin.are_you_sure')?>')) {
                var ticket_id = $("#ticket_id").val();
                var status = $(this).val();
                $.ajax({
                    url:'<?= base_url('tickets/deleteTicketStatus') ?>',
                    type:'POST',
                    dataType:'json',
                    data:{ticket_id:id},
                    async:false,
                    success:function(data){
                        if(data.status){
                          $("#tbl_tickets_listing").DataTable().destroy()
                          ticketslistingDatables();
                      }
                  }
              })
            }
        });
    });
</script>

</div>
