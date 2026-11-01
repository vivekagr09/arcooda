<script src="https://cdn.ckeditor.com/4.13.1/standard/ckeditor.js"></script>
<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'marker\');return false">marker</a>', '', false)?>
<style type="text/css">
.option-val-input{
	margin-bottom: 5px;
}
.cke_reset{
	margin-bottom: 5px;
}
.hide{
	display:none;
}
</style>
<div id="marker">
	<script type="text/javascript">
		var marker_count="<?= count($marker); ?>";
		function addandreplacerow(type,id,is_exist,valid){
			console.log(is_exist);
			var html= '<td><button type="button" class="'+((is_exist==1)?"addnewrow":"removerow")+'" id="addnew-'+id+'" data-id="'+id+'" val-id="'+valid+'">'+((is_exist==1)?"ADD NEW":"REMOVE")+'</button><br/><br/><button type="button" class="copyrow '+((is_exist==0)?"":"hide")+'" id="addnewcopy-'+id+'" data-id="'+id+'" val-id="'+valid+'">Copy</button></td>\
						 <td>\
					        <select class="option-input" name="marker['+id+'][type]" data-id="'+id+'" val-id="'+valid+'">\
					            <option value="tooltip_plus" '+((type=="tooltip_plus")?"selected":"")+'>Tooltip plus</option>\
					            <option value="link" '+((type=="link")?"selected":"")+'>Link</option>\
					            <option value="tooltip_play" '+((type=="tooltip_play")?"selected":"")+'>Tooltip Play</option>\
					            <option value="infowindow" '+((type=="infowindow")?"selected":"")+'>Info Window</option>\
					        </select>\
					        <div><img src="/img/'+type+'.png" style="padding-top: 12px; padding-bottom: 12px;"><div>\
					    </td>\
					    <td>\
			        	<div>\
			            <table style="border: 0px; width: 100%;">\
			                <tr>\
			                    <td align="left">\
			                    <input class="text-input option-val-input" type="text" placeholder="Name" id="option-val-input-'+id+'" name="marker['+id+'][name]" />\
			                     <textarea class="textarea-input option-val-input" style="width: 98%; resize: vertical; height: 70px; " id="option-desc-input-'+id+'" name="marker['+id+'][discription]" placeholder="Description"></textarea>';
			            if(type=='tooltip_plus'){
			            	html= html+'<input class="text-input option-val-input data-image" type="file" placeholder="image" id="option-val-input-'+id+'" name="marker_image['+id+']" data-id="'+id+'"/>';
			            }
			            if(type=='link'){
			            	html= html+'<input class="text-input option-val-input" type="url" placeholder="Link Url" id="option-val-input-'+id+'" name="marker['+id+'][link_url]" />';
			            }
			            if(type=='tooltip_play' || type=='infowindow'){
			            	html= html+'<input class="text-input option-val-input" type="url" placeholder="Video Link" id="option-val-input-'+id+'" name="marker['+id+'][link_url]" />';
			            }
			        if(type=='tooltip_play'  || type=='tooltip_plus'){
			            	html= html+'Window Width <input class="text-input option-val-input" type="text" placeholder="Window Width" id="option-val-window-'+id+'" name="marker['+id+'][window_width]" required value="350" style="width:88%;float:right;"/>';
			            }
						if(type=='tooltip_play'){
			            	html= html+'<p>Window Height <input class="text-input option-val-input" type="text" placeholder="Window Height" id="option-val-window-'+id+'" name="marker['+id+'][window_height]" value="142" style="width:88%;float:right;"/></p>';
			            }

			        html=html+'</td>';
			        if(type=='tooltip_plus'){
			        	html=html+'<td><img width="100" height="100" id="photo-preview-'+id+'" style="margin: 10px 0; float: left;" ></td>'
			        }												
			        html=html+'</tr></table></div></td>';

			    $('#marker-list-row-'+id).html(html);
			    if(type=='infowindow'){
			    		$(function() {
								content_editor = CKEDITOR.replace('option-desc-input-'+id+'', {
								height:100,
								 filebrowserBrowseUrl: '/ckfinder/ckfinder.html',
							    filebrowserImageBrowseUrl: '/ckfinder/ckfinder.html?type=Images',
							    filebrowserUploadUrl: '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
							    filebrowserImageUploadUrl: '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
								language : 'en',
								baseHref : <?php jecho($_GLOBALS["site_settings"]["home_uri"])?>,
								allowedContent: true,
								format_h2 : {
									element : "h2",
									attributes : {
										style : "background-color:#eee; border:1px solid #ccc; padding:5px 10px; color:#080; font-size:24px; font-weight:bold;"
									}
								},
								contentsCss : "https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css",
								bodyClass : "page_body",
								bodyId : "bd"
							});

					});

			    }

		}
		function copyrowdata(type,id,oldid){
			var html= '<td><button type="button" class="removerow id="addnew-'+id+'" data-id="'+id+'" val-id="'+oldid+'">REMOVE</button><br/><br/><button type="button" class="copyrow" id="addnewcopy-'+id+'" data-id="'+id+'" val-id="'+oldid+'">Copy</button></td>\
						 <td align="left">\
					        <select class="option-input" name="marker['+id+'][type]" data-id="'+id+'" val-id="'+oldid+'">\
					            <option value="tooltip_plus" '+((type=="tooltip_plus")?"selected":"")+'>Tooltip plus</option>\
					            <option value="link" '+((type=="link")?"selected":"")+'>Link</option>\
					            <option value="tooltip_play" '+((type=="tooltip_play")?"selected":"")+'>Tooltip Play</option>\
					            <option value="infowindow" '+((type=="infowindow")?"selected":"")+'>Info Window</option>\
					        </select>\
					        <div><img src="/img/'+type+'.png" style="padding-top: 12px; padding-bottom: 12px;"><div>\
					    </td>\
					    <td>\
			        	<div>\
			            <table style="border: 0px; width: 100%;">\
			                <tr>\
			                    <td align="left">\
			                    <input class="text-input option-val-input" type="text" placeholder="Name" id="option-val-input-'+id+'" name="marker['+id+'][name]" value="'+getValue("marker["+oldid+"][name]")+'"/>\
			                     <textarea class="textarea-input option-val-input" style="width: 98%; resize: vertical; height: 70px; " id="option-desc-input-'+id+'" name="marker['+id+'][discription]" placeholder="Description" >"'+getValue("marker["+oldid+"][discription]")+'"</textarea>';
			            if(type=='tooltip_plus'){
			            	html= html+'<input class="text-input option-val-input data-image" type="file" placeholder="image" id="option-val-input-'+id+'" name="marker_image['+id+']" data-id="'+id+'"/>';

			            }
			            if(type=='link'){
			            	html= html+'<input class="text-input option-val-input" type="url" placeholder="Link Url" id="option-val-input-'+id+'" name="marker['+id+'][link_url]" value="'+getValue("marker["+oldid+"][link_url]")+'"/>';
			            }
			            if(type=='tooltip_play' || type=='infowindow'){
			            	html= html+'<input class="text-input option-val-input" type="url" placeholder="Video Link" id="option-val-input-'+id+'" name="marker['+id+'][link_url]"  value="'+getValue("marker["+oldid+"][link_url]")+'"/>';
			            }
			        if(type=='tooltip_play'  || type=='tooltip_plus'){
			            	html= html+'Window Width <input class="text-input option-val-input" type="text" placeholder="Window Width" id="option-val-window-'+id+'" name="marker['+id+'][window_width]" required  value="'+getValue("marker["+oldid+"][window_width]")+'" style="width:88%;float:right;"/>';
			            }
						if(type=='tooltip_play'){
			            	html= html+'<p>Window Height <input class="text-input option-val-input" type="text" placeholder="Window Height" id="option-val-window-'+id+'" name="marker['+id+'][window_height]"  value="'+getValue("marker["+oldid+"][window_height]")+'" style="width:88%;float:right;"/></p>';
			            }

			        html=html+'</td>';
			        if(type=='tooltip_plus'){
			        	html=html+'<td><img width="100" height="100" id="photo-preview-'+id+'" style="margin: 10px 0; float: left;" src="'+$("#photo-preview-"+oldid).attr("src")+'"></td>';
			        	html= html+'<input class="text-input option-val-input" type="hidden" placeholder="Video Link"  name="marker['+id+'][image]"  value="'+$("#photo-preview-"+oldid).attr("src").match(/.*\/(.*)$/)[1]+'"/>';
			        }												
			        html=html+'</tr></table></div></td>';

			    $('#marker-list-row-'+id).html(html);
			    if(type=='infowindow'){
			    		$(function() {
								content_editor = CKEDITOR.replace('option-desc-input-'+id+'', {
								height:100,
								 filebrowserBrowseUrl: '/ckfinder/ckfinder.html',
							    filebrowserImageBrowseUrl: '/ckfinder/ckfinder.html?type=Images',
							    filebrowserUploadUrl: '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
							    filebrowserImageUploadUrl: '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
								language : 'en',
								baseHref : <?php jecho($_GLOBALS["site_settings"]["home_uri"])?>,
								allowedContent: true,
								format_h2 : {
									element : "h2",
									attributes : {
										style : "background-color:#eee; border:1px solid #ccc; padding:5px 10px; color:#080; font-size:24px; font-weight:bold;"
									}
								},
								contentsCss : "https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css",
								bodyClass : "page_body",
								bodyId : "bd"
							});

					});

			    }

		}
		function addandupdaterow(type,row,valid){
			$('#marker-table').append('<tr id="marker-list-row-'+row+'" class="option-list-row"></tr>');
			addandreplacerow(type,row,1,valid);
		}
		$(document).ready(function(){
			addandupdaterow('tooltip_plus',marker_count,0);
			marker_count++;
		});
		$(document).on("change", '.option-input', function(event){
			var type=$(this).val();
			var row=$(this).attr('data-id');
			var valid=$(this).attr('val-id');
			var mark=marker_count-1;
			if(parseInt(row)==mark){
				addandreplacerow(type,row,1,valid);
			}else{
				addandreplacerow(type,row,0,valid);
			}
			
		})
		$(document).on("click", '.addnewrow', function(event){
			$(this).removeClass('addnewrow');
			var row=$(this).attr('data-id');
			$('#addnewcopy-'+row).removeClass('hide');
			$(this).addClass('removerow');
			$(this).html('REMOVE');
			addandupdaterow('tooltip_plus',marker_count,0);
			marker_count++;
		});
		$(document).on("click", '.copyrow', function(event){
			var currentmarker=marker_count-1;
			$('#addnew-'+currentmarker).removeClass('addnewrow');
			$('#addnew-'+currentmarker).addClass('removerow');
			$('#addnew-'+currentmarker).html('REMOVE');
			$('#addnewcopy-'+currentmarker).removeClass('hide');
			var row=$(this).attr('data-id');
			console.log(getValue('marker['+row+'][type]'));
			copyrowdata(getValue('marker['+row+'][type]'),currentmarker,row);
			addandupdaterow('tooltip_plus',marker_count,0);
			marker_count++;
		});		
		$(document).on("click", '.removerow', function(event){
			var row=$(this).attr('data-id');
			if (confirm('Are you sure you want to delete this marker??')) {
				var theRow = getElement("marker-list-row-" + row + "");
				var dataid = $(this).attr("val-id");
	            if(dataid){
	            setValue("item_marker_deleted_val", getValue("item_marker_deleted_val") + "," + dataid);
	            }
		        if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
			}
		});
		$(document).on("change", '.data-image', function(event){
			var dataid=$(this).attr('data-id');
			 var reader = new FileReader();
		        reader.onload = function (e) {
		            $("#photo-preview-"+dataid).attr("src", e.target.result);
		        };
		        reader.readAsDataURL(this.files[0]);
		});
	</script>
	<?php $count=0 ?>
	<table class="form-table option-list-table" id="marker-table">
		<tbody>
				<?php 
			foreach ($marker as $key => $mark) { ?>
				<tr id="marker-list-row-<?= $count ?>" class="option-list-row">
					<td>
						<input type="hidden" value="<?= $mark['id']?>" name="marker[<?= $count ?>][id]" >
						<button type="button" class="removerow" id="addnew-<?= $count ?>" data-id="<?= $count ?>" val-id="<?= $mark['id']?>">REMOVE</button>
						<br/><br/><button type="button" class="copyrow" id="addnewcopy-<?= $count ?>" data-id="<?= $count ?>" val-id="<?= $mark['id']?>">Copy</button>
					</td>
					<td>
					    <select class="option-input" name="marker[<?= $count ?>][type]" data-id="<?= $count ?>"  val-id="<?= $mark['id']?>">
				            <option value="tooltip_plus" <?=(($mark['type']=="tooltip_plus")?"selected":"") ?> >Tooltip plus</option>
				            <option value="link" <?=(($mark['type']=="link")?"selected":"") ?>>Link</option>
				            <option value="tooltip_play" <?=(($mark['type']=="tooltip_play")?"selected":"") ?>>Tooltip Play</option>
				            <option value="infowindow" <?=(($mark['type']=="infowindow")?"selected":"") ?>>Info Window</option>
				        </select>
				        <div><img src="/img/<?= $mark['type']?>.png" style="padding-top: 12px; padding-bottom: 12px;"></div>
					</td>
					<td>
			        	<div>
				            <table style="border: 0px; width: 100%;">
				            	<tr>
			                    	<td align="left">
			                    		<!-- <select class="text-input option-val-input" name="marker[<?= $count ?>][regression_point]" style="width: 98%;">\
								            <option value="center" <?=(($mark['regression_point']=="center")?"selected":"") ?>>Center</option>\
								            <option value="centerbottom" <?=(($mark['regression_point']=="centerbottom")?"selected":"") ?>>Center Bottom</option>\
								            <option value="leftbottom" <?=(($mark['regression_point']=="leftbottom")?"selected":"") ?>>Left Bottom</option>\
								            <option value="centertop" <?=(($mark['regression_point']=="centertop")?"selected":"") ?>>Center Top</option>\
								        </select> -->
								        <input class="text-input option-val-input" type="text" placeholder="Name" id="option-val-input-<?= $count ?>" name="marker[<?= $count ?>][name]" value="<?= $mark['name']?>"/>
			                     		<textarea class="textarea-input option-val-input" style="width: 98%; resize: vertical; height: 70px; " id="option-desc-input-<?= $count ?>" name="marker[<?= $count ?>][discription]" placeholder="Description"><?= $mark['discription']?></textarea>
			                     		<?php 
			                     			 if($mark['type']=='tooltip_plus'){
										    	echo '<input class="text-input option-val-input data-image" type="file" placeholder="image" id="option-val-input-'.$count.'" name="marker_image['.$count.']" data-id="'.$count.'"/>';
										    }
			                     			if($mark['type']=='link'){
			                     			echo '<input class="text-input option-val-input" type="url" placeholder="Link Url" id="option-val-input-'.$count.'" name="marker['.$count.'][link_url]" value="'.$mark['link_url'].'" />';
			                     			}
			                     			if($mark['type']=='tooltip_play' || $mark['type']=='infowindow'){
			                     			echo '<input class="text-input option-val-input" type="url" placeholder="Video Link" id="option-val-input-'.$count.'" name="marker['.$count.'][link_url]" value="'.$mark['link_url'].'" />';
			                     			}
			                     			if($mark['type']=='tooltip_plus' || $mark['type']=='tooltip_play'){
			                     			echo '<p>Window Width <input class="text-input option-val-input" type="text" placeholder="Window Width" id="option-val-window-'.$count.'" name="marker['.$count.'][window_width]" value="'.$mark['window_width'].'" required style="width:88%;float:right;" /></p>';
											 
			                     			}
											 if($mark['type']=='tooltip_play'){
										    	echo '<p>Window Height <input class="text-input option-val-input" type="text" placeholder="Window Height" id="option-val-window-'.$count.'" name="marker['.$count.'][window_height]" value="'.$mark['window_height'].'" style="width:88%;float:right;" /></p>';
										    }
			                     		?>
			                    	</td>
			                    	<?php  if($mark['type']=='tooltip_plus'){ 
			                    		echo '<td><img width="100" height="100" id="photo-preview-'.$count.'" style="margin: 10px 0; float: left;" src="/webadmin/uploads/product/360view/'.$mark["image"].'"></td>';
			                    	 }?>
			                    	 <?php if($mark['type']=='infowindow'){?>
			                    	 <script type="text/javascript">
						    		    $(function() {
											content_editor = CKEDITOR.replace("option-desc-input-<?= $count ?>", {
											height:100,
											 filebrowserBrowseUrl: '/ckfinder/ckfinder.html',
										    filebrowserImageBrowseUrl: '/ckfinder/ckfinder.html?type=Images',
										    filebrowserUploadUrl: '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
										    filebrowserImageUploadUrl: '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
											language : 'en',
											baseHref : <?php jecho($_GLOBALS["site_settings"]["home_uri"])?>,
											allowedContent: true,
											format_h2 : {
												element : "h2",
												attributes : {
													style : "background-color:#eee; border:1px solid #ccc; padding:5px 10px; color:#080; font-size:24px; font-weight:bold;"
												}
											},
											contentsCss : "https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css",
											bodyClass : "page_body",
											bodyId : "bd"
													});

											});

			                    	 </script>
			                    	<?php } ?>
			                    </tr>
				            </table>
				        </div>
				    </td>
				</tr>
			<?php
				$count++; 
			}
			?>
		</tbody>
	</table>
	<input type="hidden" name="item_marker_deleted_val" value="" />
</div>