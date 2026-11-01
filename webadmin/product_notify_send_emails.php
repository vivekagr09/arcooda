<?php

$table = 'product_notify_email';
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

show_locked_msg($table, $id);

include __DIR__ . '/generic_mail.php';

function sendNotification()
{
    global $DB, $id, $EditorValue, $subject;

    $html_message = $EditorValue;
    $text_message = trim(wordwrap(strip_tags($html_message)));
    $text_message = str_replace(["\t", "  "], '', $text_message);
    $text_message = preg_replace("/\\n\\n+/", "\n", $text_message);

    $statement = "UPDATE generic_mailout SET active = 'n' WHERE section = 'product_notify' AND product_id = '$id'";

    $DB->query($statement);

    $statement = "SELECT * FROM product_notify_email WHERE product_id = '$id' AND notified = 'n'";

    $result = $DB->query($statement);

    $html_message = addslashes($html_message);
    $text_message = addslashes($text_message);
    $subject      = addslashes($subject);

    while ($row = $DB->fetch_array($result)) {
        $statement = "INSERT INTO generic_mailout (section, email, subject, text_msg, html_msg, product_id)
						VALUES ('product_notify', '$row[email]', '$subject', '$text_message', '$html_message', '$id')";
        $DB->query($statement);
    }

    send('product_notify', 'sales@arcooda.com', $id);
}

function compose()
{
    global $DB, $id;

    $id = intval($id);

    $statement = "SELECT p.*, t.name as type_name, p.product_name AS item ,
						DATE_FORMAT(p.date_available, '%d %M, %Y') AS available_from,
						tp.name as section
				FROM products p, product_types t, product_types tp
				WHERE p.id = '$id' AND t.id = p.type AND t.parent = tp.id";

    $product = $DB->query_first($statement);

    $item_link = encode_product_url($product['item'], $product['id']);
    $type      = encode_section_url($product['type_name']);
    $section   = $product['section'];

    $product_link = "https://www.arcooda.com/products/$section/$type/$item_link.html";

    $subject  = stripslashes($product['item']) . ' is now available at arcooda.com';
    $html_msg =
    '
<p><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong><u>Product
  Notification</u></strong></font></p>
<p><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Dear Sir/Madam</font></p>
<p><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>&quot;' . stripslashes($product['item']) . '&quot;</strong>
  is available from <strong>' . $product['available_from'] . '</strong> at arcooda.com</font></p>
<p><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Please visit
  the links below for more information.</font></p>
<ul>
  <li><a href="' . $product_link . '" target="_blank"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">' . $product_link . '</font></a></li>
  <li><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="https://www.arcooda.com/" target="_blank">https://www.arcooda.com/</a></font></li>
</ul>
<p><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Kind regards </font></p>
<p><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Arcooda Staff</font></p>
	';

    include __DIR__ . '/product_notify_compose.inc.php';
}

if ('' != $action && 'send' == $action && !locked($table, $id)) {
    sendNotification();
} else {
    compose();
}

include_once __DIR__ . '/footer.inc.php';
