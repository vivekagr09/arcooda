	<style type="text/css">
		#keyword-dencity th{
			text-align:center!important;
			background-color: #fec!important;
		}
		#keyword-dencity  td{
			text-align:center!important;
		}
	</style>
	<table class="table_fields seo_meta_list" style="width:95%;">
		<tr>
            <th>Title(H1):</th>
            <td style="width: 80%; border: none; background: #d9eff5;"><?= $meta_title ?></td>
        </tr>
		<tr><th></th><td></td></tr>
		<tr><th></th><td></td></tr>
		<tr>
			<?php 
				$newsTitleArray = mb_split('\s+',$meta_title);
				$missingWord = [];
				$missingWordDescription = [];
				$missingWordKeyword = [];
				foreach($newsTitleArray as $newssplit){
					if(!preg_match('/\b' . preg_quote(strtolower($newssplit), '/') . '\b/', strtolower($item["view_url"]))) { 
						$missingWord[]=$newssplit;
					}
					if (!preg_match('/\b' . preg_quote(strtolower($newssplit), '/') . '\b/', strtolower($meta_desc))) { 
						$missingWordDescription[]=$newssplit;
					}
					if (!preg_match('/\b' . preg_quote(strtolower($newssplit), '/') . '\b/', strtolower($meta_keys))) { 
						$missingWordKeyword[]=$newssplit;
					}
				}
			?>
            <th>URL:</th>
            <td style="width: 80%; border: none;margin-left:10px;color:<?= count($missingWord)>0 ?'red':'green'?>;"><?= count($missingWord)>0 ?'Missing':'Good'?> 
            	<?php if(count($missingWord)>0) { echo '('.implode(',', $missingWord) . ')'; } ?></td>
        </tr>
        <tr>
            <th>Description:</th>
            <td style="width: 80%; border: none;margin-left:10px;color:<?= count($missingWordDescription)>0 ?'red':'green'?>;"><?= count($missingWordDescription)>0 ?'Missing':'Good'?> 
            	<?php if(count($missingWordDescription)>0) { echo '('.implode(',', $missingWordDescription) . ')'; } ?></td>
        </tr>
        <tr>
            <th>Keyword:</th>
            <td style="width: 80%; border: none;margin-left:10px;color:<?= count($missingWordKeyword)>0 ?'red':'green'?>;"><?= count($missingWordKeyword)>0 ?'Missing':'Good'?> 
            	<?php if(count($missingWordKeyword)>0) { echo '('.implode(',', $missingWordKeyword) . ')'; } ?></td>
        </tr>
		<tr><th></th><td></td></tr>
		<tr><th></th><td></td></tr>
		<tr><th></th><td></td></tr>
		<tr><th></th><td></td></tr>
		<?php
			include __DIR__ . '/seoanalysis.php';
		?>
	</table>
	<!-- <iframe src="https://www.arcooda.com/webadmin/news/seoanalysis.php" width="100%" height="100%"></iframe> -->
	
<!-- </div> -->