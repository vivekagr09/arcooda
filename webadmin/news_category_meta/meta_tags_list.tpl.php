
	<table class="wide" style="width:95%;">
		<tr>
			<td class="left_title" rowspan="12" width="10%">SEO Analysis</td>
            <td class="left_title">Title(H1):</td>
            <td style="width: 80%; border: none; background: #d9eff5;"><?= $meta_title ?></td>
        </tr>
		<tr><td class="left_title"></td><td></td></tr>
		<tr><td class="left_title"></td><td></td></tr>
		<tr>
			<?php 
				$newsTitleArray = mb_split('\s+',$meta_title);
				$missingWord = [];
				$missingWordDescription = [];
				$missingWordKeyword = [];
				foreach($newsTitleArray as $newssplit){
					if (stripos($data1["slug"],$newssplit ) == false) { 
						$missingWord[]=$newssplit;
					}
					if (stripos($data1["meta_desc"],$newssplit ) == false) { 
						$missingWordDescription[]=$newssplit;
					}
					if (!preg_match('/\b' . preg_quote($newssplit, '/') . '\b/', $data1["meta_key"])) { 
						$missingWordKeyword[]=$newssplit;
					}
				}
			?>
            <td class="left_title">URL:</td>
            <td style="width: 80%; border: none;margin-left:10px;color:<?= count($missingWord)>0 ?'red':'green'?>;"><?= count($missingWord)>0 ?'Missing':'Good'?> 
            	<?php if(count($missingWord)>0) { echo '('.implode(',', $missingWord) . ')'; } ?></td>
        </tr>
        <tr>
            <td class="left_title">Description:</td>
            <td style="width: 80%; border: none;margin-left:10px;color:<?= count($missingWordDescription)>0 ?'red':'green'?>;"><?= count($missingWordDescription)>0 ?'Missing':'Good'?> 
            	<?php if(count($missingWordDescription)>0) { echo '('.implode(',', $missingWordDescription) . ')'; } ?></td>
        </tr>
        <tr>
            <td class="left_title">Keyword:</td>
            <td style="width: 80%; border: none;margin-left:10px;color:<?= count($missingWordKeyword)>0 ?'red':'green'?>;"><?= count($missingWordKeyword)>0 ?'Missing':'Good'?> 
            	<?php if(count($missingWordKeyword)>0) { echo '('.implode(',', $missingWordKeyword) . ')'; } ?></td>
        </tr>
		<tr><td class="left_title"></td><td></td></tr>
		<tr><td class="left_title"></td><td></td></tr>
		<tr><td class="left_title"></td><td></td></tr>
		<tr><td class="left_title"></td><td></td></tr>
		<?php

			include __DIR__ . '/seoanalysis.php';
		?>
	</table>
	<!-- <iframe src="https://www.arcooda.com/webadmin/news/seoanalysis.php" width="100%" height="100%"></iframe> -->
	
<!-- </div> -->