<script type="text/javascript">
//<![CDATA[
function go()
{
	window.open('sender.php?action2=send&resume=<?php echo $resume ?>&time=<?php echo time() ?>', '_self', 'toolbar=yes,location=yes,status=yes,menubar=yes,scrollbars=yes,resizable=yes');
}
setTimeout('go()', 1000);
//]]>
</script>
