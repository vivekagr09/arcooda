	</div>
</div>
<?php
$popup_window=(isset($_GET['return_message']))?true:false;

if(!$popup_window)
	include_once('webadmin_footer.tpl.php');

if($posted['print']=='true')
{
?>
<script type="text/javascript">
//<![CDATA[
window.print();
//]]>
</script>
<?php
}
?>