<!-- specials.tpl -->

<?php
if (isset($member_specials) && is_array($member_specials)) {
?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionMemberAttachments\');return false">Member Specials</a>', '<a href="#" style="color:#000;" onclick="memberSpecialsAdd();return false">Add New Special</a>')?>

<div id="sectionMemberSpecials">
	<table class="table_grid" id="member_specials_table">

	</table>
	<br /><br />
</div>

<div style="background-color:#fff;text-align:center;padding:8px;display:none;" id="sectionMemberSpecials_hideNote">Member Specials can be viewed by Clicking "Member Specials" above<br /></div>

<?php
}
?>

<!-- /specials.tpl -->