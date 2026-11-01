<?php
if (!function_exists('bpayGenerateNumber')) {
    function bpayGenerateNumber($member_id, $quote_id = 0)
    {
        $crn = $quote_id . substr('00000' . $member_id, -5);
        $crn = substr('0000000000' . $crn, -10);
        $crn .= bpayCalculateChecksum($crn);
        return $crn;
    }

}
if (!function_exists('bpayReverseMemberID')) {
    function bpayReverseMemberID($crn)
    {
        return substr($crn, -7, -2);
    }

}
if (!function_exists('bpayReverseInvoiceID')) {
    function bpayReverseInvoiceID($crn)
    {
        return substr($crn, -12, -7);
    }

}
if (!function_exists('bpayValidateNumber')) {
    function bpayValidateNumber($crn)
    {
        if (strlen($crn) < 3) {
            return false;
        }

        $number = substr($crn, 0, -2);
        $check  = substr($crn, -2);

        if (bpayCalculateChecksum($number) == $check) {
            return true;
        }

        return false;
    }

}
if (!function_exists('bpayCalculateChecksum')) {
    function bpayCalculateChecksum($crn)
    {
        $total = 0;
        for ($i = 1; $i <= strlen($crn); $i++) {
            $total += substr($crn, 0 - $i, 1) * $i;
        }

        return substr('00' . (97 - ($total % 97)), -2);
    }

}
if (!function_exists('bpayDumpBillLogo')) {
    function bpayDumpBillLogo($bpay_biller, $bpay_reference, $width = '', $centered = false)
    {
        $bpay_reference = str_replace("\r\n", " ", chunk_split($bpay_reference, 5));
        $wdt            = ('' != $width) ? $width : '1px';
?>
<table style="width:<?=$wdt?>;font-family:helvetica;"<?=($centered) ? ' align="center"' : ''?>>
	<tr>
		<td style="text-align:center;vertical-align:middle;padding:4px 4px 4px 4px;width:1px;height:1px;border:1px solid black;"><img src="https://www.carconverters.com.au/bpay_logo.png" /></td>
		<td style="text-align:left;vertical-align:middle;padding:12px 12px 12px 12px;width:auto;height:1px;border:1px solid black;font-weight:bold;white-space:nowrap;">Biller Code: <?=$bpay_biller?><br />Ref: <?=$bpay_reference?></td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:justify;font-size:8pt;width:0px;">
			<div style="white-space:nowrap;font-weight:bold;">Telephone & Internet Banking - B<span style="font-size:6pt;">PAY</span></div>
			Call your bank, credit union or building society to make this payment from your cheque, savings or credit card account. More info: <a href="https://www.bpay.com.au/" target="_blank" class="title_link">www.bpay.com.au</a>
		</td>
	</tr>
</table>
<?php
}
}
?>