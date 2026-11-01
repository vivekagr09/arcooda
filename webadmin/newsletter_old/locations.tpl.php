<!-- locations.tpl -->

<?php
moduleLoad('member'); //memberGroupCount

$clicked_members = array();
$query           = $DB->query("SELECT DISTINCT member_id FROM newsletter_tracking WHERE newsletter_id=" . intval($id));
while ($row = $DB->fetch_assoc($query)) {
    $clicked_members[] = $row['member_id'];
}

$DB->free_result($query);

if (count($clicked_members)) {
    $clicked_countries = memberGroupCount($clicked_members, 'country');

    $graph_data   = array();
    $country_list = getCountryList(false);
    foreach ($clicked_countries as $k => $v) {
        if ('' != $k) {
            $graph_data[] = array($k, $v, $country_list[$k]);
        }
    }

    $top_clicks = array();
    $query      = $DB->query("SELECT COUNT(*) AS rows, request_uri FROM newsletter_tracking WHERE newsletter_id=" . intval($id) . " GROUP BY request_uri ORDER BY rows DESC LIMIT 8");
    while ($row = $DB->fetch_assoc($query)) {
        $top_clicks[$row['request_uri']] = $row['rows'];
    }

    $DB->free_result($query);

    if (count($graph_data)) {
?>
<script type="text/javascript">
//<![CDATA[
google.setOnLoadCallback(function() {
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Country');
	data.addColumn('number', 'Visitors');
	data.addColumn('string', 'Label');
	data.addRows(<?php jecho($graph_data) ?>);
	var geomap = new google.visualization.GeoMap($('#sectionLocations_GeoMap')[0]);
	geomap.draw(data, {width:336, height:280});
});
//]]>
</script>
<?php
}
?>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionLocations\');">Locations</a>')?>

<div id="sectionLocations">
	<table class="wide"><tr>
<?php
if (count($graph_data)) {
?>
		<td style="width:50%;text-align:center;"><div id="sectionLocations_GeoMap"></div></td>
<?php
}
?>
		<td><table class="wide spacetable">
			<tr>
				<th>Rank</th>
				<th>Name</th>
				<th>Clicked</th>
			</tr>
<?php
$rank = 0;
    foreach ($top_clicks as $k => $v) {
        $rank++;
?>
			<tr<?=($rank & 1) ? '' : ' class="alt"'?>>
				<td style="text-align:center;"><?=$rank?></td>
				<td><a href="<?php hecho($k)?>" target="_blank"><?php hecho($k)?></a></td>
				<td style="text-align:center;"><?=$v?></td>
			</tr>
<?php
}
?>
		</table></td>
	</tr></table>
	<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionLocations_hideNote">Locations can be viewed by Clicking "Locations" above<br /></div>

<?php
}
?>

<!-- /locations.tpl -->
