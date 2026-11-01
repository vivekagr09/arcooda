<?php 

require_once APPPATH . '/core/phpspreadsheet/autoload.php';

function exportToExcel($all_transaction) {
    $excelGenerator = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $excelGenerator->getActiveSheet()->setTitle(__('admin.all_transaction'));
    $excelGenerator->getActiveSheet()->setCellValue('A1',__('admin.module'));
    $excelGenerator->getActiveSheet()->setCellValue('B1',__('admin.id'));
    $excelGenerator->getActiveSheet()->setCellValue('C1',__('admin.user'));
    $excelGenerator->getActiveSheet()->setCellValue('D1',__('admin.price'));
    $excelGenerator->getActiveSheet()->setCellValue('E1',__('admin.payment_gateway'));
    $excelGenerator->getActiveSheet()->setCellValue('F1',__('admin.transaction_id'));
    $excelGenerator->getActiveSheet()->setCellValue('G1',__('admin.status'));
    $excelGenerator->getActiveSheet()->setCellValue('H1',__('admin.date'));

    $i = 2;
    foreach($all_transaction as $key => $value){
        switch($value['module']){
            case 'deposit':
                $payment_gateway = __('admin.'.$value['payment_gateway']);
                $transaction_id = $value['payment_detail'];
                $status_text = strip_tags(withdrwal_status($value['status_id']));
                $url = base_url('admincontrol/vendor_deposit_details/'.$value['id']);
                break;
            case 'membership':
                $payment_gateway = $value['payment_gateway'];
                $transaction_id = json_decode($value['payment_detail'])->transaction_id;
                $status_text = strip_tags(membership_withdrwal_status($value['status_id']));
                $url = base_url('membership/membership_purchase_edit/'.$value['id']);
                break;
            case 'store':
                $payment_gateway = $value['payment_gateway'];
                $transaction_id = $value['payment_detail'];
                $status_text = strip_tags(store_withdrwal_status($value['status_id']));
                $url = base_url('admincontrol/vieworder/'.$value['id']);
                break;
        }

        $excelGenerator->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $excelGenerator->getActiveSheet()->setCellValue('A'.$i,__('admin.'.$value['module']));
        $excelGenerator->getActiveSheet()->setCellValue('B'.$i,$value['id']);
        $excelGenerator->getActiveSheet()->setCellValue('C'.$i,$value['username']);
        $excelGenerator->getActiveSheet()->setCellValue('D'.$i,c_format($value['price']));
        $excelGenerator->getActiveSheet()->setCellValue('E'.$i,$payment_gateway);
        $excelGenerator->getActiveSheet()->setCellValue('F'.$i,$transaction_id);
        $excelGenerator->getActiveSheet()->setCellValue('G'.$i,$status_text);
        $excelGenerator->getActiveSheet()->setCellValue('H'.$i,dateFormat($value['datetime'],'d F Y H:i'));
        $i++;
    }

    $excelGenerator->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $excelGenerator->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $excelGenerator->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $excelGenerator->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $excelGenerator->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
    $excelGenerator->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
    $excelGenerator->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
    $excelGenerator->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
    $excelGenerator->getActiveSheet()->getRowDimension('1')->setRowHeight(27);
    $excelGenerator->getActiveSheet()->getRowDimension('2')->setRowHeight(22);

    $excelWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($excelGenerator);
    header('Content-type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="'.time().'.xlsx"');
    $excelWriter->save('php://output');
}

function exportToPdf($author,$all_transaction){
    $table = '<table cellpadding="3" style="font-size:9px;text-align:center;">
                <thead>
                    <tr style="font-weight:bold;">
                        <th style="border-right:1px solid #ccc;border-bottom: 2px solid #ccc;">'. 
                        __('admin.module') .'</th>
                        <th style="border-right:1px solid #ccc;border-bottom: 2px solid #ccc;">'. 
                        __('admin.id') .'</th>
                        <th style="border-right:1px solid #ccc;border-bottom: 2px solid #ccc;">'. 
                        __('admin.user') .'</th>
                        <th style="border-right:1px solid #ccc;border-bottom: 2px solid #ccc;">'. 
                        __('admin.price') .'</th>
                        <th style="border-right:1px solid #ccc;border-bottom: 2px solid #ccc;">'. 
                        __('admin.payment_gateway') .'</th>
                        <th style="border-right:1px solid #ccc;border-bottom: 2px solid #ccc;">'. 
                        __('admin.transaction_id') .'</th>
                        <th style="border-right:1px solid #ccc;border-bottom: 2px solid #ccc;">'. 
                        __('admin.status') .'</th>
                        <th style="border-bottom: 2px solid #ccc;">'. __('admin.date') .'</th>
                    </tr>
                </thead>
                <tbody>';
                foreach($all_transaction as $key => $value){
                    switch($value['module']){
                        case 'deposit':
                            $payment_gateway = __('admin.'.$value['payment_gateway']);
                            $transaction_id = $value['payment_detail'];
                            $status_text = strip_tags(withdrwal_status($value['status_id']));
                            $url = base_url('admincontrol/vendor_deposit_details/'.$value['id']);
                            break;
                        case 'membership':
                            $payment_gateway = $value['payment_gateway'];
                            $transaction_id = json_decode($value['payment_detail'])->transaction_id;
                            $status_text = strip_tags(membership_withdrwal_status($value['status_id']));
                            $url = base_url('membership/membership_purchase_edit/'.$value['id']);
                            break;
                        case 'store':
                            $payment_gateway = $value['payment_gateway'];
                            $transaction_id = $value['payment_detail'];
                            $status_text = strip_tags(store_withdrwal_status($value['status_id']));
                            $url = base_url('admincontrol/vieworder/'.$value['id']);
                            break;
                    }
            $table .=  '<tr nobr="true">
                            <td style="border-right:1px solid #ccc;border-top: 1px solid #ccc;">'. 
                            __('admin.'.$value['module']) .'</td>
                            <td style="border-right:1px solid #ccc;border-top: 1px solid #ccc;">'. 
                            $value['id'] .'</td>
                            <td style="border-right:1px solid #ccc;border-top: 1px solid #ccc;">'. 
                            $value['username'] .'</td>
                            <td style="border-right:1px solid #ccc;border-top: 1px solid #ccc;">'. 
                            convertCurrency($value['price']) .'</td>
                            <td style="border-right:1px solid #ccc;border-top: 1px solid #ccc;">'. 
                            $payment_gateway .'</td>
                            <td style="font-size:7px;border-right:1px solid #ccc;border-top: 1px solid #ccc;">'. 
                            $transaction_id .'</td>
                            <td style="border-right:1px solid #ccc;border-top: 1px solid #ccc;">'. 
                            $status_text .'</td>
                            <td style="font-size:7px;border-top: 1px solid #ccc;">'. 
                            dateFormat($value['datetime'],'d F Y H:i') .'</td>
                        </tr>';
                }
    $table  .= '</tbody>   
            </table>';

    require_once(APPPATH.'third_party/tcpdf/tcpdf.php');

    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor($author);
    $pdf->SetTitle(__('admin.all_transaction'));
    $pdf->SetSubject(__('admin.all_transaction'));
    $pdf->SetKeywords(__('admin.all_transaction'));

    $pdf->SetMargins(5,5,5);
    $pdf->SetAutoPageBreak(TRUE,5);
    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);

    $pdf->AddPage();
    $pdf->writeHTML($table, true, false, true, false, '');
    ob_end_clean();
    $pdf->Output(time().'.pdf', 'D');
}

function convertCurrency($price){
    $ci = & get_instance();
    $default_currency = $ci->db->query("SELECT `code` FROM currency WHERE is_default=1")->row_array();
    $price = $ci->currency->convert((int) $price,$default_currency['code'],$ci->session->userdata('userCurrency'));
    return $ci->session->userdata('userCurrency').' '.$price;
}
