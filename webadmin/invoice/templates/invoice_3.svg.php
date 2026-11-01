<?php
//Expected input variables
/*
$charges_list        - array of charge ids to charge names
$formatted_invoice_number    - invoice number to display
$images                - url to selected available images
$invoice_comments    - the comments at the bottom of the invoice
$invoice_contacts    - the address boxes of the invoice
$invoice_diagonal    - text to display diagonally across invoice
$invoice_fields        - the inventory fields to display
$invoice_inforow    - the inforow details of the invoice
$invoice_inventory    - All the itemised inventory to display
$invoice_labels        - company information for invoice company
$invoice_reference    - reference number for payment methods
$invoice_type        - a number representing the invoice status
$invoice_type_text    - title of invoice to display
$payment_data        - method information for invoice payment method
$staff                - information about staff member
$terms_list            - array of term ids to term names
$time_to_use        - time to display on invoice
$warranty_list        - array of warranty ids to warranty names
$website_info        - website information for invoice company
$website_path        - directory holding the invoice's resource website
$website_url        - url for the invoice's resource website
 */
echo '<' . '?xml version="1.0" encoding="utf-8"?>' . "\n";
?>
<!-- Generator: Adobe Illustrator 13.0.0, SVG Export Plug-In . SVG Version: 6.00 Build 14948)  -->
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="646px" height="994px" viewBox="0 0 646 994" enable-background="new 0 0 646 994" xml:space="preserve">
<?php
//Space calculations
$space_above = 0;
//Header space
$space_above += 128;
//Contact space
foreach (array_chunk(array_keys($invoice_contacts), 2) as $row) {
    $temp = 30;
    foreach ($row as $key) {
        $temp = max($temp, count(explode("\n", $invoice_contacts[$key])) * 10.0 + 20);
    }

    $space_above += $temp;
}
//Info space
$temp = 0;
foreach ($invoice_inforow as $key => $value) {
    $temp = max($temp, count(explode("\n", $key)) * 10 + count(explode("\n", $value)) * 10 + 20);
}

$space_above += $temp;

$space_below = 0;
//Totals
$space_below += 120;
//Signature/Comments

$space_available = 994 - ($space_above + $space_below);

//-----------------------------------------------------------------------------
$ypos_full = 0;
$ypos      = 0;
?>
<g id="header" transform="matrix(1 0 0 1 0 <?=$ypos_full?>)">
	<g>
<?php
$temp = explode("\n", $invoice_labels['company_invoiceheader']);
if ('' != $images['company_logo']) {
    array_shift($temp);
?>
		<image overflow="visible" width="410" height="102" xlink:href="/<?=$images['company_logo']?>" transform="matrix(1 0 0 1 118 0)"></image>
		<rect x="118" y="67.37" fill="#FFFFFF" width="410" height="<?=count($temp) * 10 + 4.285?>"/>
<?php
} else {
?>
		<text x="323" y="54.9048" text-anchor="middle" font-family="'TimesNewRomanPSMT'" font-size="48"><![CDATA[<?=array_shift($temp)?>]]></text>
<?php
}
$y = 78.9048;
while (count($temp)) {
?>
		<text x="323" y="<?=$y?>" text-anchor="middle" font-family="'TimesNewRomanPSMT'" font-size="8"><![CDATA[<?=array_shift($temp)?>]]></text>
<?php
	$y += 10;
}
?>
	</g>
	<g>
		<rect x="484.5" y="94.004" fill="#F9EC31" width="161.5" height="17.209"/>
		<text x="565.25" y="108.0576" text-anchor="middle" fill="#231F20" font-family="'TimesNewRomanPS-BoldMT'" font-size="12"><![CDATA[<?=$invoice_type_text?>]]></text>
	</g>
</g>
<?php
$ypos = 111.213 + 10;
//-----------------------------------------------------------------------------
$ypos_full += $ypos;
$ypos = 0;
?>
<g id="contacts" transform="matrix(1 0 0 1 0 <?=$ypos_full?>)">
<?php
foreach (array_chunk(array_keys($invoice_contacts), 2) as $row) {
    $temp = 24;
    foreach ($row as $key) {
        $temp = max($temp, count(explode("\n", $invoice_contacts[$key])) * 10.0 + 14);
    }

?>
	<g>
		<rect x="2" y="<?=$ypos + 2.0?>" fill="#231F20" stroke="#231F20" stroke-width="0.25" width="316" height="<?=$temp?>"/>
		<rect x="0" y="<?=$ypos?>" fill="#FFFFFF" stroke="#231F20" stroke-width="0.25" width="316" height="<?=$temp?>"/>
		<text x="7" y="<?=$ypos + 16?>" fill="#231F20" font-family="'ComicSansMS-Bold'" font-size="8"><![CDATA[<?=$row[0]?>]]></text>
<?php
	$y = $ypos + 16;
    foreach (explode("\n", $invoice_contacts[$row[0]]) as $line) {
?>
		<text x="64" y="<?=$y?>" fill="#231F20" font-family="'ArialMT'" font-size="8"><![CDATA[<?=$line?>]]></text>
<?php
		$y += 10;
    }
?>
	</g>
<?php
	if (count($row) > 1) {
?>
	<g>
		<rect x="330" y="<?=$ypos + 2.0?>" fill="#231F20" stroke="#231F20" stroke-width="0.25" width="316" height="<?=$temp?>"/>
		<rect x="328" y="<?=$ypos?>" fill="#FFFFFF" stroke="#231F20" stroke-width="0.25" width="316" height="<?=$temp?>"/>
		<text x="335" y="<?=$ypos + 16?>" fill="#231F20" font-family="'ComicSansMS-Bold'" font-size="8"><![CDATA[<?=$row[1]?>]]></text>
<?php
		$y = $ypos + 16;
        foreach (explode("\n", $invoice_contacts[$row[1]]) as $line) {
?>
		<text x="392" y="<?=$y?>" fill="#231F20" font-family="'ArialMT'" font-size="8"><![CDATA[<?=$line?>]]></text>
<?php
			$y += 10;
        }
?>
	</g>
<?php
	}
    $ypos += $temp + 10;
}
?>
</g>
<?php

$ypos_full += $ypos;
$ypos = 0;

if (count($invoice_inforow)) {
    $height = 20;
    $width  = 0;
    $temp   = array();
    foreach ($invoice_inforow as $key => $value) {
        $bbox = imageftbbox(
            8.0,
            0.0,
            __DIR__ . '/../fonts/Arial.ttf',
            $value,
            array('linespacing' => 1.4)
        );

        $width += $temp[$key] = $bbox[2] - $bbox[6];
        $height               = max($height, count(explode("\n", $value)) * 10 + 10);
    }
    foreach ($temp as $key => $value) {
        $temp[$key] += (646 - $width) / count($temp);
    }

?>
<g id="inforow" transform="matrix(1 0 0 1 0 <?=$ypos_full?>)">
	<g>
		<rect x="0" y="0" opacity="0.7" fill="#0000FF" width="646" height="10"/>
<?php
	$x = 0;
    foreach ($invoice_inforow as $key => $value) {
?>
		<text x="<?=$x + ($temp[$key] / 2)?>" y="9" text-anchor="middle" fill="#FFFFFF" font-family="'FranklinGothic-Medium'" font-size="8"><![CDATA[<?=$key?>]]></text>
<?php
		$x += $temp[$key];
    }
?>
	</g>
	<g>
		<rect x="0" y="10" fill="none" stroke="#231F20" stroke-width="0.25" width="646" height="<?=$height?>"/>
<?php
	$x = 0;
    foreach ($invoice_inforow as $key => $value) {
?>
<?php
		$y = $ypos + 24;
        foreach (explode("\n", $value) as $line) {
?>
		<text x="<?=$x + ($temp[$key] / 2)?>" y="<?=$y?>" text-anchor="middle" fill="#231F20" font-family="'ArialMT'" font-size="8"><![CDATA[<?=$line?>]]></text>
<?php
			$y += 10;
        }
        if (end(array_keys($invoice_inforow)) != $key) {
?>
		<line fill="none" stroke="#231F20" stroke-width="0.25" x1="<?=$x + $temp[$key]?>" y1="0" x2="<?=$x + $temp[$key]?>" y2="<?=$height + 9?>"/>
<?php
		}
?>
<?php
		$x += $temp[$key];
    }
?>
	</g>
</g>
<?php
	$ypos = $height + 20;
}
//-----------------------------------------------------------------------------
$ypos_full += $ypos;
$ypos = 0;

$height = 954 - $ypos_full;

$height = 10;
foreach ($invoice_inventory as $key => $item) {
    $height += count(explode("\n", $item['product_name'])) * 10 + (('' != $item['product_notes']) ? count(explode("\n", $item['product_notes'])) * 10 : 0) + 10;
}

$height = max(480, $height);

?>
<g id="inventory" transform="matrix(1 0 0 1 0 <?=$ypos_full?>)">
	<g>
		<rect x="0" y="0" opacity="0.7" fill="#0000FF" width="646" height="10"/>
		<text x="38.25" y="9" text-anchor="middle" fill="#FFFFFF" font-family="'FranklinGothic-Medium'" font-size="8">PRODUCT ID</text>
		<text x="246.5" y="9" text-anchor="middle" fill="#FFFFFF" font-family="'FranklinGothic-Medium'" font-size="8">DESCRIPTION OF GOODS</text>
		<text x="454.75" y="9" text-anchor="middle" fill="#FFFFFF" font-family="'FranklinGothic-Medium'" font-size="8">QUANTITY</text>
		<text x="531.25" y="9" text-anchor="middle" fill="#FFFFFF" font-family="'FranklinGothic-Medium'" font-size="8">UNIT PRICE</text>
		<text x="607.75" y="9" text-anchor="middle" fill="#FFFFFF" font-family="'FranklinGothic-Medium'" font-size="8">AMOUNT</text>
	</g>
	<g>
		<rect x="0" y="10" fill="none" stroke="#231F20" stroke-width="0.25" width="646" height="<?=$height - 10?>"/>
		<line fill="none" stroke="#231F20" stroke-width="0.25" x1="76.5" y1="10" x2="76.5" y2="<?=$height - 1?>"/>
		<line fill="none" stroke="#231F20" stroke-width="0.25" x1="416.5" y1="10" x2="416.5" y2="<?=$height - 1?>"/>
		<line fill="none" stroke="#231F20" stroke-width="0.25" x1="493" y1="10" x2="493" y2="<?=$height - 1?>"/>
		<line fill="none" stroke="#231F20" stroke-width="0.25" x1="569.5" y1="10" x2="569.5" y2="<?=$height - 1?>"/>
<?php
$y = 10;
foreach ($invoice_inventory as $key => $item) {
    $row_height = 20;
    $row_height = max($row_height, count(explode("\n", $item['product_name'])) * 10 + (('' != $item['product_notes']) ? count(explode("\n", $item['product_notes'])) * 10 : 0) + 10);
?>
		<g>
			<text x="38.25" y="<?=$y + ($row_height / 2) + 4.5?>" text-anchor="middle" fill="#231F20" font-family="'ArialMT'" font-size="8"><![CDATA[<?=$item['product_str']?>]]></text>
<?php
	$temp = $y + 14;
    foreach (explode("\n", $item['product_name']) as $row) {
?>
			<text x="81.5" y="<?=$temp?>" text-anchor="start" fill="#231F20" font-family="'ArialMT'" font-size="8"><![CDATA[<?=$row?>]]></text>
<?php
		$temp += 10;
    }
    foreach (explode("\n", $item['product_notes']) as $row) {
?>
			<text x="81.5" y="<?=$temp?>" text-anchor="start" fill="#231F20" font-family="'TimesNewRomanPS-ItalicMT'" font-size="8"><![CDATA[<?=$row?>]]></text>
<?php
		$temp += 10;
    }
?>
			<text x="454.75" y="<?=$y + ($row_height / 2) + 9?>" text-anchor="middle" fill="#231F20" font-family="'ArialMT'" font-size="8"><![CDATA[<?=$item['product_qty']?>]]></text>
			<text x="531.25" y="<?=$y + ($row_height / 2) + 9?>" text-anchor="middle" fill="#231F20" font-family="'ArialMT'" font-size="8"><![CDATA[<?=$item['product_offer']?>]]></text>
			<text x="607.75" y="<?=$y + ($row_height / 2) + 9?>" text-anchor="middle" fill="#231F20" font-family="'ArialMT'" font-size="8"><![CDATA[<?=$item['product_total']?>]]></text>
			<line fill="none" stroke="#808080" stroke-width="0.5" x1="0" y1="<?=$y + $row_height + 10?>" x2="645" y2="<?=$y + $row_height + 10?>"/>
		</g>
<?php
	$y += $row_height;
}
?>
	</g>
</g>
<?php
$ypos = $height + 10;
//-----------------------------------------------------------------------------
$ypos_full += $ypos - 11;
$ypos = 0;
?>
<g id="totals" transform="matrix(1 0 0 1 0 <?=$ypos_full?>)">
	<rect x="416.5" y="80" fill="#AEDEE4" width="229.5" height="40"/>
	<rect x="0" y="0" fill="none" stroke="#231F20" stroke-width="0.25" width="646" height="120"/>
	<line fill="none" stroke="#231F20" stroke-width="0.25" x1="416.5" y1="0" x2="416.5" y2="119"/>
	<line fill="none" stroke="#231F20" stroke-width="0.25" x1="569.5" y1="0" x2="569.5" y2="119"/>
	<line fill="#231F20" stroke="#231F20" stroke-width="0.25" x1="416.5" y1="40" x2="646" y2="40"/>
	<line fill="#231F20" stroke="#808080" stroke-width="0.25" x1="416.5" y1="80" x2="646" y2="80"/>
	<g>
		<text x="5" y="14" text-anchor="start" fill="#231F20" font-family="'FranklinGothic-Medium'" font-size="8">Notes:</text>
<?php
$y = 24;
foreach ($invoice_comments as $key => $comment) {
?>
		<g>
			<circle fill="#231F20" cx="10" cy="<?=$y - 3?>" r="2"/>
			<text x="15" y="<?=$y?>" text-anchor="start" fill="#231F20" font-family="'Garamond'" font-size="8"><![CDATA[<?=$key?>: <?=$comment?>]]></text>
		</g>
<?php
	$y += 10;
}
?>
	</g>
	<g>
		<g>
			<text x="564.5" y="14" text-anchor="end" fill="#231F20" font-family="'FranklinGothic-Medium'" font-size="8">Sub Total</text>
			<text x="564.5" y="24" text-anchor="end" fill="#231F20" font-family="'FranklinGothic-Medium'" font-size="8">Discount (XX%)</text>
			<text x="564.5" y="34" text-anchor="end" fill="#231F20" font-family="'FranklinGothic-Medium'" font-size="8">Shipping and Handling</text>
		</g>
		<g>
			<text x="607.75" y="14" text-anchor="middle" fill="#231F20" font-family="'Arial-BoldMT'" font-size="8">13320.63</text>
			<text x="607.75" y="24" text-anchor="middle" fill="#231F20" font-family="'Arial-BoldMT'" font-size="8">(1332.06)</text>
			<text x="607.75" y="34" text-anchor="middle" fill="#231F20" font-family="'Arial-BoldMT'" font-size="8">2332.96</text>
		</g>
	</g>
	<g>
		<g>
			<text x="564.5" y="54" text-anchor="end" fill="#231F20" font-family="'FranklinGothic-Medium'" font-size="8">Total</text>
			<text x="564.5" y="74" text-anchor="end" fill="#231F20" font-family="'FranklinGothic-Medium'" font-size="8">Amount Paid</text>
		</g>
		<g>
			<text x="607.75" y="54" text-anchor="middle" fill="#231F20" font-family="'Arial-BoldMT'" font-size="8">13320.63</text>
			<text x="607.75" y="74" text-anchor="middle" fill="#231F20" font-family="'Arial-BoldMT'" font-size="8">(2332.96)</text>
		</g>
	</g>
	<g>
		<g>
			<text x="564.5" y="104" text-anchor="end" fill="#231F20" font-family="'FranklinGothic-Medium'" font-size="8">Total Due</text>
		</g>
		<g>
			<text x="607.75" y="104" text-anchor="middle" fill="#231F20" font-family="'Arial-BoldMT'" font-size="8">13187.57</text>
		</g>
	</g>
</g>
<?php
$ypos = 130;
//-----------------------------------------------------------------------------
$ypos_full += $ypos;
$ypos = 0;
?>
<g id="footer" transform="matrix(1 0 0 1 0 <?=$ypos_full?>)">
	<image overflow="visible" width="219" height="106" xlink:href="data:image/jpeg;base64,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
Y8yf9Wf/2Q==" transform="matrix(0.5624 0 0 0.5624 522.8344 0)"></image>
	<image overflow="visible" width="160" height="115" xlink:href="data:image/jpeg;base64,/9j/4AAQSkZJRgABAgEAggCCAAD/7AARRHVja3kAAQAEAAAAHgAA/+4AIUFkb2JlAGTAAAAAAQMA
EAMCAwYAAAM6AAAFmwAADef/2wCEABALCwsMCxAMDBAXDw0PFxsUEBAUGx8XFxcXFx8eFxoaGhoX
Hh4jJSclIx4vLzMzLy9AQEBAQEBAQEBAQEBAQEABEQ8PERMRFRISFRQRFBEUGhQWFhQaJhoaHBoa
JjAjHh4eHiMwKy4nJycuKzU1MDA1NUBAP0BAQEBAQEBAQEBAQP/CABEIAHUAoQMBIgACEQEDEQH/
xAC/AAEAAwADAQAAAAAAAAAAAAAABAUGAQIDBwEBAQEBAAAAAAAAAAAAAAAAAAIBAxAAAQUAAgED
AgcBAAAAAAAABQECAwQGABEQIDASIRZAUDEyExQVJhEAAgECBAMDCAcFBAsAAAAAAQIDEQQAIRIF
MUETUWEiIHGBkTJCUhQwscHRYnIjoZIzQxUQQKJT8IKywtJjcyQ0VCUSAAEDAgQFBQAAAAAAAAAA
AAEAESEgMRBRoQJBYXGBEjBgseEy/9oADAMBAAIRAxEAAAD6AAAAj0ZpGLi03r537a3zFzcahElS
5AAAAcZnVvlPH26INz7xivsIPfV7Qd4xa9IFrimkSffcv5/zzSxt8IACCUseJM6vaH6z8V9jf8yj
+/KXDkeNPfNYuPusrafnZWg16WOJ2sORLjJ63IUsulf3p31Of0MgkAAA8faMY6VZXvRgt7gd8zkc
9ZPWQ6QLr59vtZfVw6DGsRpMgAHCqLTLNJT1qpuH1YbGNKwEgM7R72ktae3zm+JUe8l4zHbS+JRe
eP8Aq1MzaWNVK3rszHp01kqZjkQAAA6Z3StfP4v0lb5767fyML23XufObXZ84q7PlIMAAAAAAAAA
AAAAf//aAAgBAgABBQDz0vPg7n8bufF3qRFVUYic7VW/XtO+/qnF6VXM68p9eKnxTvriuXyjlTjX
987Rqvb0vGfu6Xt/7vQn6ucicl8NXpXp9f3en6N4xvavXtfDXJ0sa8+S87bx6I3nbl4jPo53fpRy
pz+TnyZz5t4si8VVX3//2gAIAQMAAQUA/Kk/Lv/aAAgBAQABBQD8XavU6bLW7BwrJtikqSazTv4u
n06LDp9Oix7M5HytvRT1pkx95vtFDY4TFd1Bsg1Ack7BASlcjiuDY7WiWMQRCQ/6IQVavkiGjrjB
1qrQolBUgJOqWnNjWCjI8tB6zepe2eiIkso4oIrQ6l0sraVik7TQEhdLQEyMpm5BMVgpMgdVmIkx
ZY2aIVIc/iqU6XzV0Q4vbGWaVnO6VhRPTpzVhZs/noZoYDNwUaJUBVIkPhP2alfHDvlBRp1k8z1K
thLGRGudM4sClFhXzzlCQKnGWG2a1kCXiMDfJkkwUNBC5bkyyGi3CJSkQzobOObJ7Dmte0hVdmrN
LM333CAqNwnN2W1dB52b/wC1dz89eIday4a4Rz1WIgT9q5Wjt1axS1Dns1YMSkpmtrFPOhT/AK2a
Fy5ulfkOHMMrft72iFyKhSyFR0YD6NRHJcL+dqjqdnLyNjq9InKc324f9lzmsbalfqrzWtY3RXHR
VMnCl8v5LDoig8QSsjrMM0U8RUVULU4CZXNrSI0r8XpVURCelEjuf1DmlWpUr0q5C/XH1r1i+XIj
qEA6n6NNn5bagj8tOSneq3oXsY9tzH0JJFr7WgiaHRQIuvsNcuqtuRSewtc+3jxBBudEjPBI3Vor
YJETN7PgK4Wt6jmXqFFneXB2R+xV0dMsOut/XxdWRKdQmaUq3tWySxRJa0wyFxTWTuUdnTBXgwRQ
FQexLFFMwhiB077WZ0Vbn+gYoK3YXY0+9rfxTSQtfJpCFhzB+hILXxRGzwZnBAtPdVEXj6NKTiiB
a8YNHR8ZHGz8f//aAAgBAgIGPwCi2FqmCzKcQhdmR0W0S68SH6JxIoYdyibcD9KIxgrmgHd749ES
DnpZHlFMIhu6HfAFPmnF+Ipz3fCc2o8dycSmM9VbVCLphon3FkwgUwpAK/Ks6gMpn1//2gAIAQMC
Bj8A9sf/2gAIAQEBBj8A/vZkupkhUc3IGCsLPdOOUSkg+k5YJs9qbTSuuQ0+zHhS1gB+Jhl62xQ3
NmPSv/Fj27Obu1KP97FZ9sEiji0TV9XHAS8iltGPN1qvrXGq0uEmHYrAn1fR9S8lCk+zGM3bzKME
2arttk2SzzfxG/KPuBx85eCSZBmbq9cwxD8qGrn9mG+XvoyI8nitEVCD3u+p8dG52wyhLgWkvWna
WUOeYTmMWNvaxQQ210GDH5cSupXmOZrXBkbpLcSGRUmEKBlAYqpZO3LDbQ0dr17eST5mZoUKtEhV
QEA4mtcWUUO3xySXZZf03MLqV05gjLnj+p2tzJHBGG6kV0onClM2FT4qeY4F5ZqQtKi725y6j80R
Oseg4rc6dzslyaWPKVB+IcR6RjrWcgantIcnU9hX6A7Zsqie84Szfy4RzJPDLEm4zP8AMMtTNuM4
LRgDiII/fp28MJebdKlxPC4N21yCbgxhhq6StRRl2Y2rdbZDdWFvKJpo0GrUjUKtTnlXEu8wH5Tb
fl9E0kg6SySk8lanDF3uheO7S4YmOOFGkdTyZWppFeeLe6hsr5GtK9Eoi5MT7WfmxcWkW2Xtbp9c
txqAlJ7qCg4YtbpNvvbOW0XT1I0Vi4qSxfhWtaYtrrcJDFZxAobaaN0NCM/EuVa4+S2BA8U46fUg
8SxKxGovTOpGLiV3SL5JflJLaMEayvCVxzr24ktnjaKSCMNLf24zhZj7MoGRXhWuIruzkW3u5KG2
uosra6BzCsOCMezgcNZ3a/L7nDlLCctVPeWvlDY9rYLdSrquZ60EEVKkk8ssKzKV24HUAcnvHH8y
Tno+FfScPYb0FjtbqnyUqZQoFy0YuJ72KJtpvAJQxP6nWINRAF8R1ZYjtNqU7ZtaE6Jbg652Umvh
HLuwJdweTcJ+JeZjp9CjAW3gjiA4aVA8jTPCko/Gob68GaxL2FxykgYgeleeJpL6MSxzp0zulsv6
qDk0icDTEQHUuIbiRpLncYn8E8LZ9OQVFDq4jEe1XMeuB4zWNF1LHEnh1NThTEclq5a9gXrbddDM
3EKCrROebovD4lxHeJ4XPhmT4JBxHkT3z59JfAp95zko9eOndEtLdabzc5Oehzqht6/i9pu6mLq6
27cEtPk5XiisgBTTFl+oT8VMbfd7hB17yZwbe1XLqTKSnn0duBuW8EXF+wGiM/w4ByRF4ZfQlWAK
kUIOYIwb611f0e6PTvrdSf0S/hEsfZiOKUqNuSCWH5qNqvPFOxcVrXPxZ4W2sxoltAJLM1qVkizT
Pv4HBjiGiz3iEXMMfJZBUuvoIYeRtOzk0juJupMPwqQPqJw25XLrCdwmeUFyF8FSkS+hFGHuFleG
dwHnihk0B1bmwHbi43QoBa2TGz22P3UWPJnHefo5baUakmQowPeKY22SSWSOC1nlguWiFWYRBjEp
yORIAxFNdGdkuopJbgOpEMdSDCI/RjbCMhb7pcW60/y3ZHp/jPkbbXnbTBPzaZKftxsl7HB83DY6
JZrcCpZdBUkD8JOIbyztpLaG1glSeWQaS5cURPQc8RKPbWSUSDnq1n7Po5ryY0SFCx76cB6TiHrq
C1wzzspH+Y1Rke7HYBjZlTM3F3cX7D/lmTwH92LyNr3lVqtrNpl/IxB+w4m26tWsZWVO+GQ9WFvM
VbGWWJ7G48O3bm5mtZT7KSt7SE9/3fRFmICgVJOQAwtlbV/otq4a6nHCZ14IvdgIooqgBQOAAwLO
BtN1e1ijPwJT9WU9yLi53aNStlbRraWIPwINNfUKnz+RNZS8JV8LfCwzVvQcfrqfnNuHy97F70to
D4JFHNov9nCTQsHjkAZGGYIOGtLpaqc1Ye0jDgy4Fpu6NdbevhhvkBZlXkJBgS2kyzIeanMeceVU
4KSS9Wf3YIvG5PmGA19q27aiai3XKaVfx9mEtrZBHCgoqjDXNw1FGSqM2djwVRzJw1jDnf3fgnIN
VtLbj0q/EeL+rEVnbikcS07yebHz+Sm6bb+nudtmpH81R7p/07sOscZ6YJ+a2734TXxSQA8V7V5Y
E9rIJIzzHEHsI5HBR1DK2RUioODcWEkm33Bz1QkhSe9cUhmh3CNeGsaXI/ZgC62Vz2tE2ofVjS+0
XanuUH7cEQbRdOw5EAYpbbbHbA8GmetPRlgf1bdGWM8YbYaR6Wyxqt4AZecr+Jz6T/Z0VrPdsPBb
x5se9vhHecC3tKT7hmA6Z29ovPQfef8AH6saF/UuZM55zxY9g7vL+ZiJtb9c0uEyzHDVT68Fr1Ht
5icr63FY5f8Aqx+y314DXsQljp/5Nt41/wBeP2lxW2uEfurRh5wf7ZzEpeTptoUHSSacm5YUdZ3J
eMENJpVxGaRhpOFK5V54BYUamY78apHCAc2NMGOBmu5hl04Br9bcBgxGQQCtOhbESzt3M/sJ9eCZ
lO3WEhrICS00352bxH6sdCzjCD3m4sx7WP0JjlQOjcVYVB9BwZ7F3sZ+TRk6fVivTh3BBmH9mT95
dLYKyx3tpTLwt1V9AlH24H/emvZNbmv+A4AN1bMCKEGGX7sao3tUYII6x20h8IOoCjUGRxpSe7mB
92GJYx6zqOAVsHYk/wAW7dn/AGNpXH/073RF/wCvbjSvm8NBgG2gHUH81/E/rPD6ahzGP1LeNq9q
KfsxnaQ/uL92PBaxDzIv3YoihR+EAfV/f//Z" transform="matrix(0.5535 0 0 0.5535 557.44 0)"></image>
	<line fill="none" stroke="#231F20" stroke-width="0.25" stroke-dasharray="1" x1="416.5" y1="64" x2="645" y2="64"/>
	<text x="646" y="73" text-anchor="end" fill="#231F20" font-family="'MyriadPro-Regular'" font-size="8">COINOP EXPRESS Company Stamp and Signature</text>

	<g>
		<text transform="matrix(1 0 0 1 5.7305 739.814)"><tspan x="0" y="0" fill="#231F20" font-family="'TimesNewRomanPS-BoldMT'" font-size="8">Bank Details:</tspan><tspan x="0" y="9.6" fill="#231F20" font-family="'TimesNewRomanPSMT'" font-size="8">Account Name: </tspan><tspan x="52.426" y="9.6" fill="#231F20" font-family="'TimesNewRomanPSMT'" font-size="8" letter-spacing="17">	</tspan><tspan x="72" y="9.6" fill="#231F20" font-family="'TimesNewRomanPSMT'" font-size="8">COINOPEXPRESS.COM LTD.</tspan><tspan x="0" y="19.2" fill="#231F20" font-family="'TimesNewRomanPSMT'" font-size="8">Account No:      </tspan><tspan x="53.102" y="19.2" fill="#231F20" font-family="'TimesNewRomanPSMT'" font-size="8" letter-spacing="16">	</tspan><tspan x="72" y="19.2" fill="#231F20" font-family="'TimesNewRomanPSMT'" font-size="8">400-070397-274</tspan><tspan x="0" y="28.8" fill="#231F20" font-family="'TimesNewRomanPSMT'" font-size="8">Bank Name:       </tspan><tspan x="54.211" y="28.8" fill="#231F20" font-family="'TimesNewRomanPSMT'" font-size="8" letter-spacing="15">	</tspan><tspan x="72" y="28.8" fill="#231F20" font-family="'TimesNewRomanPSMT'" font-size="8">The Hongkong and Shanghai </tspan><tspan x="0" y="38.4" fill="#231F20" font-family="'TimesNewRomanPSMT'" font-size="8" letter-spacing="34">		</tspan><tspan x="72" y="38.4" fill="#231F20" font-family="'TimesNewRomanPSMT'" font-size="8">Banking Corporation</tspan><tspan x="0" y="48" fill="#231F20" font-family="'TimesNewRomanPSMT'" font-size="8">Address:             </tspan><tspan x="54.441" y="48" fill="#231F20" font-family="'TimesNewRomanPSMT'" font-size="8" letter-spacing="15">	</tspan><tspan x="72" y="48" fill="#231F20" font-family="'TimesNewRomanPSMT'" font-size="8">1 Queen&apos;s Road, Central, </tspan><tspan x="0" y="57.6" fill="#231F20" font-family="'TimesNewRomanPSMT'" font-size="8" letter-spacing="34">		</tspan><tspan x="72" y="57.6" fill="#231F20" font-family="'TimesNewRomanPSMT'" font-size="8">Hong Kong</tspan><tspan x="0" y="67.2" fill="#231F20" font-family="'TimesNewRomanPSMT'" font-size="8">Swift Code:        </tspan><tspan x="54.445" y="67.2" fill="#231F20" font-family="'TimesNewRomanPSMT'" font-size="8" letter-spacing="15">	</tspan><tspan x="72" y="67.2" fill="#231F20" font-family="'TimesNewRomanPSMT'" font-size="8">HSBC HKHH HKH</tspan></text>
	</g>

	<text x="323" y="112" text-anchor="middle" fill="#AEDEE4" font-family="'CancunPlain'" font-size="11">Thankyou for your Business!</text>
</g>
</svg>