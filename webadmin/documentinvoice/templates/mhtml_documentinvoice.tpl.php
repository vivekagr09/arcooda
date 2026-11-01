<?php
ob_start();

$staff        = staffFromDatabase(staffFromUser($_SERVER['REMOTE_USER']));
$payment_data = $DB->query_first("SELECT * FROM `highwaygroup`.`payment_methods` WHERE site_id=" . intval($sinvoice['site_id']) . " AND payment_method_id=" . formatSQLString($sinvoice['paymenttype_id']) . " LIMIT 1", MYSQLI_ASSOC);
$website_path = '/home/hwgroup/public_html/';

$force_images                   = array();
$force_images['company_logo']   = 'webadmin/images/sites/site_' . $sinvoice['site_id'] . '/logo_invoice.png';
$force_images['payment_method'] = 'webadmin/images/payment/' . $payment_data['payment_method_image'];
$force_images['stamp']          = 'webadmin/images/sites/site_' . $sinvoice['site_id'] . '/stamp.png';
$force_images['signature']      = 'images/sigs/' . $staff['signature'];

$boundary = md5(mt_rand(0, 65535));

ob_start();
foreach ($force_images as $key => $value) {
    if (is_file($website_path . $value)) {
        $new_filename = md5($value) . '.' . end(explode('.', $value));
//        $force_images[$key]='cid:'.$new_filename;
        $force_images[$key] = $new_filename;
        echo '--' . $boundary . "\r\n";
        echo 'Content-Type: ' . mime_content_type($website_path . $value) . "\r\n";
        echo 'Content-Transfer-Encoding: base64' . "\r\n";
//        echo 'Content-ID: <'.$new_filename.'>'."\r\n";
        echo 'Content-Location: ' . $new_filename . "\r\n";
        echo "\r\n";
        echo chunk_split(base64_encode(file_get_contents($website_path . $value)));
        echo "\r\n";
    } else {
        $force_images[$key] = '';
    }
}
$attach_buffer = ob_get_clean();
include __DIR__ . '/html_documentinvoice.tpl.php';

ob_start();
?>
<html xmlns:v=3D"urn:schemas-microsoft-com:vml"
xmlns:o=3D"urn:schemas-microsoft-com:office:office"
xmlns:w=3D"urn:schemas-microsoft-com:office:word"
xmlns=3D"http://www.w3.org/TR/REC-html40">

<head>
<meta http-equiv=3DContent-Type content=3D"text/html; charset=3Dus-ascii">
<meta name=3DProgId content=3DWord.Document>
<meta name=3DGenerator content=3D"Microsoft Word 11">
<meta name=3DOriginator content=3D"Microsoft Word 11">
<link rel=3DFile-List href=3D"documentinvoice_view_files/filelist.xml">
<link rel=3DEdit-Time-Data href=3D"documentinvoice_view_files/editdata.mso">
<!--[if !mso]>
<style>
v\:* {behavior:url(#default#VML);}
o\:* {behavior:url(#default#VML);}
w\:* {behavior:url(#default#VML);}
.shape {behavior:url(#default#VML);}
</style>
<![endif]-->
<title>Invoice D#3718</title>
<!--[if gte mso 9]><xml>
 <o:DocumentProperties>
  <o:Author>User</o:Author>
  <o:LastAuthor>User</o:LastAuthor>
  <o:Revision>2</o:Revision>
  <o:TotalTime>0</o:TotalTime>
  <o:Created>2007-05-03T08:00:00Z</o:Created>
  <o:LastSaved>2007-05-03T08:00:00Z</o:LastSaved>
  <o:Pages>1</o:Pages>
  <o:Words>91</o:Words>
  <o:Characters>524</o:Characters>
  <o:Company>Microsoft Corporation</o:Company>
  <o:Lines>4</o:Lines>
  <o:Paragraphs>1</o:Paragraphs>
  <o:CharactersWithSpaces>614</o:CharactersWithSpaces>
  <o:Version>11.6360</o:Version>
 </o:DocumentProperties>
</xml><![endif]--><!--[if gte mso 9]><xml>
 <w:WordDocument>
  <w:ValidateAgainstSchemas/>
  <w:SaveIfXMLInvalid>false</w:SaveIfXMLInvalid>
  <w:IgnoreMixedContent>false</w:IgnoreMixedContent>
  <w:AlwaysShowPlaceholderText>false</w:AlwaysShowPlaceholderText>
  <w:BrowserLevel>MicrosoftInternetExplorer4</w:BrowserLevel>
 </w:WordDocument>
</xml><![endif]--><!--[if gte mso 9]><xml>
 <w:LatentStyles DefLockedState=3D"false" LatentStyleCount=3D"156">
 </w:LatentStyles>
</xml><![endif]-->
<?php
$sinvoice_buffer = str_replace('<head>', ob_get_clean(), $sinvoice_buffer);

echo 'From: <Highway Group Pty Ltd>' . "\r\n";
echo 'Subject: Invoice ' . formatFileSafe(documentinvoiceFormatNumber($dinvoice_id, $sinvoice['revision_id'], $sinvoice['site_id'])) . "\r\n";
echo 'Date: ' . date('D, d M Y H:i:s O') . "\r\n";
echo 'MIME-Version: 1.0' . "\r\n";
echo 'Content-Type: multipart/related;' . "\r\n";
echo '	type="text/html";' . "\r\n";
echo '	boundary="' . $boundary . '"' . "\r\n";
echo 'X-MimeOLE: Produced By Microsoft MimeOLE V6.00.2900.2180' . "\r\n";
echo "\r\n";
echo 'This is a multi-part message in MIME format.' . "\r\n";
echo "\r\n";
echo '--' . $boundary . "\r\n";
echo 'Content-Type: text/html;' . "\r\n";
echo '	charset="Windows-1252"' . "\r\n";
echo 'Content-Transfer-Encoding: base64' . "\r\n";
echo "\r\n";
echo chunk_split(base64_encode($sinvoice_buffer));
echo $attach_buffer;

echo '--' . $boundary . '--' . "\r\n";

$sinvoice_buffer = ob_get_clean();
?>