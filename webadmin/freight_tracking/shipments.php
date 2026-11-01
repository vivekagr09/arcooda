<?php
//include_once __DIR__ . '/common.inc.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';

global $DB;
moduleLoad('invoice');
moduleLoad('member');
$invoice_frieight_tracking_service = invoiceGetOptionList('freight_tracking');
$invoice_status =  [
    'working'   => 'Open',
    'feedback'    => 'Open',
    'nointerest'     => 'Closed',
    'payment'  => 'Awaiting Payment',
    'approval' => 'Awaiting Shipping',
    'shipping' => 'Awaiting Shipping',
    'shipped'    => 'Shipped',
    'shipped-loan'    => 'Shipped',
    'processed' => 'Processed',
    'cancelled'     => 'Closed',
    'nohelp'  => 'Closed',
    'returned' => 'Open',
    'sent_factory' => 'Open',
    'prepare_factory' => 'Open',
    'prepare_customer' => 'Open',
    'sent_customer' => 'Open',
    'return_completed' => 'Processed',
    'return_cancelled' => 'Closed',
];
$action = "do_add";
$id = 0;
if (!function_exists('freightShipmentGenericsFromPost')) {
    function freightShipmentGenericsFromPost($posted, $table)
    {
         global $DB;
        if (!is_array($posted)) {
            return array();
        }
        $result = array();
        $fields = $DB->get_table_columns($table);
        foreach ($posted as $k1 => $v1) {
                if (array_key_exists($k1,$fields) && !empty($v1)) {
                    if($v1 || $v1==0){
                        $result[$k1] = $v1;
                    }    
                }
        }
        $result['transit_time_report'] =  isset($posted['transit_time_report']) ? 1 : 0;
        return $result;
    }
}
if (!function_exists('freightShipmentGenericToDatabase')) {
    function freightShipmentGenericToDatabase(&$item, $table, $primary_key = 'invoice_id') {
        global $DB, $_GLOBALS;
        $id         = intval($item[$primary_key]);
        $fields     = $DB->get_table_columns($table);
        $old_vals   = $DB->query_first(sprintf(
            "SELECT * FROM %s WHERE `%s` = %d LIMIT 1",
            $table,
            $primary_key,
            $id
        ), MYSQLI_ASSOC);
        if ($old_vals) {
            $set_vals = array();
            foreach ($fields as $k => $v) {
                if (array_key_exists($k, $item) && $item[$k] !=$old_vals[$k]) {
                    if (is_array($item[$k])) {
                        $set_vals[$k] = "`" . $k . "` = " . $DB->escape(implode(",", $item[$k]));
                    } elseif (isset($item[$k])) {
                        $set_vals[$k] = "`" . $k . "` = " . $DB->escape($item[$k]);
                    } else {
                        $set_vals[$k] = "`" . $k . "` = NULL";
                    }
                }
            }
            if (count($set_vals)) {
                $DB->query(sprintf(
                    "UPDATE %s SET %s WHERE `%s` = %d LIMIT 1",
                    $table,
                    implode(', ', $set_vals),
                    $primary_key,
                    $id
                ));
            }
        }
        return $id;
    }
}

if (isset($_GET['action']) && !empty($_GET['action'])) {
    if ($_GET['action'] == 'do_add') {
        $action = "do_add";
    } else if ($_GET['action'] == 'do_edit') {
        $action = "do_edit";

        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = intval($_GET['id']);
        }

    } else {
        die("Incorrect action");
    }
}
if (!empty($_POST)) {
        if(count($posted['freight_shipment'])){
            foreach ($posted['freight_shipment'] as $key => $freight) {
                $mainfreight = freightShipmentGenericsFromPost($freight,'`highwaygroup`.`invoices`');
                freightShipmentGenericToDatabase($mainfreight,'`highwaygroup`.`invoices`');
            }
        }
    $_SESSION['shipment_updated'] = true; 
}
$shipment_list=[];

$whereAND['is_freight_checked'] = " is_freight_checked = 'no' ";
$whereAND['fright_tracking_service'] = " fright_tracking_service > 0 ";
$whereAND['proforma_tracknumber'] = " proforma_tracknumber != '' ";
$order    = (array_key_exists('order', $posted) && 'asc' == $posted['order']) ? 'asc' : 'Desc';
$order_by = (array_key_exists('order_by', $posted)) ? strtolower($posted['order_by']) : 'freight_ship_date';
$orderQuery = 'ORDER BY '.$order_by.' '.$order;

if(isset($_GET['display-all']) && $_GET['display-all'] == true){
    unset($whereAND['is_freight_checked']);
}

$query = $DB->query(sprintf(
    "SELECT bill_name,ship_country,invoice_id,open_date,revision_id,site_id,invoice_offerus,fright_tracking_service,frieght_traking_url,freight_ship_date,freight_received_date,is_freight_checked,proforma_tracknumber,invoice_action,frieght_icon_traking_url  FROM %s %s %s",
    '`highwaygroup`.`invoices`',
     (count($whereAND)) ? sprintf(" WHERE %s", implode(" AND ", $whereAND)) : "",
    $orderQuery
));
while ($row = $DB->fetch_assoc($query)) {
    if (is_null($primary_key)) {
        $shipment_list[] = $row;
    } 
}
$DB->free_result($query);
$sort_url           = $get_vars;
$sort_url['order']  = ('desc' == $order) ? 'asc' : 'desc';
unset($sort_url['order_by']);
$tmp      = $sort_url;
$sort_url = 'shipments.php?' . http_build_query($sort_url);
$sort_url .= '&order_by=';
?>

<br />

<table width="100%" border="0" cellspaing="2" cellpadding="2"><tbody><tr>
    <td bgcolor="#EEEEEE" width="20%"><div align="center"><b>Opened Shipments</b> <a  style="float: right; padding-right: 20px;" href="./shipments.php?display-all=true">View All</a> 
        <a  style="float: right; padding-right: 20px;" href="./shipments.php">Display Opened Shipment</a><a  style="float: right; padding-right: 20px;" href="./transitAverages.php">Transit Averages</a></div>
         
    </td>
</tr></tbody></table>

<form action="" method="post">
    <?php 
    if(isset($_SESSION['shipment_updated']) && $_SESSION['shipment_updated']){
        echo '<div style="color:red;text-align:left;padding:5px;">Shipment Detail updated successfully.</div>';
    }

    include __DIR__ . '/shipment_detail.php';
    ?>
    <div style="text-align:center;">
        <button type="submit" style="padding: 12px 81px;cursor:pointer;" >Submit</button>
        <button type="button" onclick="openTracking()" style="padding: 12px 81px;cursor:pointer;" >Check Tracking</button>
        <span style="float:right;margin-right: 50px;"><i><span style="color:red"><i><small>*</small></span> - Required Field</i></span>
    </div>
</form>

<script type="text/javascript" src="//code.jquery.com/jquery-1.9.0.min.js"></script>
<script type="text/javascript">
    function openTracking(){
        const ids = <?php echo json_encode($trackingIds); ?>;
        // create the 17track URL
          const url = 'https://t.17track.net/en#nums=' + encodeURIComponent(ids.join(','));

          // open in new tab (you can also redirect instead of open)
          window.open(url, '_blank');
    }
</script>
<?php
if(isset($_SESSION['shipment_updated']))
unset($_SESSION['shipment_updated']);
include_once __DIR__ . '/footer.inc.php';
