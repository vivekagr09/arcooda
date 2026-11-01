<?php
/**
 * @fixme This script needs updating to use phpspreadsheet instead of writeexcel
 */
?><?php

include_once __DIR__ . '/common.inc.php';

moduleLoad('invoice');

$order = $posted['order'];
if ('asc' != $order) {
    $order = 'desc';
}

$page_id  = intval($posted['page_id']);
$order_by = strtolower($posted['order_by']);

//Build a search criteria array
$criteria = array();
foreach (invoiceGetOptionList('all_fields') as $key => $value) {
    if ('' != $posted[$key]) {
        $criteria[$key] = explode('|', $posted[$key]);
    } elseif (array_key_exists($key, $posted)) {
        $criteria[$key] = $posted[$key];
    }
}

unset($criteria['id']);
if ('' != $posted['search_for']) {
    $criteria['any_text'] = $posted['search_for'];
}

if ('' != $posted['subset_list']) {
    $criteria['id'] = explode('|', $posted['subset_list']);
}

//Find and pagenate the invoices
$invoice_list = invoiceSearch($criteria);
$invoice_list = invoiceSort($invoice_list, $order_by);
if ('desc' == $order) {
    $invoice_list = array_reverse($invoice_list);
}

$count_total    = count($invoice_list);
$items_per_page = max(1, $count_total);
$invoice_list   = array_chunk($invoice_list, $items_per_page);
$count_pages    = count($invoice_list);
$page_id        = max(0, min($count_pages - 1, $page_id));
$invoice_list   = $invoice_list[$page_id];
$count_display  = count($invoice_list);

$invoice_labels = $DB->query_first(sprintf(
    "SELECT * FROM `highwaygroup`.`company_info` WHERE `company_id` = %d LIMIT 1",
    $_GLOBALS["site_settings"]["site_id"]
), MYSQLI_ASSOC);
$temp           = explode("\n", str_replace("\r", "", $invoice_labels['company_invoiceheader']));
//if ($invoice['bill_country'] == 'AU' && $invoice['ship_country'] == 'AU') $temp = explode("\n", str_replace("\r", "", $invoice_labels['company_invoiceheader_au']));
$invoice_actions = array('shipped' => 'Goods Shipped', 'approval' => 'Awaiting Payment Approval', 'shipping' => 'Awaiting Shipment', 'payment' => 'Awaiting Payment', 'feedback' => 'Awaiting Customer Feedback', 'nointerest' => 'Customer Not Interested', 'cancelled' => 'Cancelled', 'processed' => 'Processed');

$columns = array(
    'Site'              => 2.29,
    'Invoice'           => 9.00,
    "Open\nDate"        => 8.00,
    'Customer'          => 18.22,
    "Invoice\nValue"    => 13.00,
    "Products\nLocated" => 4.43,
    "Freight\nLocated"  => 4.43,
    "Payment\nMade"     => 4.43,
    'Status'            => 19.71,
    "Days\nOpen"        => 4.57,
    'Comments'          => 43.14,
);

$mode = 'pdf';

$filename = 'dailyreport_' . intval($posted['handler_id']) . '_' . date('ymd');

if ('pdf' == $mode) //PDF Version
{
    moduleLoad('tcpdf');
    class MYPDF extends TCPDF
    {
        public $scale       = 2.12;
        public $head_height = 9.00;
        public $norm_size   = 8;
        public function Header()
        {
            global $columns, $all_staff_list, $posted;

            $xpos = $this->GetX();
            $ypos = $this->GetY();
            $this->SetFontSize($this->norm_size);
            $this->MultiCell(0, 0, "Date: " . date('d/m/Y'), 0, 'R');
            $this->MultiCell(0, 0, "Handler: " . $all_staff_list[$posted['handler_id']], 0, 'R');

            $this->SetXY($xpos, $ypos);
            $this->SetFontSize(24);
            $this->MultiCell(0, 0, "Highway Group Report", 0, 'L');
            $this->SetFontSize($this->norm_size);
            $this->MultiCell(0, 0, "Open Inquiries and Orders", 0, 'L');

            $this->SetFont('', 'B', $this->norm_size);
            foreach ($columns as $key => $width) {
                $xpos = $this->GetX();
                $ypos = $this->GetY();

                $w = $width * $this->scale;
                $h = $this->head_height * $this->scale;

                $this->StartTransform();
                //$this->Rotate(-90,$xpos+$w/2,$ypos+$w/2);
                $this->Rotate(90, $xpos + $h / 2, $ypos + $h / 2);
                $this->MultiCell($h, $w, $key, 0, 'L');
                $this->StopTransform();

                $this->Rect($xpos, $ypos, $width * $this->scale, $this->head_height * $this->scale);
                $this->SetXY($xpos + $width * $this->scale, $ypos);
            }
            $this->SetY($ypos + $this->head_height * $this->scale);
            $this->SetFont('', '', $this->norm_size);
        }
        public function Body($invoice_list)
        {
            global $_GLOBALS;

            global $columns, $invoice_actions;

            $this->Open();
            $this->AddPage();
            $this->SetMargins(12.7, 12.7);

            foreach ($invoice_list as $invoice_id) {
                $invoice = invoiceFromDatabase($invoice_id);
                $format  = 'td';
                if ($alt = !$alt) {
                    $format = 'td_alt';
                }

                if (time() > $invoice['write_time'] + $_GLOBALS['site_options']['WARNING_IDLE_DAYS'] * 60 * 60 * 24) {
                    $format = 'td_idle_timeout';
                }

                if (0 != $invoice['needby_time'] && time() > $invoice['needby_time'] - 604800) {
                    $format = 'td_need_soon';
                }

                if (0 != $invoice['needby_time'] && time() > $invoice['needby_time']) {
                    $format = 'td_need_timeout';
                }

                $xpos = $this->GetX();
                $ypos = $this->GetY();
                $minx = $xpos;
                $maxy = $ypos;

                $this->MultiCell(reset($columns) * $this->scale, 0, $invoice['site_id'], 0, 'C');
                $maxy = max($maxy, $this->GetY());
                $this->SetXY($xpos += current($columns) * $this->scale, $ypos);

                $this->MultiCell(next($columns) * $this->scale, 0, invoiceFormatNumber($invoice['invoice_id'], $invoice['revision_id'], $invoice['site_id']), 0, 'C');
                $maxy = max($maxy, $this->GetY());
                $this->SetXY($xpos += current($columns) * $this->scale, $ypos);

                $this->MultiCell(next($columns) * $this->scale, 0, date('d/m/Y', $invoice['open_time']), 0, 'C');
                $maxy = max($maxy, $this->GetY());
                $this->SetXY($xpos += current($columns) * $this->scale, $ypos);

                $this->MultiCell(next($columns) * $this->scale, 0, $invoice['bill_name'], 0, 'C');
                $maxy = max($maxy, $this->GetY());
                $this->SetXY($xpos += current($columns) * $this->scale, $ypos);

                $this->MultiCell(next($columns) * $this->scale, 0, $invoice['invoice_offer'] . ' ' . $invoice['invoice_currency'], 0, 'C');
                $maxy = max($maxy, $this->GetY());
                $this->SetXY($xpos += current($columns) * $this->scale, $ypos);

                $this->MultiCell(next($columns) * $this->scale, 0, ('y' == $invoice['products_located']) ? ($invoice['products_found'] . '/' . $invoice['products_needed']) : 'No', 0, 'C');
                $maxy = max($maxy, $this->GetY());
                $this->SetXY($xpos += current($columns) * $this->scale, $ypos);

                $this->MultiCell(next($columns) * $this->scale, 0, ('y' == $invoice['ship_located']) ? 'Yes' : 'No', 0, 'C');
                $maxy = max($maxy, $this->GetY());
                $this->SetXY($xpos += current($columns) * $this->scale, $ypos);

                $this->MultiCell(next($columns) * $this->scale, 0, ('y' == $invoice['payments_located']) ? 'Yes' : 'No', 0, 'C');
                $maxy = max($maxy, $this->GetY());
                $this->SetXY($xpos += current($columns) * $this->scale, $ypos);

                $this->MultiCell(next($columns) * $this->scale, 0, $invoice_actions[$invoice['invoice_action']], 0, 'C');
                $maxy = max($maxy, $this->GetY());
                $this->SetXY($xpos += current($columns) * $this->scale, $ypos);

                $this->MultiCell(next($columns) * $this->scale, 0, $invoice['days_open'], 0, 'C');
                $maxy = max($maxy, $this->GetY());
                $this->SetXY($xpos += current($columns) * $this->scale, $ypos);

                $this->MultiCell(next($columns) * $this->scale, 0, $invoice['handler_note'], 0, 'C');
                $maxy = max($maxy, $this->GetY());
                $this->SetXY($xpos += current($columns) * $this->scale, $ypos);

                $xpos = $minx;
                foreach ($columns as $key => $width) {
                    $this->Rect($xpos, $ypos, $width * $this->scale, $maxy - $ypos);
                    $xpos += $width * $this->scale;
                }
                $this->SetXY($minx, $maxy);
            }
        }
        public function Footer()
        {
        }
        public function StartTransform()
        {
            $this->_out('q');
        }
        public function Translate($t_x, $t_y)
        {
            $tm[0] = 1;
            $tm[1] = 0;
            $tm[2] = 0;
            $tm[3] = 1;
            $tm[4] = $t_x * $this->k;
            $tm[5] = -$t_y * $this->k;
            $this->Transform($tm);
        }
        public function Rotate($angle, $x = '', $y = '')
        {
            if ('' === $x) {
                $x = $this->x;
            }

            if ('' === $y) {
                $y = $this->y;
            }

            $y = ($this->h - $y) * $this->k;
            $x *= $this->k;
            $tm[0] = cos(deg2rad($angle));
            $tm[1] = sin(deg2rad($angle));
            $tm[2] = -$tm[1];
            $tm[3] = $tm[0];
            $tm[4] = $x + $tm[1] * $y - $tm[0] * $x;
            $tm[5] = $y - $tm[0] * $y - $tm[1] * $x;
            $this->Transform($tm);
        }
        public function Transform($tm)
        {
            $this->_out(sprintf('%.3f %.3f %.3f %.3f %.3f %.3f cm', $tm[0], $tm[1], $tm[2], $tm[3], $tm[4], $tm[5]));
        }
        public function StopTransform()
        {
            $this->_out('Q');
        }
    }
    $pdf = new MYPDF('L', 'mm', 'A4', true);
    $pdf->SetFont('Helvetica', '', $pdf->norm_size);
    $pdf->aliasNbPages();
    $pdf->Body($invoice_list);

    header('Content-Type: application/pdf; name="' . $filename . '.pdf"');
    header('Content-Disposition: attachment; filename="' . $filename . '.pdf"');
    echo $pdf->Output("example_001.pdf", "S");
}

if ('excel' == $mode) {
    moduleLoad('writeexcel');

    $fname      = tempnam('/tmp', 'simple.xls');
    $workbook   = new writeexcel_workbook($fname);
    $base_style = array('size' => 8, 'valign' => 'top', 'align' => 'center', 'border' => 1, 'text_wrap' => 1, 'fg_color' => 9, 'pattern' => 1);
    $formats    = array(
        'th'                   => $workbook->addformat(array_merge($base_style, array('bold' => 1, 'rotation' => -90, 'valign' => 'bottom'))),
        'td'                   => $workbook->addformat($base_style),
        'td_alt'               => $workbook->addformat($base_style),
        //'td_alt'=>$workbook->addformat(array_merge($base_style,array('fg_color'=>47))),
        'td_idle_timeout'      => $workbook->addformat(array_merge($base_style, array('fg_color' => 13))),
        'td_need_soon'         => $workbook->addformat(array_merge($base_style, array('fg_color' => 14))),
        'td_need_timeout'      => $workbook->addformat(array_merge($base_style, array('fg_color' => 10))),
        'td_spec'              => $workbook->addformat(array_merge($base_style, array('align' => 'left', 'fg_color' => 9))),
        'td_alt_spec'          => $workbook->addformat(array_merge($base_style, array('align' => 'left', 'fg_color' => 9))),
        //'td_alt_spec'=>$workbook->addformat(array_merge($base_style,array('align'=>'left','fg_color'=>47))),
        'td_idle_timeout_spec' => $workbook->addformat(array_merge($base_style, array('align' => 'left', 'fg_color' => 13))),
        'td_need_soon_spec'    => $workbook->addformat(array_merge($base_style, array('align' => 'left', 'fg_color' => 14))),
        'td_need_timeout_spec' => $workbook->addformat(array_merge($base_style, array('align' => 'left', 'fg_color' => 10))),
        'h1'                   => $workbook->addformat(array('size' => 24, 'merge' => 1, 'align' => 'left')),
        'other1'               => $workbook->addformat(array('merge' => 1, 'align' => 'left')),
        'other2'               => $workbook->addformat(array('merge' => 1, 'align' => 'right', 'text_wrap' => 1)),
    );

    $worksheet = &$workbook->addworksheet();
    $worksheet->set_landscape();
    $worksheet->set_paper('A4');
    $worksheet->set_margins(0.5);
    $xpos = 0;
    foreach ($columns as $value => $width) {
        $worksheet->set_column($xpos, $xpos++, $width);
    }

    $worksheet->set_row(2, 57);

    $ypos = 0;
    $worksheet->write_string($ypos, 0, "Highway Group Report", $formats['h1']);
    $worksheet->write_string($ypos++, 10, "Date: " . date('d/m/Y') . "\nHandler: " . $all_staff_list[$posted['handler_id']], $formats['other2']);
    $worksheet->write_string($ypos++, 0, "Open Inquiries and Orders", $formats['other1']);

    $xpos = 0;
    foreach ($columns as $value => $width) {
        $worksheet->write_string($ypos, $xpos++, $value, $formats['th']);
    }

    $worksheet->repeat_rows(0, $ypos++);

    foreach ($invoice_list as $invoice_id) {
        $invoice = invoiceFromDatabase($invoice_id);
        $format  = 'td';
        if ($alt = !$alt) {
            $format = 'td_alt';
        }

        if (time() > $invoice['write_time'] + $_GLOBALS['site_options']['WARNING_IDLE_DAYS'] * 60 * 60 * 24) {
            $format = 'td_idle_timeout';
        }

        if (0 != $invoice['needby_time'] && time() > $invoice['needby_time'] - 604800) {
            $format = 'td_need_soon';
        }

        if (0 != $invoice['needby_time'] && time() > $invoice['needby_time']) {
            $format = 'td_need_timeout';
        }

        $worksheet->write_string($ypos, 0, $invoice['site_id'], $formats[$format]);
        $worksheet->write_string($ypos, 1, invoiceFormatNumber($invoice['invoice_id'], $invoice['revision_id'], $invoice['site_id']), $formats[$format]);
        $worksheet->write_string($ypos, 2, date('d/m/Y', $invoice['open_time']), $formats[$format]);
        $worksheet->write_string($ypos, 3, $invoice['bill_name'], $formats[$format]);
        $worksheet->write_string($ypos, 4, $invoice['invoice_offer'] . ' ' . $invoice['invoice_currency'], $formats[$format]);
        $worksheet->write_string($ypos, 5, ('y' == $invoice['products_located']) ? ($invoice['products_found'] . '/' . $invoice['products_needed']) : 'No', $formats[$format]);
        $worksheet->write_string($ypos, 6, ('y' == $invoice['ship_located']) ? 'Yes' : 'No', $formats[$format]);
        $worksheet->write_string($ypos, 7, ('y' == $invoice['payments_located']) ? 'Yes' : 'No', $formats[$format]);
        $worksheet->write_string($ypos, 8, $invoice_actions[$invoice['invoice_action']], $formats[$format]);
        $worksheet->write_string($ypos, 9, $invoice['days_open'], $formats[$format]);
        $worksheet->write_string($ypos++, 10, $invoice['handler_note'], $formats[$format . '_spec']);
    }

    $workbook->close();

    header('Content-Type: application/x-msexcel; name="' . $filename . '.xls"');
    header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
    readfile($fname);
    unlink($fname);
}

if ('html' == $mode) //HTML Version
{
//    header('Content-Type: text/html');
    header('Content-Type: text/html; name="' . $filename . '.html"');
    header('Content-Disposition: attachment; filename="' . $filename . '.html"');
?><!DOCTYPE html>
<html lang="en">
	<head>
        <meta charset="utf-8" />
		<title><?php hecho($_GLOBALS["site_settings"]["site_name"])?> Report</title>
		<style type="text/css">
/*<![CDATA[*/
table{border-collapse:collapse;width:10.7in}
td,th{border:1px solid #000}
td{white-space:nowrap;text-align:center}
tr.idle_timeout{background-color:#ff0}
tr.need_soon{background-color:#f0f}
tr.need_timeout{background-color:#f00}
/*]]>*/
        </style>
		<!--[if IE]>
		<style type="text/css">
/*<![CDATA[*/
th{writing-mode:tb-rl;text-align:right}
/*]]>*/
		</style>
		<![endif]-->
	</head>
	<body>

<table>
	<tr>
		<td><h1><?php hecho($_GLOBALS["site_settings"]["site_name"])?> Report</h1>Open Inquiries and Orders</td>
		<td style="text-align:right;">Date: <?= date('d/m/Y') ?><br />Handler: <?php hecho($all_staff_list[$posted['handler_id']])?></td>
	</tr>
</table>
<table>
	<thead style="display:table-header-group;"><tr>
		<th>Site</th>
		<th>Invoice</th>
		<th>Open<br />Date</th>
		<th>Customer</th>
		<th>Invoice<br />Value</th>
		<th>Products<br />Located</th>
		<th>Freight<br />Located</th>
		<th>Payment<br />Made</th>
		<th>Status</th>
		<th>Days<br />Open</th>
		<th>Comments</th>
	</tr></thead>
	<tbody>
<?php
foreach ($invoice_list as $invoice_id) {
        $invoice = invoiceFromDatabase($invoice_id);
        $format  = '';
        if ($alt = !$alt) {
            $format = 'alt';
        }

        if (time() > $invoice['write_time'] + $_GLOBALS['site_options']['WARNING_IDLE_DAYS'] * 60 * 60 * 24) {
            $format = 'idle_timeout';
        }

        if (0 != $invoice['needby_time'] && time() > $invoice['needby_time'] - 604800) {
            $format = 'need_soon';
        }

        if (0 != $invoice['needby_time'] && time() > $invoice['needby_time']) {
            $format = 'need_timeout';
        }

?>
		<tr<?= ('' != $format) ? (' class="' . $format . '"') : '' ?>>
			<td><?php hecho($invoice['site_id'])?></td>
			<td><?php hecho(invoiceFormatNumber($invoice['invoice_id'], $invoice['revision_id'], $invoice['site_id']))?></td>
			<td><?php hecho(date('d/m/Y', $invoice['open_time']))?></td>
			<td><?php hecho($invoice['bill_name'])?></td>
			<td><?php hecho($invoice['invoice_offer'] . ' ' . $invoice['invoice_currency'])?></td>
			<td><?php hecho(('y' == $invoice['products_located']) ? ($invoice['products_found'] . '/' . $invoice['products_needed']) : 'No')?></td>
			<td><?php hecho(('y' == $invoice['ship_located']) ? 'Yes' : 'No')?></td>
			<td><?php hecho(('y' == $invoice['payments_located']) ? 'Yes' : 'No')?></td>
			<td><?php hecho($invoice_actions[$invoice['invoice_action']])?></td>
			<td><?php hecho($invoice['days_open'])?></td>
			<td style="white-space:normal;text-align:left;"><?php hecho($invoice['handler_note'])?></td>
		</tr>
<?php
}
?>
	</tbody>
</table>

	</body>
</html>
<?php
}
?>