<!-- developer_tools.tpl -->

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionDeveloperTools\');">Developer Tools</a>') ?>
<div id="sectionDeveloperTools">

<script type="text/javascript">
function developerPreviewInvoice(theFrm)
{
	var oldact=theFrm.action;
	var oldtrg=theFrm.target;
	theFrm.action="invoice_preview.php";
	theFrm.target="_blank";
	theFrm.submit();
	theFrm.action=oldact;
	theFrm.target=oldtrg;
}
function developerPreviewPurchase(theFrm)
{
	var oldact=theFrm.action;
	var oldtrg=theFrm.target;
	theFrm.action="invoice_purchase_preview.php";
	theFrm.target="_blank";
	theFrm.submit();
	theFrm.action=oldact;
	theFrm.target=oldtrg;
}
</script>

<table class="wide">
	<tr>
		<td class="left_title">Preview Buttons</td>
		<td>
			<input type="button" value="Preview Invoice" onclick="developerPreviewInvoice(this.form)" />
			<input type="button" value="Preview Purchase Order" onclick="developerPreviewPurchase(this.form)" />
			<input type="button" value="Preview Post" onclick="testPost(this.form)" />
		</td>
	</tr>
</table>
<br /><br />
</div>
<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionDeveloperTools_hideNote">
Developer Tools can be viewed by Clicking "Developer Tools" above
<br />
</div>

<!-- /developer_tools.tpl -->
