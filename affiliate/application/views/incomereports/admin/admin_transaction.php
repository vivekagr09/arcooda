<div class="container-fluid">
    <div class="row mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <div class="row align-items-center g-3" id="filter-form">
                        <input type="hidden" name="is_admin" value="1">
                        <div class="col-md-2">
                            <label class="form-label"><?= __('admin.user') ?></label>
                            <select name="user_id" class="form-select user-autocomplete"></select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label"><?= __('admin.date') ?></label>
                            <input autocomplete="off" type="text" name="date" value="" class="form-control daterange-picker">
                        </div>
                        <div class="col-md-2 d-grid">
                            <button class="btn btn-light mt-3" onclick="table.ajax.reload();">
                                <i class="fa fa-search"></i> <?= __('admin.search') ?>
                            </button>
                        </div>
                        <div class="col-md-2 d-grid">
                            <button id="exportPdf" class="btn btn-light mt-3">
                                <i class="fa fa-file-pdf-o"></i> <?= __('admin.download_as_pdf') ?>
                            </button>
                        </div>
                        <div class="col-md-2 d-grid">
                            <button id="exportExcel" class="btn btn-light mt-3">
                                <i class="fa fa-file-excel-o"></i> <?= __('admin.download_as_excel') ?>
                            </button>
                        </div>
                        <div class="col-md-2 text-end">
                            <div class="align-middle mt-3">
                                <label class="form-label d-inline"><?= __('admin.users') ?>:
                                    <span class="total-affiliate"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered btn-part affilable-table-hidden" id="table-report">
                        <thead class="table-light text-dark">
                            <tr class="align-middle text-center">
                                <th scope="col">#</th>
                                <th scope="col"></th>
                                <th scope="col"></th>
                                <th scope="col" colspan="2"><?= __('admin.cpc') ?></th>
                                <th scope="col" colspan="3"><?= __('admin.cps') ?></th>
                                <th scope="col"><?= __('admin.cpa') ?></th>
                                <th scope="col" colspan="2"><?= __('admin.total') ?></th>
                            </tr>
                            <tr class="table-secondary text-dark text-center">
                                <th scope="col"><?= __('admin.no') ?></th>
                                <th scope="col"><?= __('admin.full_name') ?></th>
                                <th scope="col"><?= __('admin.username') ?></th>
                                <th scope="col"><?= __('admin.count') ?></th>
                                <th scope="col"><?= __('admin.commission') ?></th>
                                <th scope="col"><?= __('admin.count') ?></th>
                                <th scope="col"><?= __('admin.total') ?></th>
                                <th scope="col"><?= __('admin.commission') ?></th>
                                <th scope="col"><?= __('admin.count_commission_cpa') ?></th>
                                <th scope="col"><?= __('admin.income') ?></th>
                                <th scope="col"><?= __('admin.commission') ?></th>
                            </tr>
                        </thead>
                            <tbody class="tiny-table"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript" src="<?= base_url('assets/plugins/datatable') ?>/jquery.dataTables.min.js"></script>

<link rel="stylesheet" type="text/css" href="<?= base_url('assets/plugins/datatable') ?>/jquery.dataTables.css">

<link rel="stylesheet" type="text/css" href="<?= base_url('assets/plugins/datatable') ?>/dataTables.bootstrap.min.css">

<script src="<?= base_url('assets/plugins/datatable') ?>/moment.js"></script>

<script type="text/javascript" src="<?= base_url('assets/plugins/datatable') ?>/daterangepicker.min.js"></script>

<link rel="stylesheet" type="text/css" href="<?= base_url('assets/plugins/datatable') ?>/daterangepicker.css" />



<script type="text/javascript">
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

            format: 'DD-M-YYYY'

        }
    });

	$('.daterange-picker').on('apply.daterangepicker', function(ev, picker) {

        $(this).val(picker.startDate.format('DD-M-YYYY') + ' - ' + picker.endDate.format('DD-M-YYYY'));
    });

    $('.daterange-picker').on('cancel.daterangepicker', function(ev, picker) {

        $(this).val('');
    });

	var table = $('#table-report').DataTable({

	    dom: 'Bfrtip',

	    ajax: function(data,callback){
	    	$.ajax({

		    	url:"<?= base_url('incomereport/get_data') ?>",

		    	data: {
					destination : 'admin-user-stat',
					page_no : data.start,
					is_admin : 1,
					page_lenght : 20,
					date     : $(".daterange-picker").val(),
					user_id  : $("select[name=user_id]").val(),
			  	},

		    	dataType:'json',

		    	type:'post',

		    	complete:function(){

		    		

		    	},

		    	success:function(json){

		    		$(".total-affiliate").text(json.data.length)

		    		callback(json)

		    	},

		    })

		},
		
		pageLength: 20,

	    buttons: [],

	    bFilter: false, 

        bPaginate : true,

        pagination : true,

        bInfo: false,

        processing: true,
        serverSide: true,

        language: {

            'loadingRecords': '&nbsp;',

            'processing': '<?= __('admin.loading') ?>'+'...'

        },
	});

	$(".user-autocomplete").select2({
		ajax: {

			url: '<?= base_url('incomereport/user_search') ?>',

			dataType: 'json',

			data: function(params) {
				return {
					p: params.term,
					page: params.page
				};
			},
			processResults: function(data, params) {
				var data = $.map(data, function(obj) {

					obj.id = obj.id;

					obj.text = obj.name;

					return obj;

				});
				params.page = params.page || 1;
				return {

					results: data,

					pagination: {

						more: (params.page * 30) < data.total_count

					}

				};

			},

			cache: true

		},

		escapeMarkup: function(markup) {
			return markup;
		},

		allowClear: true,

		minimumInputLength:3,

		placeholder: '',
    });

    // $(".export-excel").on('click',function(){

    // 	$this = $(this);

    // 	$.ajax({

    // 		url:'<?= base_url('incomereport/get_data') ?>?export=excel&filter=is_admin=1&date=' + $(".daterange-picker").val(),

    // 		type:'POST',

    // 		dataType:'json',

    // 		data: {

	//     		is_admin:1,

	//     		date:$(".daterange-picker").val(),

	//     		user_id: $("select[name=user_id]").val(),

	//     	},

    // 		beforeSend:function(){

    // 			$this.btn("loading");

    // 		},

    // 		complete:function(){

    // 			$this.btn("reset");

    // 		},

    // 		success:function(json){

    // 			if (json['download']) {

    // 				window.location.href = json['download'];

    // 			}

    // 		},

    // 	})
    // })
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to export data to PDF
    function openPDF() {
        // Initialize jsPDF
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        doc.text('User Report', 10, 10);

        $.ajax({
            url: '<?= base_url('incomereport/get_data') ?>',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                const tableData = response.data.map(row => {
                    // Remove the image cell
                    return row.map(cell => {
                        if (typeof cell === 'string' && cell.includes('<img')) {
                            return cell.split('<img')[0];
                        }
                        return cell;
                    });
                });

                doc.autoTable({
                    head: [['#', 'User','UserNmae', 'Clicks','Amount', 'Sales', 'Amount', 'Comm', 'Actions/Amount', 'Income', 'T.Comm']],
                    body: tableData,
                    startY: 20
                });
                doc.save('User Report.pdf');
                window.open(doc.output('bloburl'), '_blank');
            }
        });
    }

    // Function to export data to Excel
    function exportToExcel() {
        $.ajax({
            url: '<?= base_url('incomereport/get_data') ?>',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                const tableData = response.data.map(row => {
                    // Remove the image cell
                    return row.map(cell => {
                        if (typeof cell === 'string' && cell.includes('<img')) {
                            return cell.split('<img')[0];
                        }
                        return cell;
                    });
                });

                const ws_name = "User Report";
                const wb = XLSX.utils.book_new();
                const ws = XLSX.utils.aoa_to_sheet([
                    ['#', 'User', 'UserNmae' , 'Clicks','Amount', 'Sales', 'Amount', 'Comm', 'Actions/Amount', 'Income', 'T.Comm'],
                    ...tableData
                ]);
                XLSX.utils.book_append_sheet(wb, ws, ws_name);
                XLSX.writeFile(wb, 'User Report.xlsx');
            }
        });
    }

    document.getElementById('exportPdf').addEventListener('click', openPDF);
    document.getElementById('exportExcel').addEventListener('click', exportToExcel);
});

</script>
