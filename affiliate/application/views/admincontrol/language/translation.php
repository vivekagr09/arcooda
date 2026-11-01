<div class="row">
	<div class="col-12">
		<div class="card m-b-30">
			<div class="card-header translation-header px-3 py-2 d-flex justify-content-between align-items-center flex-wrap">
				<div class="d-flex align-items-center flex-wrap">
					<h4 class="mb-0"><?= __("admin.translation") ?></h4>
					<select id="translation_file" class="form-select mx-3">
						<option value=""><?= __("admin.select_translation") ?></option>
						<option value="admin"><?= __("admin.admin_side") ?></option>
						<option value="user"><?= __("admin.affiliate_side") ?></option>
						<option value="client"><?= __("admin.client_side") ?></option>
						<option value="store"><?= __("admin.store_side") ?></option>
						<option value="template_simple"><?= __("admin.default_template") ?></option>
						<option value="front"><?= __("admin.landing_template") ?></option>
					</select>
					<span class="ms-3">
						<?= __("admin.missing_translation") ?> : <?= $language['count']['missing'] ?> / <?= $language['count']['all'] ?>
					</span>
				</div>
				<div class="d-flex align-items-center flex-wrap">
					<div class="input-group">
						<input type="text" id="myInput" onkeyup="searchFunction()" placeholder="Search" class="form-control lang-search">
						<button onclick="window.location.href='<?= base_url("admincontrol/language") ?>'" class="btn btn-dark" type="button"><?= __("admin.backtohome") ?></button>
					</div>
				</div>
			</div>
			<div id="alert-space" class="px-3 py-2"></div>
			<div class="px-3 py-2 d-flex justify-content-end align-items-center">
				<span class="me-3"></span>
				<button class="btn btn-primary save-translation"><?= __("admin.save_changes") ?></button>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table id="myTable" class="table table-striped">
						<thead>
							<tr>
								<th width="200px"><?= __("admin.key") ?></th>
								<th width="200px"><?= __("admin.default") ?></th>
								<th><?= $language['name'] ?></th>
							</tr>
						</thead>
						<tbody id="translation"></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$("#translation_file").on('change',function(){
		$this = $(this);
		var html = '';
		
		$(".save-translation, .lang-search").hide();
		$('.lang-search').val('');
		location.hash = $this.val();
		if($this.val() != ''){
			$.ajax({
				url:'<?= base_url("admincontrol/get_translation") ?>',
				type:'POST',
				dataType:'json',
				data:{id:$this.val(),'translation_id': <?= $language['id'] ?>},
				beforeSend:function(){$this.prop("disabled",true);},
				complete:function(){$this.prop("disabled",false);},
				success:function(json){
					$.each(json,function(key,data){
						html += '<tr>';
						html += '	<td>'+ key +'</td>';
						html += '	<td>'+ data['text'] +'</td>';
						html += '	<td><input type="text" name="translation['+ key +']" value="'+ data['value'] +'"></td>';
						html += '</tr>';
					})
					$("#translation").html(html);
					$(".save-translation, .lang-search").show();
					checkMissing();
				},
			})
		}
		else
		{
			$("#translation").html('');
		}
	})
	if(location.hash.replace("#","") != ''){
		$("#translation_file").val(location.hash.replace("#","")).trigger("change");
	}
	$(".save-translation").on('click',function(){
		$this = $(this);
		let data = {};
 		$('input[name^="translation"]').each(function(oneTag){
			let name = $(this).attr('name');
			name = name.replace('translation[', '');
			name = name.slice(0, -1);
			data[name] = $(this).val();
		});

		data = JSON.stringify(data);
		var html = '';
		
		if($("#translation_file").val() != ''){
			$.ajax({
				url:'<?= base_url("admincontrol/save_translation") ?>?id=' + $("#translation_file").val() + '&translation_id=<?= $language['id'] ?>',
				type:'POST',
				dataType:'json',
				data:{data:data},
				beforeSend:function(){$this.prop("disabled",true);},
				complete:function(){$this.prop("disabled",false);},
				success:function(json){
					if(json['success']){
						$("#alert-space").html("<div class='alert alert-success my-alert'>"+ json['success'] +"</div>");
						setTimeout(function(){ $(".my-alert").remove(); }, 4000);
					}
				},
			})
		}
	})
	$('#translation').delegate('[name^="translation"]',"change",function(){
		checkMissing();
	});
	
	function checkMissing(){
		$('[name^="translation"]').each(function(){
			var val = $.trim($(this).val());
			if(val == ''){
				$(this).addClass("missing");
			}
			else{
				$(this).removeClass("missing");
			}
		});
		$(".save-translation span").html(' Missing Translation : '+ $("#translation .missing").length +'/' + $("#translation input").length);
	}
	function searchFunction() {
	  var input, filter, table, tr, td, i;
	  input = document.getElementById("myInput");
	  filter = input.value.toUpperCase();
	  table = document.getElementById("myTable");
	  tr = table.getElementsByTagName("tr");
	  for (i = 0; i < tr.length; i++) {
	    td = tr[i].getElementsByTagName("td")[1];
	    if (td) {
	      if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
	        tr[i].style.display = "";
	      } else {
	        tr[i].style.display = "none";
	      }
	    }       
	  }
	}
</script>
