<script src="<?= base_url('assets/js') ?>/moment.js" type="text/javascript" ></script>
<script src="<?= base_url('assets/js') ?>/main.min.js"></script>
<script src="<?= base_url('assets/js') ?>/fullcalendar.min.js"></script>
<link rel="stylesheet" href="<?= base_url('assets/css') ?>/fullcalendar.min.css"/>


<style type="text/css" media="screen">
    .removetodolisCalView {
        cursor: pointer;
        z-index: 9999999;
    }
</style>

<div class="container-fluid my-5">
    <div class="card shadow-lg">
        <div class="card-header bg-light border-0">
            <h2 class="font-weight-bold text-primary"><?= __('admin.to_do_list') ?></h2>
            <div class="clearfix"></div>
            <?php flashMsg($this->session->flashdata('flash')); ?>
        </div>
        <div class="card-body">
            <div class="card border-0 shadow-sm">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-add-todo" tabindex="-1" aria-labelledby="modal-add-todoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h4" id="modal-add-todoLabel"><?= __('admin.add_to_do_list') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-12 col-md-4 mb-3">
                        <input type="text" class="form-control" id="todonotesCal" placeholder="Add To-do note">
                    </div>
                    <div class="form-group col-12 col-md-4 mb-3">
                        <input type="text" class="form-control" id="tododateCal" placeholder=" To-do date">
                    </div>
                    <div class="form-group col-12 col-md-4 mb-3">
                        <button type="button" class="btn btn-primary w-100" id="btnAddCalnote"><?= __('admin.add') ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
	$(document).ready(function() {
		var calendar;
		function initCalender() {
			calendar = $('#calendar').fullCalendar({
				themeSystem: 'bootstrap4',
				defaultView: 'month',
				editable: false,
				disableDragging:true,
				header: {
					left: 'today',
					center: 'title ',
					right: ' prev,next,month'
				},
				buttonText : {
					prev : '<?= __('admin.prev') ?>',
					next : '<?= __('admin.next') ?>',
					month :'<?= __('admin.month') ?>',
					today :'<?= __('admin.today') ?>',
				},
				monthNames: 
						[
						'<?= __('admin.jan') ?>', 
						'<?= __('admin.feb') ?>', 
						'<?= __('admin.mar') ?>', 
						'<?= __('admin.apr') ?>', 
						'<?= __('admin.may') ?>', 
						'<?= __('admin.jun') ?>', 
						'<?= __('admin.jul') ?>', 
						'<?= __('admin.aug') ?>', 
						'<?= __('admin.sep') ?>', 
						'<?= __('admin.oct') ?>', 
						'<?= __('admin.nov') ?>', 
						'<?= __('admin.dec') ?>'
						], 
    			dayNamesShort: [
    					'<?= __('admin.sun') ?>',
					    '<?= __('admin.mon') ?>',
					    '<?= __('admin.tue') ?>',
					    '<?= __('admin.wed') ?>',
					    '<?= __('admin.thu') ?>',
					    '<?= __('admin.fri') ?>',
					    '<?= __('admin.sat') ?>'
    				],
				events:'<?=base_url()?>'+"todo/getodolist?isCalView=1",
				eventRender: function(event, element) {
					if(event.is_done=="1"){
						element.find('.fc-title').addClass('isTodaCompleted').attr('title','Click to view/update');
					}
					var isTodoDone = event.is_done=="1" ? 'checked':'';
					element.find(".fc-content").prepend("<div class='float-left'><input type='checkbox' data-id='"+event.id+"' class='completedTodoCalView mr-3' "+isTodoDone+"></div>");
					element.find(".fc-content").prepend("<div class='float-right'><a class='removetodolisCalView' data-id='"+event.id+"' ><i class=' fa fa-trash'></i></a></div>")
				},
				dayClick: function(events) {

					var check = moment(events._d).format('YYYY-MM-DD');
					var today = moment(new Date()).format('YYYY-MM-DD');
					if(check < today)
					{
						return showPrintMessage("<?= __('admin.you_cant_select_past_dates')  ?>", 'error');
					}
					;
					$("#tododateCal").val(check);
					$("#todonotesCal").val('');
					$("#todoListItemid").val(0);
					$('#btnAddCalnote').text('Add');
					$('#modal-add-todo').modal('show');
				},
				eventClick: function(event, jsEvent, view) {
					var cu = jsEvent.target;
					if($(cu).hasClass('fa-trash')) return false;

					if($(cu).hasClass('completedTodoCalView')) {
						console.log(true)
						var id = $(cu).data('id');
						var is_completed = 0;
						if ($(cu).attr('checked')) {
							$(cu).removeAttr('checked');
							is_completed=0;
						} else {
							$(cu).attr('checked', 'checked');
							is_completed=1;
							$(cu).parent().addClass('isTodaCompleted')
						}
						var id = $(cu).data('id');
						var $that = $(cu);
						$.ajax({
							url:'<?= base_url('todo/actiontodolist') ?>',
							type:'POST',
							dataType:'json',
							data:{id:id,action:2,is_completed:is_completed},
							async:false,
							success:function(data){
								if(data.status) {
									gettodoList();
									calendar.fullCalendar('destroy');
									initCalender();
									showPrintMessage(data.message, 'success');
								}
								else {
                        			showPrintMessage(data.message, 'error');
                    			}
							},
						})
						return true;	
					}
					$('#todonotesCal').val(event.notes)
					$("#todoListItemid").val(event.id);
					$("#tododateCal").val( moment(event.start).format('YYYY-MM-DD'));
					$('#modal-add-todo').modal('show');
					$('#btnAddCalnote').text('Update');
				},
			});
		}
		initCalender();
		
		$(document).on('click', '.removetodolisCalView', function(e) {
			if(confirm('<?= __('admin.are_you_sure')?>')){
				$("#modal-add-todo").modal('hide');
				var id = $(this).data('id');
				var $that = $(this);
				$.ajax({
					url:'<?= base_url('todo/actiontodolist') ?>',
					type:'POST',
					dataType:'json',
					data:{id:id,action:1},
					async:false,
					success:function(data){
						if(data.status) {
							gettodoList();
							calendar.fullCalendar('destroy');
							initCalender();
							showPrintMessage(data.message, 'success');
						}
						else{
							showPrintMessage(data.message, 'error');
						}
					},
				});
			}
		});
		$("#btnAddCalnote").click(function(){
			var todo_date = $("#tododateCal").val();
			var todonotesCal = $("#todonotesCal").val();
			var id = $("#todoListItemid").val();

			if (todonotesCal && todo_date) {
				$.ajax({
					url:'<?= base_url('todo/addtodolist') ?>',
					type:'POST',
					dataType:'json',
					async:false,
					data: { note :todonotesCal,id:id,todo_date:todo_date},
					success:function(data){
						if(data.status){
							gettodoList();
							$("#tododateCal,#todonotesCal").val('');
							$("#todoListItemid").val(0);
							$('#btnAddCalnote').text('Add');
							$('#modal-add-todo').modal('hide');
							calendar.fullCalendar('destroy');
							initCalender();
							showPrintMessage(data.message, 'success');
						}
						else {
                        	showPrintMessage(data.message, 'error');
                    	}
					},
				});
			}
		})
	});
</script>