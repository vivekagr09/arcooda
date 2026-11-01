<?php
if (!function_exists('cm2in')) {
    /**
     * @param string $cm
     *
     * @return string
     */
    function cm2in($cm = '0.001') {
        return bcmul($cm, "0.393701", 3);
    }
}
?>
<style type="text/css">
	.margin-b-l{
		margin-right: 2px;
    	margin-left: 5px;
	}
</style>
<div <?= ($item['type'] == '305' ? '' : 'style="display:none;"')?> class="display-monitor">
	<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionMonitorOtion&quot;);return false">Monitor Options</a>','<a href="/webadmin/monitor-help-text/" target="_blank">Monitor Options Help Text</a>')?>

	<div id="sectionMonitorOtion" >
		<table class="table_fields">
			 <tr class="section-2 section-3 section-4">
		            <th>Monitor Size (<small><i><a href="options.php?action=list&amp;option_name=monitor_size" target="_blank">Add</a></i></small>) :</th>
		            <td><select name="monitor[monitor_size]">
		                <option value="0">Not Selected</option>
		                <?=formatHTMLOptions(productGetOptionList('monitor_size'), $monitorOptions['monitor_size'])?>
		            </select>
		        </td>
		    </tr>
			<tr class="section-2 section-3 section-4" >
	            <th>Panel Type (<small><i><a href="options.php?action=list&amp;option_name=monitor_type" target="_blank">Add</a></i></small>) :</th>
	            <td><select name="item[monitor_type]">
	                <option value="0">Not Selected</option>
	                <?=formatHTMLOptions(productGetOptionList('monitor_type'), $item['monitor_type'])?>
	            </select></td>
		    </tr>
		    <tr class="section-2 section-3 section-4">
		            <th>Monitor Aspect Ratio (<small><i><a href="options.php?action=list&amp;option_name=monitor_ratio" target="_blank">Add</a></i></small>) :</th>
		            <td><select name="item[monitor_ratio]">
		                <option value="0">Not Selected</option>
		                <?=formatHTMLOptions(productGetOptionList('monitor_ratio'), $item['monitor_ratio'])?>
		            </select>
		        </td>
		    </tr>
		   
		   <!--   <tr class="section-2 section-3 section-4">
		            <th>Aspect Ratio (default) (<small><i><a href="options.php?action=list&amp;option_name=monitor_ratio" target="_blank">Add</a></i></small>) :</th>
		            <td>	
		            	<?php 
		            		foreach (productGetOptionList('monitor_ratio') as $key => $value) {
		            			echo '<input type="radio" name="monitor[aspect_ratio_default]" value="'.$key.'" '.($monitorOptions['aspect_ratio_default'] == $key ? "checked":"").'>'.$value;
		            		}
		            	?>
		        	</td>
		    </tr> -->
		     <tr class="section-2 section-3 section-4">
		            <th>Software Aspect Ratio :</th>
		            <td>	
		            	<?php
		            		$selectedValues = [];
	            			if($monitorOptions['aspect_ratio_software']){
	            				$selectedValues = explode(',', $monitorOptions['aspect_ratio_software']);
	            			} 
		            		foreach (productGetOptionList('monitor_ratio') as $key => $value) {

		            			 $checked = in_array($key, $selectedValues) ? 'checked' : '';
		            			echo '<input type="checkbox" name="monitor[aspect_ratio_software][]" value="'.$key.'" '.$checked.'>'.$value;
		            		}
		            	?>
		        	</td>
		    </tr>
		    <tr class="section-2 section-3 section-4">
	            <th>Inputs :</th>
	            <td>
	            	<?php 
	            		$optionInputArray = [
	            			'vga'=>'VGA',
	            			'dvi'=>'DVI',
	            			'hdmi'=>'HDMI',
	            			'displayport'=>'DisplayPort',
	            			'usb'=>'USB (type A)',
	            			'usb_type_c'=>'USB (type C)',
	            		];
	            		foreach ($optionInputArray as $key => $optionVal) {
	            			echo '<div style="padding-bottom: 10px;"><select name="monitor[input_'.$key.']" style="width: 20%;">';
	            				for($i=0;$i<=10 ; $i++){
	            					$selected = $monitorOptions['input_'.$key.''] == $i  ? 'selected' : '';
			                		echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
			                	}
	            			echo '</select>  '.$optionVal.'</div>';
	            		}
	            	?>
	        	</td>
		    </tr>
		    <tr class="section-2 section-3 section-4">
		        <th>Monitor Resolution (<small><i><a href="options.php?action=list&amp;option_name=monitor_resolution" target="_blank">Add</a></i></small>) :</th>
		        <td><select name="item[monitor_resolution]">
		            <option value="0">Not Selected</option>
		            <?=formatHTMLOptions(productGetOptionList('monitor_resolution'), $item['monitor_resolution'])?>
		        </select></td>
		    </tr>
		     <tr class="section-2 section-3 section-4">
		            <th>Brightness (<small><i><a href="options.php?action=list&amp;option_name=monitor_brightness" target="_blank">Add</a></i></small>) :</th>
		            <td><select name="monitor[monitor_brightness]">
		                <option value="0">Not Selected</option>
		                <?=formatHTMLOptions(productGetOptionList('monitor_brightness'), $monitorOptions['monitor_brightness'])?>
		            </select>
		        </td>
		    </tr>
		     <tr class="section-2 section-3 section-4">
		            <th>Contrast Ratio (<small><i><a href="options.php?action=list&amp;option_name=monitor_contrast_ratio" target="_blank">Add</a></i></small>) :</th>
		            <td><select name="monitor[monitor_contrast_ratio]">
		                <option value="0">Not Selected</option>
		                <?=formatHTMLOptions(productGetOptionList('monitor_contrast_ratio'), $monitorOptions['monitor_contrast_ratio'])?>
		            </select>
		        </td>
		    </tr>
		    <tr class="section-2 section-3 section-4">
		            <th>Viewing Angle (<small><i><a href="options.php?action=list&amp;option_name=monitor_viewing_angle" target="_blank">Add</a></i></small>) :</th>
		            <td><select name="monitor[monitor_viewing_angle]">
		                <option value="0">Not Selected</option>
		                <?=formatHTMLOptions(productGetOptionList('monitor_viewing_angle'), $monitorOptions['monitor_viewing_angle'])?>
		            </select>
		        </td>
		    </tr>
		    <tr class="section-2 section-3 section-4">
		            <th>Display Color (<small><i><a href="options.php?action=list&amp;option_name=monitor_display_color" target="_blank">Add</a></i></small>) :</th>
		            <td><select name="monitor[monitor_display_color]">
		                <option value="0">Not Selected</option>
		                <?=formatHTMLOptions(productGetOptionList('monitor_display_color'), $monitorOptions['monitor_display_color'])?>
		            </select>
		        </td>
		    </tr>
		   	<tr class="section-2 section-3 section-4">
		            <th>Refresh Rate (<small><i><a href="options.php?action=list&amp;option_name=monitor_refresh_rate" target="_blank">Add</a></i></small>) :</th>
		            <td><select name="monitor[monitor_refresh_rate]">
		                <option value="0">Not Selected</option>
		                <?=formatHTMLOptions(productGetOptionList('monitor_refresh_rate'), $monitorOptions['monitor_refresh_rate'])?>
		            </select>
		        </td>
		    </tr>
		    <tr class="section-2 section-3 section-4">
		            <th>Display Surface (<small><i><a href="options.php?action=list&amp;option_name=monitor_display_surface" target="_blank">Add</a></i></small>) :</th>
		            <td><select name="monitor[monitor_display_surface]">
		                <option value="0">Not Selected</option>
		                <?=formatHTMLOptions(productGetOptionList('monitor_display_surface'), $monitorOptions['monitor_display_surface'])?>
		            </select>
		        </td>
		    </tr>
		    <tr class="section-2 section-3 section-4">
		            <th>Power (<small><i><a href="options.php?action=list&amp;option_name=monitor_power" target="_blank">Add</a></i></small>) :</th>
		            <td><select name="monitor[monitor_power]">
		                <option value="0">Not Selected</option>
		                <?=formatHTMLOptions(productGetOptionList('monitor_power'), $monitorOptions['monitor_power'])?>
		            </select>
		        </td>
		    </tr>
		    <tr class="section-2 section-3 section-4">
		            <th>Low Resolution Support (<small><i><a href="options.php?action=list&amp;option_name=monitor_low_resolution_support" target="_blank">Add</a></i></small>) :</th>
		            <td>
		            <div class="display-resolution-support">
		            	<?php 
		            		$selectedValues = [];
	            			if($monitorOptions['monitor_low_resolution_support_value']){
	            				$selectedValues = explode(',', $monitorOptions['monitor_low_resolution_support_value']);
	            			} 
		            		foreach (productGetOptionList('monitor_low_resolution_support') as $key => $value) {
		            			 $checked = in_array($key, $selectedValues) ? 'checked' : '';
		            			echo '<input type="checkbox" name="monitor[monitor_low_resolution_support_value][]" value="'.$key.'" '.$checked.'>'.$value;
		            		}
		            	?>
		            </div>
		        </td>
		    </tr>
		    <?php 
			    $low_resolution_options = [
				    'low_resolution_option' => 'Low Resolution Auto Picture',
				    'low_resolution_analysis_option' => 'Low Resolution Analysis',
				    'screen_mirror_option' => 'Screen Mirror Mode',
				];
				foreach ($low_resolution_options as $key => $optionValue) {
			    	echo '<tr class="section-2 section-3 section-4">
			    		 	<th>'.$optionValue.' :</th>
			    		 	<td>
				    		  	<select name="monitor['.$key.']">
				    		  		<option value="">Not Selected</option>';
				    		  		echo formatHTMLOptions(['yes'=>'Yes','no'=>'No'], $monitorOptions[$key]);
				                echo '</select>
			              	</td>
			            </tr>';

		    	}
			?>
			<tr class="section-2 section-3 section-4">
		            <th>Low Resolution rotate:</th>
		            <td>
		            <div class="display-resolution-support">
		            	<?php 
		            		$selectedValues = [];
	            			if($monitorOptions['monitor_low_resolution_rotate']){
	            				$selectedValues = explode(',', $monitorOptions['monitor_low_resolution_rotate']);
	            			} 
		            		foreach (productGetOptionList('monitor_low_resolution_support') as $key => $value) {
		            			 $checked = in_array($key, $selectedValues) ? 'checked' : '';
		            			echo '<input type="checkbox" name="monitor[monitor_low_resolution_rotate][]" value="'.$key.'" '.$checked.'>'.$value;
		            		}
		            	?>
		            </div>
		        </td>
		    </tr>

		    <tr class="section-2 section-3 section-4">
		            <th>PC Monitor Resolution (<small><i><a href="options.php?action=list&amp;option_name=monitor_pc_resolution" target="_blank">Add</a></i></small>) :</th>
		            <td><select name="monitor[monitor_pc_resolution]">
		                <option value="0">Not Selected</option>
		                <?=formatHTMLOptions(productGetOptionList('monitor_pc_resolution'), $monitorOptions['monitor_pc_resolution'])?>
		            </select>
		        </td>
		    </tr>
		    <tr class="section-2 section-3 section-4">
		            <th style="vertical-align: top;">Screen Rotate PC Mode :</th>
		            <td>
			            <select name="monitor[screen_rotate_option]">
			            	<option value="">Not Selected</option>
			                <?=formatHTMLOptions(['yes'=>'Yes','no'=>'No'], $monitorOptions['screen_rotate_option'])?>
			            </select>
			            <div class="screen_rotate_option_class" style="padding-top: 5px;<?= $monitorOptions['screen_rotate_option'] == 'yes' ? '' : 'display: none;'?>">
		            		<?php
		            			$selectedValues = [];
		            			if($monitorOptions['screen_rotate_option_value']){
		            				$selectedValues = explode(',', $monitorOptions['screen_rotate_option_value']);
		            			} 
		            			foreach (productGetOptionList('screen_rotate_option_value') as $key => $value) {
		            			 $checked = in_array($key, $selectedValues) ? 'checked' : '';
		            			echo '<input type="checkbox" name="monitor[screen_rotate_option_value][]" value="'.$key.'" '.$checked.'>'.$value;
		            		}
		            		?>
		            	</div>
		        	</td>
		    </tr>
		   <tr class="section-2 section-3 section-4">
    		 	<th style="padding-top: 12px; vertical-align: top;">Integrated Speakers :</th>
    		 	<td>
	    		  	<select name="item[integrated_speakers]"  style=" margin: 10px 0px;">
	    		  		<option value="">Not Selected</option>';
	    		  		<?=  formatHTMLOptions(['yes'=>'Yes','no'=>'No'], $item['integrated_speakers']); ?>
	                </select>
	                 <div class="display-integrated_speakers" style="<?= $item['integrated_speakers'] == 'yes' ? '' : 'display: none;'?>">
	                 	<select name="item[speakers_support_type]">
		    		  		<option value="">Not Selected</option>';
		    		  		<?=  formatHTMLOptions(['Passive Speaker Support'=>'Passive Speaker Support','Active Speaker Support'=>'Active Speaker Support'], $item['speakers_support_type']); ?>
		                </select>
	            		<textarea  oninput="adjustTextareaHeight(this);" name="item[integrated_speakers_comment]" style="width: 97%; margin: 10px 0px;" value="<?= $item['integrated_speakers_comment'] ?>"><?= $item['integrated_speakers_comment'] ?></textarea>
	            	</div>
              	</td>
            </tr>
		    <tr class="section-2 section-3 section-4">
	            <th style="vertical-align: top;">Audio Out :</th>
	            <td>
		            <select name="monitor[audio_output_jack]">
		            	<option value="">Not Selected</option>
		                <?=formatHTMLOptions(['yes'=>'Yes','no'=>'No'], $monitorOptions['audio_output_jack'])?>
		            </select>
		            <div class="display-audio_output_jack" style="<?= $monitorOptions['audio_output_jack'] == 'yes' ? '' : 'display: none;'?>">
	            		<textarea  oninput="adjustTextareaHeight(this);" name="monitor[audio_output_jack_comment]" style="width: 97%; margin: 10px 0px;" value="<?= $monitorOptions['audio_output_jack_comment'] ?>"><?= $monitorOptions['audio_output_jack_comment'] ?></textarea>
	            	</div>
	        	</td>
		    </tr>
		    <tr class="section-2 section-3 section-4">
		            <th style="vertical-align: top;">Audio in :</th>
		            <td>
		            <select name="monitor[audio_input_jack]">
		            	<option value="">Not Selected</option>
		                <?=formatHTMLOptions(['yes'=>'Yes','no'=>'No'], $monitorOptions['audio_input_jack'])?>
		            </select>
		            <div class="display-audio_input_jack" style="<?= $monitorOptions['audio_input_jack'] == 'yes' ? '' : 'display: none;'?>">
		            		<textarea oninput="adjustTextareaHeight(this);" name="monitor[audio_input_jack_comment]" style="width: 97%; margin: 10px 0px;min-height:35px;" value="<?= $monitorOptions['audio_input_jack_comment'] ?>"><?= $monitorOptions['audio_input_jack_comment'] ?></textarea>
		            </div>
		        </td>
		    </tr>
		    <tr class="section-2 section-3 section-4">
		            <th style="vertical-align: top;">Mounting Brackets :</th>
		            <td>
		            <select name="monitor[mounting_brackets]">
		            	<option value="">Not Selected</option>
		                <?=formatHTMLOptions(['yes'=>'Yes','no'=>'No'], $monitorOptions['mounting_brackets'])?>
		            </select>
		            <div class="display-mounting_brackets" style="<?= $monitorOptions['mounting_brackets'] == 'yes' ? '' : 'display: none;'?>">
		            		<!--  <select  name="monitor[mounting_brackets_value]" style="width: 50%; margin: 10px 0px;">
		            			<?php
		            				for($i=0;$i<=8;$i++){
		            					echo '<option value="'.$i.'" '.($i == $monitorOptions['mounting_brackets_value']?"selected":"").'>'.$i.'</option>';
		            				}
		            			?>

		            		</select> Qty -->
		            		<span>Refer to the <a href="#monitor-bracket-option">Monitor Bracket section </a></span>
		            </div>
		        </td>
		    </tr>
		   <tr class="section-2 section-3 section-4">
		        <th style="vertical-align: top;">VESA Mount (<small><i><a href="options.php?action=list&amp;option_name=monitor_vesa_mount" target="_blank">Add</a></i></small>) :</th>
	            <td>
		            <select name="monitor[monitor_vesa_mount]">
		            	<option value="">Not Selected</option>
		            	<?=formatHTMLOptions(['yes'=>'Yes','no'=>'No'], $monitorOptions['monitor_vesa_mount'])?>
		            </select>
		            <div class="display-monitor_vesa_mount" style="<?= $monitorOptions['monitor_vesa_mount'] == 'yes' ? '' : 'display: none;'?>">
		            	<?php 
		            		$selectedValues = [];
	            			if($monitorOptions['monitor_vesa_mount_value']){
	            				$selectedValues = explode(',', $monitorOptions['monitor_vesa_mount_value']);
	            			} 
		            		foreach (productGetOptionList('monitor_vesa_mount') as $key => $value) {
		            			$checked = in_array($key, $selectedValues) ? 'checked' : '';
		            			echo '<div style="padding-bottom:3px;"><input type="checkbox" name="monitor[monitor_vesa_mount_value][]" value="'.$key.'" '.$checked.'>'.$value.'</div>';
		            		}
		            	?>
		            </div>
	        	</td>
		    </tr>


		    <?php 
		    	$MonotorOptionArray =[
		    		'osd_buton_option' => 'OSD Button Extension Option',
		    		'rgb_board_option' => 'RGB Board Option',
		    		'remote_controll_option' => 'Remote Control Option',
		    		'cable_management_option' => 'Cable Management',
		    		'ddcci_option' => 'DDCCI',
		    		'freesync_Pro_option' => 'FreeSync Pro',
		    		'ultra_hdr_option' => 'Ultra HDR',
		    		'ultra_vivid_option' => 'Ultra Vivid',
		    		'over_drive_option' => 'Over Drive',
		    		'over_scan_option' => 'Over Scan',
		    		'displayport_mst_option' => 'DisplayPort MST',
		    		'screen_clone_option' => 'Screen Clone',
		    		'pinp_option' => 'Picture in Picture',
		    	];
		    	foreach ($MonotorOptionArray as $key => $optionValue) {
		    	echo '<tr class="section-2 section-3 section-4">
		    		 	<th>'.$optionValue.' :</th>
		    		 	<td>
			    		  	<select name="monitor['.$key.']">
			    		  		<option value="">Not Selected</option>';
			    		  		echo formatHTMLOptions(['yes'=>'Yes','no'=>'No'], $monitorOptions[$key]);
			                echo '</select>
		              	</td>
		            </tr>';

		    	}
		    ?>
		     <tr class="section-2 section-3 section-4">
		            <th>Multi Display :</th>
		            <td>
		            <select name="monitor[multi_display]">
		            	<option value="">Not Selected</option>
		                <?=formatHTMLOptions(['no'=>'No','2-4 Inputs'=>'2-4 Inputs'], $monitorOptions['multi_display'])?>
		            </select>
		        </td>
		    </tr>
		    <tr class="section-2 section-3 section-4">
		    	<th style="vertical-align: top;">Screen Viewing Size:</th>
		    	<td>
		    		<table style="width: 98%;display: inline-block;">
		    			<tr class="alt">
				            <th>Unit Size (cm) :</th>
				            <td>
				                Width = 
				                <input type="number" min="0.0" step="any" style="width:5em;" name="monitor[screen_width]" value="<?=$monitorOptions['screen_width'] ?? 0.00 ?>" /> Height =
				                <input type="number" min="0.0" step="any" style="width:5em;" name="monitor[screen_height]" value="<?=$monitorOptions['screen_height'] ?? 0.00 ?>" /> 
				            </td>
				        </tr>
				        <tr class="alt">
				            <th>Unit Size (inches) :</th>
				            <td>
				                Width =
				                <input type="number" min="0.000" step="any" style="width:5em;" name="monitor[screen_width_in]" value="<?=cm2in($monitorOptions['screen_width'] ?? 0.00 )?>" /> Height =
				                <input type="number" min="0.000" step="any" style="width:5em;" name="monitor[screen_height_in]" value="<?=cm2in($monitorOptions['screen_height'] ?? 0.00)?>" />
				            </td>
				        </tr>
		    		</table>
		    	</td>
		    </tr>
		    <tr class="section-2 section-3 section-4">
		    	<th style="vertical-align: top;">Monitor Dimensions  :</th>
		    	<td>
		    		<table style="width: 98%;display: inline-block;">
		    			<tr class="alt">
				            <th>Unit Size (cm) :</th>
				            <td>
				                Width = 
				                <input type="number" min="0.0" step="any" style="width:5em;" disabled value="<?=$item_dimension['width'] ?? 0.00 ?>" /> Depth =
				                <input type="number" min="0.0" step="any" style="width:5em;" disabled value="<?=$item_dimension['depth'] ?? 0.00?>" /> Height =
				                <input type="number" min="0.0" step="any" style="width:5em;" disabled value="<?=$item_dimension['height'] ?? 0.00 ?>" /> 
				            </td>
				        </tr>
				        <tr class="alt">
				            <th>Unit Size (inches) :</th>
				            <td>
				                Width =
				                <input type="number" min="0.000" step="any" style="width:5em;" disabled  value="<?=cm2in($item_dimension['width'] ?? 0.00 )?>" /> Depth =
				                <input type="number" disabled min="0.000" step="any" style="width:5em;"  value="<?=cm2in($item_dimension['depth'] ?? 0.00)?>" /> Height =
				                <input type="number" min="0.000" step="any" disabled style="width:5em;"  value="<?=cm2in($item_dimension['height'] ?? 0.00)?>" />
				            </td>
				        </tr>
		    		</table>
		    	</td>
		    </tr>
		    <tr class="section-2 section-3 section-4">
		    	<th style="vertical-align: top;">Monitor Dimensions with Brackets :</th>
		    	<td>
		    		<table style="width: 98%;display: inline-block;">
		    			<tr class="alt">
				            <th>Unit Size (cm) :</th>
				            <td>
				                Width = 
				                <input type="number" min="0.0" step="any" style="width:5em;" name="monitor[screen_width_brackets]" value="<?=$monitorOptions['screen_width_brackets'] ?? 0.00 ?>" /> Depth =
				                <input type="number" min="0.0" step="any" style="width:5em;" name="monitor[screen_depth_brackets]" value="<?=$monitorOptions['screen_height_brackets'] ?? 0.00 ?>" /> Height =
				                <input type="number" min="0.0" step="any" style="width:5em;" name="monitor[screen_height_brackets]" value="<?=$monitorOptions['screen_height_brackets'] ?? 0.00 ?>" /> 
				            </td>
				        </tr>
				        <tr class="alt">
				            <th>Unit Size (inches) :</th>
				            <td>
				                Width =
				                <input type="number" min="0.000" step="any" style="width:5em;" name="monitor[screen_width_brackets_in]" value="<?=cm2in($monitorOptions['screen_width_brackets'] ?? 0.00 )?>" /> Depth =
				                <input type="number" min="0.000" step="any" style="width:5em;" name="monitor[screen_depth_brackets_in]" value="<?=cm2in($monitorOptions['screen_depth_brackets'] ?? 0.00)?>" /> Height =
				                <input type="number" min="0.000" step="any" style="width:5em;" name="monitor[screen_height_brackets_in]" value="<?=cm2in($monitorOptions['screen_height_brackets'] ?? 0.00)?>" />
				            </td>
				        </tr>
		    		</table>
		    	</td>
		    </tr>
		    <tr class="section-2 section-3 section-4">
		            <th>Best Uses:</th>
		            <td>
		            <textarea name="monitor[best_uses]" style="width: 97%;" oninput="adjustTextareaHeight(this);"><?= $monitorOptions['best_uses'] ?></textarea>
		        </td>
		    </tr>
		     <tr class="section-2 section-3 section-4">
		            <th>Notes:</th>
		            <td>
		            <textarea name="monitor[notes]" style="width: 97%;" oninput="adjustTextareaHeight(this);"><?= $monitorOptions['notes'] ?></textarea>
		        </td>
		    </tr>
		    <tr class="section-2 section-3 section-4">
		            <th>Allow in Comparison Table:</th>
		            <td>
		            <input type ="checkbox" name="monitor[is_comparable]" value="yes" <?= $monitorOptions['is_comparable'] == 'yes' ? 'checked':'' ?>/>
		            <a href="/arcooda-monitor-comparison-table.html" target="_blank"> View Online Table</a>
		        </td>
		    </tr>
		    <br\>
		     <tr class="section-2 section-3 section-4">
		            <th ><div style="color:red;border: 1px solid red;padding:10px;margin-bottom: 1px;margin-top: 2px;">NOTE FOR ONLINE</div></th>
		            <td>
		        	</td>
		    </tr>
		     <br\>
		    <tr class="section-2 section-3 section-4">
		            <th>Monitor Model Number:</th>
		            <td>
		            <input type ="text" name="monitor[model_number]" value="<?= $monitorOptions['model_number'] ?>" />
		        </td>
		    </tr>
		    <tr class="section-2 section-3 section-4">
		            <th>Panel Link:</th>
		            <td>
		            	<input type ="text" name="monitor[panel_link]" id="monitor[panel_link]" value="<?= $monitorOptions['panel_link'] ?>" style="width: 45%;"/><a href="#" style="padding: 5px;" onclick="copyPanelLink(); return false;"><i class="far fa-clone"></i></a>
		            		
		        	</td>
		    </tr>
		    <tr class="section-2 section-3 section-4">
		            <th>AD Board Model:</th>
		            <td>
		            <input type ="text" name="monitor[ad_board_model]" value="<?= $monitorOptions['ad_board_model'] ?>" />
		        	</td>
		    </tr>
		    <tr class="section-2 section-3 section-4">
		            <th>Power Board Model:</th>
		            <td>
		            <input type ="text" name="monitor[power_board_model]" value="<?= $monitorOptions['power_board_model'] ?>" />
		        	</td>
		    </tr>
		    <tr class="section-2 section-3 section-4">
		            <th>Panel PPI:</th>
		            <td>
		            <input type ="text" name="monitor[panel_ppi]" value="<?= $monitorOptions['panel_ppi'] ?>" />
		        	</td>
		    </tr>
		    <tr class="section-2 section-3 section-4">
		            <th>Lag Test(Leo Bodnar):</th>
		            <td>
		            <input type ="text" name="monitor[lagtest_bodnar_top]" value="<?= $monitorOptions['lagtest_bodnar_top'] ?>" /><span class="margin-b-l">Top</span>
		            <input type ="text" name="monitor[lagtest_bodnar_middle]" value="<?= $monitorOptions['lagtest_bodnar_middle'] ?>" /><span class="margin-b-l">Middle</span>
		            <input type ="text" name="monitor[lagtest_bodnar_bottom]" value="<?= $monitorOptions['lagtest_bodnar_bottom'] ?>" /><span class="margin-b-l">Bottom</span>
		            <input type ="date" name="monitor[lagtest_bodnar_date]" value="<?= $monitorOptions['lagtest_bodnar_date'] ?>" /><span class="margin-b-l">Date</span>
		        	</td>
		    </tr>
		     <tr class="section-2 section-3 section-4">
		            <th>Lag Test(Leo Slueth):</th>
		            <td>
		            <input type ="text" name="monitor[lagtest_sluet_top]" value="<?= $monitorOptions['lagtest_sluet_top'] ?>" /><span class="margin-b-l">Top</span>
		            <input type ="text" name="monitor[lagtest_sluet_middle]" value="<?= $monitorOptions['lagtest_sluet_middle'] ?>" /><span class="margin-b-l">Middle</span>
		            <input type ="text" name="monitor[lagtest_sluet_bottom]" value="<?= $monitorOptions['lagtest_sluet_bottom'] ?>" /><span class="margin-b-l">Bottom</span>
		            <input type ="date" name="monitor[lagtest_sluet_date]" value="<?= $monitorOptions['lagtest_sluet_date'] ?>" /><span class="margin-b-l">Date</span>
		        	</td>
		    </tr>
		     <tr class="section-2 section-3 section-4">
		            <th><span style="color:red;border: 1px solid red;padding:10px;margin-bottom: 1px;margin-top: 2px;"><a href="/webadmin/products_new/private-monitor-comparison-table.php" target="_blank"> View Admin Table</a></span> Admin Notes :</th>
		            <td>
		            <textarea style="width: 45%;" name="monitor[admin_notes]" rows="7"> <?= $monitorOptions['admin_notes'] ?> </textarea>
		        	</td>
		    </tr>
		</table>
	</div>
</div>
 <script type="text/javascript">
    $('select[name="monitor[monitor_low_resolution_support]"]').on('change', function() {
        if ($(this).val() == 'yes') {
            $('.display-low_resolution_support').show();
        }else{
            $('.display-low_resolution_support').hide();
        }
    });
     $('select[name="monitor[audio_input_jack]"]').on('change', function() {
        if ($(this).val() == 'yes') {
            $('.display-audio_input_jack').show();
        }else{
            $('.display-audio_input_jack').hide();
        }
    });
     $('select[name="monitor[audio_output_jack]"]').on('change', function() {
        if ($(this).val() == 'yes') {
            $('.display-audio_output_jack').show();
        }else{
            $('.display-audio_output_jack').hide();
        }
    });
     $('select[name="item[integrated_speakers]"]').on('change', function() {
        if ($(this).val() == 'yes') {
            $('.display-integrated_speakers').show();
        }else{
            $('.display-integrated_speakers').hide();
        }
    });
     $('select[name="monitor[monitor_vesa_mount]"]').on('change', function() {
        if ($(this).val() == 'yes') {
            $('.display-monitor_vesa_mount').show();
        }else{
            $('.display-monitor_vesa_mount').hide();
        }
    });
    $('select[name="monitor[screen_rotate_option]"]').on('change', function() {
        if ($(this).val() == 'yes') {
            $('.screen_rotate_option_class').show();
        }else{
            $('.screen_rotate_option_class').hide();
        }
    });
     $('select[name="monitor[mounting_brackets]"]').on('change', function() {
        if ($(this).val() == 'yes') {
            $('.display-mounting_brackets').show();
            $('#monitor-bracket-option').show();
        }else{
            $('.display-mounting_brackets').hide();
            $('#monitor-bracket-option').hide();
        }
    });
    document.addEventListener('DOMContentLoaded', function() {
	  document.querySelectorAll('div[class^="display-resolution-support"]').forEach(group => {
	    const checkboxes = group.querySelectorAll('input[type="checkbox"]');
	    const noCheckbox = Array.from(checkboxes).find(cb => cb.value === 'no');

	    if (noCheckbox) {
	      noCheckbox.addEventListener('change', function() {
	        checkboxes.forEach(cb => {
	          if (cb !== noCheckbox) {
	            cb.disabled = this.checked;
	            if (this.checked) cb.checked = false;
	          }
	        });
	      });
	    }
	  });
	});
    let MonitorDimension = {
	    field: function (elem) {
	        return $(elem).attr("name").split("[")[1].split("]")[0];
	    },
	    selector: function (field) {
	        if (Array.isArray(field)) {
	            var result = [];
	            while (field.length) {
	                result.push(this.selector(field.pop()));
	            }
	            return result.join(",");
	        }
	        return "[name='monitor[" + field + "]']";
	    },
	    get: function (field) {
	        return $(this.selector(field)).val();
	    },
	    set: function (field, value, noBubble) {
	        var $elem = $(this.selector(field));
	        if (value !== $elem.val()) {
	            if (noBubble) $elem.val(value);
	            else $elem.val(value).change();
	        }
	    }
	};   
	$(MonitorDimension.selector(["screen_width", "screen_height"])).change(function () {
	        var field  = MonitorDimension.field(this);
	        var width  = MonitorDimension.get("screen_width");
	        var height = MonitorDimension.get("screen_height");
	        MonitorDimension.set(field + "_in", cm2in($(this).val()), true);
	}); 
	$(MonitorDimension.selector(["screen_width_in", "screen_height_in"])).change(function () {
	         var field = MonitorDimension.field(this);
            MonitorDimension.set(substr(field, 0, -3), in2cm($(this).val()));
	}); 
	$(MonitorDimension.selector(["screen_width_brackets", "screen_depth_brackets","screen_height_brackets"])).change(function () {
	        var field  = MonitorDimension.field(this);
	        var width  = MonitorDimension.get("screen_width");
	        var height = MonitorDimension.get("screen_height");
	        MonitorDimension.set(field + "_in", cm2in($(this).val()), true);
	}); 
	$(MonitorDimension.selector(["screen_width_brackets_in","screen_depth_brackets_in","screen_height_brackets_in"])).change(function () {
	         var field = MonitorDimension.field(this);
            MonitorDimension.set(substr(field, 0, -3), in2cm($(this).val()));
	});   
	function copyPanelLink() {
	    // Prefer span text, fallback to hidden input
	    const hidden = document.getElementById('monitor[panel_link]');
	    
	    const textToCopy = hidden?.value.trim() || '';

	    if (!textToCopy) {
	        alert('Panel Link URL is empty.');
	        return;
	    }

	    navigator.clipboard.writeText(textToCopy)
	    .then(() => {
	        alert('Panel Link copied to clipboard!');
	    })
	    .catch(err => {
	        console.error('Clipboard copy failed:', err);
	        alert('Failed to copy tracking URL.');
	    });
	}  
 </script>