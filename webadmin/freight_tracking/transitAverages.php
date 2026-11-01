<?php
//include_once __DIR__ . '/common.inc.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';

global $DB;

$shipment_list = [];
$all_country_list = array();
if ($query = $DB->query(
    "SELECT `code`, `name` FROM `world`.`country`"
)) {
    while ($row = $DB->fetch_assoc($query)) {
        $all_country_list[$row['code']] = trim($row['name']);
    }
    $DB->free_result($query);
}
$query = $DB->query("
SELECT
    i.ship_country,

    MAX(i.invoice_id) AS last_invoice_id,

    DATEDIFF(CURDATE(), MAX(i.freight_ship_date)) AS last_shipment_days,

    /* 4-week average shipment gap */
    ROUND(
        AVG(
            CASE
                WHEN i.freight_ship_date >= DATE_SUB(CURDATE(), INTERVAL 4 WEEK)
                THEN DATEDIFF(CURDATE(), i.freight_ship_date)
            END
        ), 2
    ) AS avg_shipment_days_last_4_weeks,

    /* Invoice list for last 4 weeks */
    GROUP_CONCAT(
        CASE
            WHEN i.freight_ship_date >= DATE_SUB(CURDATE(), INTERVAL 4 WEEK)
            THEN i.invoice_id
        END
        ORDER BY i.invoice_id ASC
        SEPARATOR ', '
    ) AS invoices_last_4_weeks,

    COUNT(
        CASE
            WHEN i.freight_ship_date >= DATE_SUB(CURDATE(), INTERVAL 4 WEEK)
            THEN 1
        END
    ) AS total_shipments_last_4_weeks,

    /* Total average shipment gap */
    ROUND(AVG(DATEDIFF(CURDATE(), i.freight_ship_date)), 2) AS total_avg_shipment_days,

    /* Invoice list for total average */
    GROUP_CONCAT(
        i.invoice_id
        ORDER BY i.invoice_id ASC
        SEPARATOR ', '
    ) AS all_invoice_ids,

    COUNT(*) AS total_shipments

FROM highwaygroup.invoices i
WHERE
    i.site_id = 39
    AND fright_tracking_service > 0 
    AND proforma_tracknumber != ''
    AND is_freight_checked = 'yes'
    AND freight_ship_date IS NOT NULL
    AND transit_time_report = 0

GROUP BY i.ship_country
ORDER BY i.ship_country ASC;
");

while ($row = $DB->fetch_assoc($query)) {
        $row['country'] = $all_country_list[$row['ship_country']];
        $shipment_list[] = $row;
}
$DB->free_result($query);
?>

<br />

<table width="100%" border="0" cellspaing="2" cellpadding="2"><tbody><tr>
    <td bgcolor="#EEEEEE" width="20%"><div align="center"><b>Transit days Averages</b> <a  style="float: right; padding-right: 20px;" href="./shipments.php?display-all=true">View All</a> 
        <a  style="float: right; padding-right: 20px;" href="./shipments.php">Display Opened Shipment</a><a  style="float: right; padding-right: 20px;" href="./transitAverages.php">Transit Averages</a></div>
         
    </td>
</tr></tbody></table>
    
    <?php 
        $count = 0;

        usort($shipment_list, function ($a, $b) {
            return strcasecmp($a['country'], $b['country']);
        });
    ?>
    <?=formatHTMLTitle('<a href="#" onclick="toggleSection(\'sectionSS\');return false">Opened Shipments</a>', false)?>
    <div id="sectionSS">
        <table class="table_grid">
            <thead>
                <tr>
                    <th> Country</th>
                    <th >Latest Shipment</th>
                    <th >4 Week Average</th>
                    <th >Qty Shipment</th>
                    <th >Total Average</th>
                    <th >Qty Shipment</th>
                </tr>
            </thead>
            <tbody id="screenshots">
                <?php foreach ($shipment_list as $key => $list) { ?>
                    <tr <?=($count % 2 == 1) ? 'class="alt"' : ''?>>
                        <td> <?= $all_country_list[$list['ship_country']]?></td>
                        <td> <a href="/webadmin/invoice/invoice_edit.php?invoice_id=<?=  $list['last_invoice_id'] ?>" target="_blank"><?= $list['last_shipment_days']?> days</a></td>
                         <td><?= $list['avg_shipment_days_last_4_weeks'] > 0 ? ( round($list['avg_shipment_days_last_4_weeks']). ' days'):'No Shipments' ?></td>
                          <td> <?= $list['total_shipments_last_4_weeks'] > 0 ? ('<a href="/webadmin/invoice/invoice_list.php?invoice_list='.$list['invoices_last_4_weeks'].'" target="_blank">'.$list['total_shipments_last_4_weeks'].'</a>'):0 ?></td>
                        <td> <?= $list['total_avg_shipment_days'] > 0 ? ( round($list['total_avg_shipment_days']). ' days'):'No Shipments' ?> </td>
                         <td> <?= $list['total_shipments'] > 0 ? ('<a href="/webadmin/invoice/invoice_list.php?invoice_list='.$list['all_invoice_ids'].'" target="_blank">'.$list['total_shipments'].'</a>'):0 ?></td>
                </tr>
               <?php $count++; } ?>
            </tbody>
        </table>
    </div>
<?php
include_once __DIR__ . '/footer.inc.php';
