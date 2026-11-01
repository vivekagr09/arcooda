<?php
/***********************************************************************
 ** Title.........:    Product Details Page
 ** Version.......:    1.1
 ** Author........:    Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
 ** Filename......:    product_details.php
 ** Last changed..:    7/19/02
 ** Notes.........:
 ***********************************************************************/

//select the product details from product_id
$statement = "SELECT
					p.*, UNIX_TIMESTAMP(p.price_changed) AS date_changed,
					UNIX_TIMESTAMP(p.date_available) AS time_available,
					t.name AS type_name,
					cl.name AS location,
					a.availability,
					m.manufacturer as product_manufacturer,
					tp.name as section,
					pd.`condition` as product_condition,
					tp.description as section_description,
					t.generic_notes,
					pdim.width as dim_width,
					pdim.height as dim_height,
					pdim.depth as dim_depth
				FROM products p
				LEFT JOIN product_types t ON p.type=t.id
				LEFT JOIN product_types tp ON t.parent=tp.id
				LEFT JOIN product_country_location cl ON (p.country_location = cl.id)
				LEFT JOIN product_availability a ON (p.available = a.id)
				LEFT JOIN manufacturers m ON (p.manufacturer = m.id)
				LEFT JOIN product_conditions pd ON (p.`condition` = pd.id)
				LEFT JOIN products_dimen pdim ON (pdim.id = p.id)
				WHERE p.id = '$product_id'
			";
$item                  = $DB->query_first($statement);
$item['year_released'] = lookup_year($item['year_released']);

if ($item['dim_width'] > 0) {
    $item['dimension'] = 'W' . $item['dim_width'] . ' x D' . $item['dim_depth'] . ' x H' . $item['dim_height'] . ' mm';
}

if (!is_array($item)) {
    exit();
}

//non active products, warn the admin
if ('n' == $item['active'] && !isset($preview)) {

    $time      = date('l F jS, Y, H:i:s', time());
    $user_ip   = $REMOTE_ADDR;
    $user_host = gethostbyaddr($user_ip);

    $message =
        '
		There was an request for an non-active product

		PRODUCT ID     : ' . $item['id'] . '
		PRODUCT NAME   : ' . $item['product_name'] . '
		USER ADDRESS   : ' . $user_host . '
		TIME           : ' . $time . '
		WEBSITE        : ' . $_SERVER['HTTP_HOST'] . '

		--END--

		';
    //mail("admin@coinopexpress.com","Non-active products ".$item['id'].":".$item['product_name'],$message);
    //header("Location: /");
}

//get the product details
$product = product_details($item);

//make the html meta tag data
$htmldata = $item['product_name'] . ' ';
$htmldata .= $item['type_name'] . ' ';
$htmldata .= $item['long_desc'] . ' ';
$htmldata .= $item['short_desc'] . ' ';
$htmldata .= $item['notes'] . ' ';
$htmldata .= cal_price($item['sales_price'], $item['sales_currency']) . ' ';
$htmldata .= $item['product_condition'] . ' ';
$htmldata .= $item['dimension'] . ' ';
$htmldata .= $item['weight'];

$sect_url = "/products/" . trim($params[0]);
$type_url = $sect_url . '/' . trim($params[1]);
$prod_url = $type_url . '/' . trim($params[2]);

//create the keywords and description
$results     = desc_keys($htmldata);
$keywords    = $results[keys]; //meta tag keywords
$description = trim($results[desc]) . '.'; //meta tag description
//$html_title = $product['name'].' - '.$item['type_name'].' - '.$item['section_description'];//html title
$html_title = "<a href=\"$prod_url.html\" class=\"htlink\">" . $product['name'] . "</a> - <a href=\"$type_url.html\" class=\"htlink\">" . $item['type_name'] . "</a> - <a href=\"$sect_url.html\" class=\"htlink\">" . $item['section_description'] . "</a>"; //html title

include 'header.inc.php';

include 'products_header.inc.php';

$hide_details = false;

if (isset($_GET['d'])) {
    $hide_details = true
    ;
}
?>
<!-- product <?php echo $item['id'] ?> details -->
<br />
<script language="javascript">
<!--
function openfcwindow(weight)
{
	newWin = window.open('/freight/adminShippingForm.php?w='+weight+'&DestC=US','quoteWin','width=600,height=450,resizable,scrollbars');
	newWin.focus();
}

function openhtowindow(weight)
{
	newWin = window.open('/about_us/howto_order.html','htoWin','toolbar,location,directories,status,menubar,scrollbars,copyhistory,resizable');
	newWin.focus();
}
//-->
</script>
<table width="90%" border="0" cellspacing="1" cellpadding="2" align="center">
  <tr>
    <td colspan="2" bgcolor="#EEEEEE"> <table width="100%">
        <tr>
          <td>&nbsp; <font size="5"><b><?php echo $product['name'] ?></b></font>
          </td>
<?php
if ($item['time_available'] > time()) {
?>
          <td align="right"><a href="javascript:;" onclick="MM_openBrWindow('/notify_available.html?pid=<?php echo $item['id'] ?>','notify','width=400,height=300')">Notify
            Me When This Product Is Available</a></td>
<?php
}
?>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td width="1%" valign="top">&nbsp;<?php echo $product['large_img'] ?>&nbsp;</td>
    <td width="99%" valign="top">
<?php
if (!$hide_details) {
?>
		<table width="100%" border="0" cellspacing="1" cellpadding="2">
        <tr>
          <td colspan="3">&nbsp;</td>
        </tr>
<?php
if ('y' == $item['active'] || isset($preview)) {
?>
        <tr valign="top">
          <td width="20%" nowrap valign="top"><b>Unit Price:</b></td>
          <td width="80%" colspan="2"><font color="<?php echo $product['price_changed'] ? "#FF0000" : "#000000" ?> "><?php echo flux_prices($item); ?></font></td>
		</tr>
<?php
if (intval($item['min_order']) <= 0) {
?>
        <tr valign="top">
          <td width="20%" nowrap valign="top"><b>Quantity:</b></td>
          <td width="20%"><?php echo $product['qty'] ?></td>
		  <td width="60%">
<?php
$temp      = $item;
            $temp[qty] = 1;
//              showBuyOption($temp);
?>
		  </td>
        </tr>
<?php
}
    } else {
?>
        <tr valign="top">
          <td width="20%" nowrap valign="top">&nbsp;</td>
          <td width="80%" colspan="2"><IMG SRC="/webadmin/sold_out.gif" WIDTH="128" HEIGHT="70" BORDER=0 ALT="SOLD OUT"><br><br></td>
        </tr>
<?php
}
    if (intval($item['min_order']) > 0) {
?>
        <tr valign="top">
          <td width="20%" nowrap valign="top"><b>Min. Order:</b></td>
          <td width="20%"><?php echo $item['min_order'] ?></td>
		  <td width="60%">
<?php
$temp      = $item;
        $temp[qty] = intval($item['min_order']);
//              showBuyOption($temp);
?>
		  </td>
        </tr>
<?php
}
    if (doubleval($item['sample_price']) > 0) {
?>
        <tr valign="top">
          <td width="20%" nowrap valign="top"><b>Sample Price:</b></td>
          <td width="80%" colspan="2"><?php echo $product['sample_price'] ?></td>
        </tr>
<?php
}
    if (intval($item['sample_qty']) > 0) {
?>
        <tr valign="top">
          <td width="20%" nowrap valign="top"><b>Sample Qty:</b></td>
          <td width="20%"><?php echo $item['sample_qty'] ?></td>
		  <td width="60%">
<?php
$temp      = $item;
        $temp[qty] = intval($item['sample_qty']);
//              showBuyOption($temp);
?>
		  </td>
        </tr>
<?php
}
    if ('machines' == $section || 'pcbs' == $section) {
        if (trim($item['availability']) != '') {
?>
        <tr valign="top">
          <td width="20%" nowrap valign="top"><b>Available:</b></td>
          <td width="80%" colspan="2"><?php echo $item['availability'] ?></td>
        </tr>
<?php
}
        if (trim($item['product_condition']) != '') {
?>
        <tr valign="top">
          <td width="20%" nowrap valign="top"><b>Condition:</b></td>
          <td width="80%" colspan="2"><?php echo $item['product_condition'] ?></td>
        </tr>
<?php
}
?>
	<tr><td colspan="2" align="center" nowrap="nowrap"><br />
<?php
if (('pcbs' == $section) && ("Hong Kong" == $item['location'])):
            // Twiddle the weight value to deal with min_order and incremental weight
            if ($item['min_order'] <= 1) {
                // Just show weight for one item
                $weight = $item['weight'];
            } elseif ($item['weight_inc'] <= 0) {
            // No weight increment, multiple items all cost the same to ship
            $weight = $item['weight'] * $item['min_order'];
        } else {
            // min order and weight increment exist
            $weight = $item['weight'] + ($item['weight_inc'] * ($item['min_order'] - 1));
        }
?>
		<a href="#" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('fc','','/webadmin/orange_freight.gif',1)" onclick="javascript: openfcwindow(<?php echo floatval($weight) ?>);" ><img name="fc" border="0" src="/webadmin/green_freight.gif" width="81" height="22"></a>
<?php
endif;
    } else {
        echo "<tr><td colspan=\"2\" align=\"center\" nowrap=\"nowrap\"><br />";
    }
?>
		&nbsp;&nbsp;<a href="#" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('hto','','/webadmin/orange_order.gif',1)" onclick="javascript: openhtowindow();"><img name="hto" border="0" src="/webadmin/green_order.gif" width="81" height="22"></a>
	</td></tr>
      </table>
<?php
}
//if hide_details
else {
        echo '&nbsp;';
    }
?>
		  </td>
	  </tr>
	</table>
	<br />
<?php
    if (trim($item['long_desc']) != '') {
?>
	<!-- about the product -->
	<!-- header -->
	<table border="0" width="90%" cellspacing="2" cellpadding="2" align="center">
	  <tr valign="top">
	    <td class="small_detail" colspan="4"> <table width="100%" border="0" cellspacing="0" cellpadding="0">
	        <tr>
	          <td width="20%" nowrap bgcolor="#000099">
			  	<font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>&nbsp;&nbsp;&nbsp;About
	            <?php echo $product['name'] ?>&nbsp;&nbsp;&nbsp;</b></font></td>
	          <td width="80%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
	        </tr>
	        <tr bgcolor="#000099">
	          <td colspan="2"><img src="/_.gif" width="1" height="1" /></td>
	        </tr>
	      </table></td>
	  </tr>
	</table>
	<!-- //header -->
	<!-- details -->
	<br>
	<table width="85%" border="0" cellspacing="15" cellpadding="2" align="center">
	  <tr>
	    <td><?php echo nl2br($item['long_desc']) ?></td>
	  </tr>
	</table>
	<br />
	<!-- //details -->
	<!-- //about the product -->
	<br />
<?php
    }
    if (trim($item['product_details']) != '' && !$hide_details) {?>
	<!-- product details -->
	<!-- header -->
	<table border="0" width="90%" cellspacing="2" cellpadding="2" align="center">
	  <tr valign="top">
	    <td class="small_detail" colspan="4"> <table width="100%" border="0" cellspacing="2" cellpadding="0">
	        <tr>
	          <td> <table width="100%" border="0" cellspacing="0" cellpadding="0">
	              <tr>
	                <td width="20%" nowrap bgcolor="#000099"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>&nbsp;&nbsp;&nbsp;Product
	                  Details&nbsp;&nbsp;&nbsp;</b></font></td>
	                <td width="80%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
	              </tr>
	              <tr bgcolor="#000099">
	                <td colspan="2"><img src="/_.gif" width="1" height="1" /></td>
	              </tr>
	            </table></td>
	        </tr>
	      </table></td>
	  </tr>
	</table>
	<!-- //header -->
	<!-- details -->
	<br>
	<table width="85%" border="0" cellspacing="15" cellpadding="2" align="center">
	  <tr>
	    <td>
	<?php format_list($item['product_details']);?>
		</td>
	  </tr>
	</table>
	<br />
	<!-- //details -->
	<!-- //product details -->
	<?php }?>
	<?php if (isset($item['generic_notes']) && (trim($item['generic_notes']) != "")): ?>
	<!-- generic notes -->
	<!-- header -->
	<table border="0" width="90%" cellspacing="2" cellpadding="2" align="center">
	  <tr valign="top">
	    <td class="small_detail" colspan="4"> <table width="100%" border="0" cellspacing="2" cellpadding="0">
	        <tr>
	          <td> <table width="100%" border="0" cellspacing="0" cellpadding="0">
	              <tr>
	                <td width="20%" nowrap bgcolor="#000099"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>&nbsp;&nbsp;&nbsp;Special Notes&nbsp;&nbsp;&nbsp;</b></font></td>
	                <td width="80%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
	              </tr>
	              <tr bgcolor="#000099">
	                <td colspan="2"><img src="/_.gif" width="1" height="1" /></td>
	              </tr>
	            </table></td>
	        </tr>
	      </table></td>
	  </tr>
	</table>
	<!-- //header -->
	<br />
	<table width="85%" border="0" cellspacing="15" cellpadding="2" align="center">
	  <tr>
	    <td>
	<?php format_list($item['generic_notes']);?>
		</td>
	  </tr>
	</table>
	<br />
	<!-- //generic notes -->
	<?php endif;?>

<?php if (!$hide_details) {?>
<!-- other details -->
<!-- header -->
<table border="0" width="90%" cellspacing="2" cellpadding="2" align="center">
  <tr valign="top">
    <td class="small_detail" colspan="4"> <table width="100%" border="0" cellspacing="2" cellpadding="0">
        <tr>
          <td> <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="20%" nowrap bgcolor="#000099"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>&nbsp;&nbsp;&nbsp;Other
                  Details&nbsp;&nbsp;&nbsp;</b></font></td>
                <td width="80%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
              </tr>
              <tr bgcolor="#000099">
                <td colspan="2"><img src="/_.gif" width="1" height="1" /></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
</table>
<!-- //header -->
<!-- others -->
<br>
<table width="85%" border="0" cellspacing="15" cellpadding="2" align="center">
  <tr>
	<td>
	  <table width="100%" border="0" cellspacing="1" cellpadding="2" align="center">
        <tr>
          <td width="20%" nowrap valign="top"><b>Product ID:</b></td>
          <td width="80%"><?php echo get_product_id($product_id, $item['section'], $item['type_name'], $item['type']) ?></td>
        </tr>
        <tr>
          <td width="20%" nowrap valign="top"><b>Item Type:</b></td>
          <td width="80%"><?php echo $item['type_name'] ?></td>
        </tr>
        <?php if (trim($item['product_manufacturer']) != '' && trim($item['product_manufacturer']) != '0') {?>
        <tr>
          <td width="20%" nowrap valign="top"><b>Manufacturer:</b></td>
          <td width="80%"><?php echo $item['product_manufacturer'] ?></td>
        </tr>
        <?php }?>
        <?php if (trim($item['location']) != '') {?>
        <tr>
          <td width="20%" nowrap valign="top"><b>Country:</b></td>
          <td width="80%"><?php echo $item['location'] ?></td>
        </tr>
        <?php }?>
<?php if ('machines' != $section && 'pcbs' != $section) {?>
        <?php if (trim($item['availability']) != '') {?>
        <tr>
          <td width="20%" nowrap valign="top"><b>Available:</b></td>
          <td width="80%"><?php echo $item['availability'] ?></td>
        </tr>
        <?php }?>
        <?php if (trim($item['product_condition']) != '') {?>
        <tr>
          <td width="20%" nowrap valign="top"><b>Condition:</b></td>
          <td width="80%"><?php echo $item['product_condition'] ?></td>
        </tr>
        <?php }?>
<?php }?>
        <?php if (trim($item['year_released']) != '') {?>
        <tr>
          <td width="20%" nowrap valign="top"><b>Year Released:</b></td>
          <td width="80%"><?php echo $item['year_released'] ?></td>
        </tr>
        <?php }?>
        <?php if (trim($item['dimension']) != '') {?>
        <tr>
          <td width="20%" nowrap valign="top"><b>Dimensions:</b></td>
          <td width="80%"><?php echo $item['dimension'] ?></td>
        </tr>
        <?php }?>
        <?php if (trim($item['weight']) != '') {?>
        <tr>
          <td width="20%" nowrap valign="top"><b>Weight:</b></td>
          <td width="80%"><?php echo $item['weight'] ?></td>
        </tr>
        <?php }?>
        <?php if (trim($item['notes']) != '') {?>
        <tr>
          <td width="20%" nowrap valign="top"><b>Notes:</b></td>
          <td width="80%"><?php echo nl2br($item['notes']) ?></td>
        </tr>
        <?php }?>
	  </table>
	</td>
  </tr>
</table>
<br>
<!-- //others -->
<!-- //other details -->
<?php
} //if not hide details ?>

<?php

//select the related products detail
$statement = "SELECT t.name as type,
					 t.description as type_desc,
					 tp.name AS section,
					 tp.description as section_desc,
					 pr.title
				FROM products_related pr, product_types t, product_types tp
				WHERE t.parent = tp.id
					AND pr.type_id = t.id
					AND pr.product_id = '$product_id'";

$result = $DB->query($statement);

//if there are related products, show them
if ($DB->num_rows($result) > 0) {

?>
<!-- related products -->
<!-- header -->
<table border="0" width="90%" cellspacing="2" cellpadding="2" align="center">
  <tr valign="top">
    <td class="small_detail" colspan="4"> <table width="100%" border="0" cellspacing="2" cellpadding="0">
        <tr>
          <td> <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="20%" nowrap bgcolor="#000099"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>&nbsp;&nbsp;&nbsp;Related Products&nbsp;&nbsp;&nbsp;</b></font></td>
                <td width="80%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
              </tr>
              <tr bgcolor="#000099">
                <td colspan="2"><img src="/_.gif" width="1" height="1" /></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
</table>
<!-- //header -->
<!-- related -->
<br>
<table width="85%" border="0" cellspacing="15" cellpadding="2" align="center">
  <tr>
    <td>
		<ul>
<?php
while ($row = $DB->fetch_array($result)) {
        $link = $base_url . 'products/' . $row['section'] . '/' . encode_section_url($row['type']) . '.html';

        echo "<li><a href=\"$link\">";
        if ('' == $row['title']) {
            echo $row['section_desc'] . ' - ' . $row['type'];
        } else {
            echo $row['title'];
        }

        echo "</a></li>\n";

    }
?>
		</ul>
	</td>
  </tr>
</table>
<!-- //related -->
<!-- //related products -->
<?php }?>
<br />
<?php
$statement = "SELECT s.*, t.type FROM product_screenshots s, screenshot_type t WHERE s.id = '$product_id' AND s.ss_type = t.id ORDER BY rank";
$ss        = $DB->query($statement);

$ss_count = $DB->num_rows($ss);
if ($ss_count > 0) {
    while ($row = $DB->fetch_array($ss)) {
        $screen_shots[] = $row;
    }

    include 'screen_shots.php';
}

$show_specials_legends = $product['price_changed'];

?>
<ul><li><b>Please Note:</b> there is minimum order of $200USD <a href="/about_us/howto_order.html">(more info)</a></li></ul>
<?php
?>
<!-- //product <?php echo $item['id'] ?> details -->
