<?php
/***********************************************************************
** Title.........:
** Version.......:
** Author........:	Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
** Filename......:
** Last changed..:
** Notes.........:
***********************************************************************/



function word_wrap ($string, $cols = 70, $prefix='')
{
	$cols = intval($cols);

	if ($cols <= 0)
		$cols = 80;

	$t_lines = explode("\n", $string);
        $outlines = "";

	foreach ($t_lines as $thisline) {
	    if(strlen($thisline) > $cols) {

		$newline = "";
		$t_l_lines = explode(" ", $thisline);

		foreach ($t_l_lines as $thisword) {
		    while((strlen($thisword) + strlen($prefix)) > $cols) {
			$cur_pos = 0;
			$outlines .= $prefix;

			for($num=0; $num < $cols-1; $num++) {
			    $outlines .= $thisword[$num];
			    $cur_pos++;
			}

			$outlines .= "\n";
			$thisword = substr($thisword, $cur_pos, (strlen($thisword)-$cur_pos));
		    }

		    if((strlen($newline) + strlen($thisword)) > $cols) {
			$outlines .= $prefix.$newline."\n";
			$newline = $thisword." ";
		    } else {
			$newline .= $thisword." ";
		    }
		}

		$outlines .= $prefix.$newline."\n";
	    } else {
		$outlines .= $prefix.$thisline."\n";
	    }
	}
	return $outlines;
}


?>