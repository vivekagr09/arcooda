<html>
	<head>
		<title>Working...</title>
		<script type="text/javascript">
<!--
var updated_contacts = new Array();

<?php
$i = 0;
foreach($contacts as $c): ?>
updated_contacts[<?=$i++?>] = '<?=$c['contact_name']?>'
updated_contacts[<?=$i++?>] = '<?=$c['contact_email']?>'
updated_contacts[<?=$i++?>] = '<?=$c['contact_id']?>';
<?php
endforeach; ?>

opener.update_contacts(updated_contacts);
self.close();
//-->
		</script>
	</head>

	<body>
		<p>Please wait while the contacts section is updated.</p>
	</body>
</html>
