<?php
?><HTML>
<HEAD>
<TITLE><?=$title . ' - ' . date('d M Y');?></TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
<script>
<!--
function printpage() {
        if (window.print)
        {
                window.print();
        }
        else
        {
                alert('To print this page, select Print Frame from the file menu.');
        }
}

function doPrint()
{
        if (confirm("Click OK to print this document"))
        {
                printpage();
        }

}
// -->
</script>
<link rel="stylesheet" href="print_site.css" type="text/css">

</HEAD>

<BODY BGCOLOR="#FFFFFF" TEXT="#000000" link="#000000" vlink="#000000" alink="#000000" onLoad="doPrint();return false">

	<div align="center">
	<h1><?=strtoupper($title);?></h1>
		<?=date('d F Y');?><br><br>
	</div>
