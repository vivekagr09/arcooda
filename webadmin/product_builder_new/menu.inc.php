<!-- menu.inc -->
<?php 
		
        $option_total = $DB->query_first(sprintf(
            "SELECT COUNT(*) AS `total` FROM `pb2_options`"
        ), MYSQLI_ASSOC);
        $builder_total = $DB->query_first(sprintf(
            "SELECT COUNT(*) AS `total` FROM `pb2_builders`"
        ), MYSQLI_ASSOC);
         $category_total = $DB->query_first(sprintf(
            "SELECT COUNT(*) AS `total` FROM `pb2_machines_category`"
        ), MYSQLI_ASSOC);
?>
<table cellspacing="0" cellpadding="4" width="100%">
		<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"><b>Product Builder #2</b></td></tr>
		<tr><td>
			<ul class="blocklist">
			<li><a href="/webadmin/product_builder_new/?action=add_builder">Add New Builder</a></li>
			<li><a href="/webadmin/product_builder_new">View Builders(<?= $builder_total['total']?>)</a><br /><br /></li>
			<li><a href="/webadmin/product_builder_new/?action=add_option">Add New Option</a></li>
			<li><a href="/webadmin/product_builder_new/?action=view_option">View Options(<?= $option_total['total']?>)</a></li><br/>
			<li><a href="/webadmin/product_builder_new/?action=view_category">View Category(<?= $category_total['total']?>)</a><br /><br /></li>

			<li><a class="td_options unfinished" href="/webadmin/product_builder@/"><?php hecho(_("Product Builder")) ?></a></li>
		</ul></td></tr>
	</table>

<!-- /menu.inc -->
