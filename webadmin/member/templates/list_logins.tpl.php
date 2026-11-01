<?php
/**
 * @var mixed[][] $member_logins
 */
?>
<!-- list_logins.tpl -->

<table class="wide">
	<tr>
		<th style="width:25%;">Date</th>
		<th style="width:35%;">IP Address</th>
		<th style="width:10%;">Country</th>
		<th style="width:20%;">Site</th>
		<th style="width:10%;">Browser</th>
	</tr>
<?php
$site_list = array(
    'cnv'  => 'Converters.tv',
    'car'  => 'Car Converters',
    'he'   => 'Highway Entertainment',
    'hg'   => 'Highway Games',
    'xbox' => 'Xbox Arcade Machine',
    'asp'  => 'Arcade Spare Parts',
);
$count = 0;
if (count($member_logins)) {
    foreach ($member_logins as $login) {
        $c_array = getCountryInfo($login['login_ip']);
        $country = $c_array['country'];
        if ('' == $c_array['a2']) {
            $c_array['a2'] = 'zz';
        }

        if ('Unknown' != $country) {
            $country = '<i class="flagicon-' . strtolower(stripslashes($c_array['a2'])) . '" title="' . hecho($country, 1) . '"></i>';
        }

        $login['browser'] = 'N/A';
        $brow             = '';
        $agent            = 'N/A';
        if (!empty($login['user_agent'])) {
            if (false !== ($temp = get_browser($login['user_agent'], true))) {
                $login['browser'] = $temp['browser'];
                $brow             = strtolower(reset(explode(' ', $login['browser'])));
                $agent            = $login['user_agent'];
            }
        }
?>
	<tr style="text-align:center;"<?=$count++ % 2 == 1 ? ' class="alt"' : ''?>>
		<td><?=date(_('jS F, Y'), $login['login_time'])?></td>
		<td><a href="https://www.ip2location.com/<?=$login['login_ip']?>" target="_blank"><?=$login['login_ip']?></a></td>
		<td><?=$country?></td>
		<td><?=$site_list[$login['site']]?></td>
		<td><?=('' != $brow) ? ('<img src="/webadmin/images/browsers/' . strtolower($brow) . '_16.png" alt="' . $brow . '" title="' . $agent . '" />') : ''?></td>
	</tr>
<?php
}
}

?>
</table>

<!-- /list_logins.tpl -->
