<div>
    <div>
        <div style="width:50%;float:left"><span style="color:red">Invoice <?= $_POST['order_id']?></span> New Message</div>
        <div style="width:50%;float:right">Order Status <span style="color:red"><?= $invoices->status() ?></span></div>
    </div>
    <br/>
    <br/>
    <br/>
    <div>
    <table width=100% cellpadding=2 cellspacing=0 style="font-size:13px;border:1px solid #000;">
        <tr style="width:100%;"><td style="width:30%;border: 1px solid;padding:10px 30px 10px 29px;"><strong>Title</strong></td><td style="width:70%;border: 1px solid;"><?= $_POST['order_id'] . '-' . $_POST['subject'] ?></td></tr>
        <tr style="width:100%;"><td  style="width:30%;border: 1px solid;padding: 30px;"><strong>Message</strong></td><td  style="width:70%;border: 1px solid;"><?= $_POST['message'] ?></td></tr>
    </table>
    </div>
    <div style="margin-top:30px;margin-bottom:50px;">
        <div style="display:flex;justify-content:center">
            <div>Links : <a style="text-decoration: underline;" href=" <?= htmlspecialchars('https://' . $_SERVER['HTTP_HOST'] . '/webadmin/invoice/?action=edit&invoice_id=' . $_POST['id']) ?>" target="_blank">View Invoice</a>, <a style="text-decoration: underline;" href=" <?= htmlspecialchars('https://' . $_SERVER['HTTP_HOST'] . '/webadmin/invoice/?action=edit&invoice_id=' . $_POST['id']) ?>" >Edit Invoice</a></div>
            <div style="padding-left:10px;text-align:center;"><a style="text-decoration: underline;" href=" <?= htmlspecialchars('https://' . $_SERVER['HTTP_HOST'] . '/webadmin/invoice/?action=edit&invoice_id=' . $_POST['id']) ?>#sectionMemberNotes">Reply to Customer</a></div>
        </div>
    </div>
    <div>
        <table width=100% cellpadding=2 cellspacing=0 style="font-size:13px;border:1px solid #000;">
            <tr>
                <td   style="width:50%;border-right:1px solid #000 ;border-bottom:1px solid #000 ;line-height:22px;padding-left: 10px;background-color:#e8e0e0;">
                    <strong>Bill To Address</strong>
                </td>
            
                <td  style="width:50%;line-height:22px;;border-bottom:1px solid #000 ;padding-left: 10px;background-color:#e8e0e0;">
                    <strong>Ship To Address</strong>
                </td>
            </tr>
            <tr>
                <td  style="width:50%;border-right:1px solid #000 ;line-height:22px;padding-left: 10px;">
                    <?= $invoices->billing_address()['bill_company']?> </br>
                    Attn: <?= $invoices->billing_address()['bill_name']?> </br>
                    <?= $invoices->billing_address()['bill_address']?></br>
                    <?= $invoices->billing_address()['bill_city']?>, <?= $invoices->billing_address()['bill_state']?> <?= $invoices->billing_address()['bill_postcode']?></br>
                    <?= $invoices->billing_address()['bill_country']?></br></br>
                    ph: <?= $invoices->billing_address()['bill_phone']?></br>
                    Email: <?= $invoices->billing_address()['bill_email']?>
                </td>
                <td  style="width:50%;border-right:1px solid #000 ;line-height:22px;padding-left: 10px;">
                   <?= $invoices->shipping_address()['ship_company']?> </br>
                    Attn: <?= $invoices->shipping_address()['ship_name']?> </br>
                    <?= $invoices->shipping_address()['ship_address']?></br>
                    <?= $invoices->shipping_address()['ship_city']?>, <?= $invoices->shipping_address()['ship_state']?> <?= $invoices->shipping_address()['ship_postcode']?></br>
                    <?= $invoices->shipping_address()['ship_country']?></br></br>
                    ph: <?= $invoices->shipping_address()['ship_phone']?></br>
                    Email: <?= $invoices->shipping_address()['ship_email']?>
                </td>
            </tr>
        </table>
    </div>
    <div>
        <table width=100% cellspacing=0 style="font-size:13px;border:1px solid #000;margin-top:20px;">
            <tr>
                <td   style="width:10%;border-right:1px solid #000 ;border-bottom:1px solid #000 ;line-height:22px;text-align: center;background-color:#e8e0e0;">
                    <strong>Item ID</strong>
                </td>
            
                <td  style="width:50%;line-height:22px;border-right:1px solid #000 ;border-bottom:1px solid #000 ;text-align: center;background-color:#e8e0e0;">
                    <strong>Product Name</strong>
                </td>
                <td   style="width:10%;border-right:1px solid #000 ;border-bottom:1px solid #000 ;line-height:22px;text-align: center;background-color:#e8e0e0;">
                    <strong>Qty</strong>
                </td>
                <td   style="width:15%;border-right:1px solid #000 ;border-bottom:1px solid #000 ;line-height:22px;text-align: center;background-color:#e8e0e0;">
                    <strong>Price Each</strong>
                </td>
                <td   style="width:15%;border-right:1px solid #000 ;border-bottom:1px solid #000 ;line-height:22px;text-align: center;background-color:#e8e0e0;">
                    <strong>Amount</strong>
                </td>
            </tr>
            <?php foreach ($invoices->invoiceProduct($_POST['id']) as $key => $pro) { 

                ?>
            <tr>
                <td  style="width:10%;border-right:1px solid #000 ;line-height:22px;text-align: center;">
                    <?= $pro['product_id'] ?>
                </td>
                <td  style="width:50%;border-right:1px solid #000 ;line-height:22px;">
                    <?= $pro['product_name'] ?>
                </td>
                <td  style="width:10%;border-right:1px solid #000 ;line-height:22px;text-align: center;">
                    <?= $pro['product_qty'] ?>
                </td>
                <td  style="width:15%;border-right:1px solid #000 ;line-height:22px;text-align: center;">
                     <?= number_format((float)$pro['product_offerus'], 2, '.', '').' '.$invoices->currency() ; ?>
                </td>
                <td  style="width:15%;border-right:1px solid #000 ;line-height:22px;text-align: center;">
                    <?= $pro['product_qty']*$pro['product_offerus'].' '.$invoices->currency() ?>
                </td>
            </tr>
            <?php } ?>
            <tr>
                <td  style="width:10%;border-top:1px solid #000 ;line-height:22px;text-align:right;">
                </td>
                <td  style="width:50%;border-top:1px solid #000 ;line-height:22px;text-align:right;">
                </td>
                <td  style="width:10%;border-right:1px solid #000 ;border-top:1px solid #000 ;line-height:22px;text-align:right;">
                </td>
                <td  style="width:15%;border-right:1px solid #000 ;border-top:1px solid #000;line-height:22px;padding-left: 10px;text-align: center;font-weight:500;">
                    Sub Total :
                </td>
                <td  style="width:15%;border-right:1px solid #000 ;border-top:1px solid #000;line-height:22px;padding-left: 10px;text-align: center;">
                    <?= $invoices->subtotal()?>
                </td>
            </tr>
            <tr>
                <td  style="width:10%;border-top:1px solid #000 ;line-height:22px;text-align:right;">
                </td>
                <td  style="width:50%;border-top:1px solid #000 ;line-height:22px;text-align:right;">
                </td>
                <td  style="width:10%;border-right:1px solid #000 ;border-top:1px solid #000 ;line-height:22px;text-align:right;">
                </td>
                <td  style="width:15%;border-right:1px solid #000 ;border-top:1px solid #000;line-height:22px;padding-left: 10px;text-align: center;font-weight:500;">
                    <strong>Grand Total</strong>
                </td>
                <td  style="width:15%;border-right:1px solid #000 ;border-top:1px solid #000;line-height:22px;padding-left: 10px;text-align: center;">
                    <?= $invoices->total()?>
                </td>
            </tr>
        </table>
    </div>
</div>