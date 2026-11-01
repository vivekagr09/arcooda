<script type="text/javascript">
//<![CDATA[
function go() {
	window.open("HE_mail.php?action=send&resume=<?= $resume ?>&time=<?= time() ?>", "_self","toolbar=yes,location=yes,status=yes,menubar=yes,scrollbars=yes,resizable=yes");
}
setTimeout('go()', 3000);
//]]>
</script>
