<td class="left" style="width: 25%;"><b>Seo Analysis:</b></td>
<td class="right" style="width: 100%;" colspan="2">
<table class="wide" style="width:95%;">
	<tr>
        <td class="left_title">Title(H1):</td>
        <td style="width: 80%; border: none; background: #d9eff5;"><?= htmlspecialchars($item['name'])?></td>
    </tr>
	<tr><td class="left_title"></td><td></td></tr>
	<tr><td class="left_title"></td><td></td></tr>
	<tr>
		<?php 
			$newsTitleArray = mb_split('\s+',$item['name']);
			$missingWord = [];
			$missingWordDescription = [];
			$missingWordKeyword = [];
			foreach($newsTitleArray as $newssplit){
				if(!preg_match('/\b' . preg_quote(strtolower($newssplit), '/') . '\b/', strtolower($url_data))) { 
					$missingWord[]=$newssplit;
				}

				if (!preg_match('/\b' . preg_quote(strtolower($newssplit), '/') . '\b/', strtolower($item['description']))) { 
					$missingWordDescription[]=$newssplit;
				}
				if (!preg_match('/\b' . preg_quote(strtolower($newssplit), '/') . '\b/', strtolower($item['meta_keywords']))) { 
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
</td>
