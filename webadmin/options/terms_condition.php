

<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';
?>
<script src="https://cdn.ckeditor.com/4.13.1/standard/ckeditor.js"></script>
<script type="text/javascript">
//<![CDATA[
$(function() {
			
			content_editor = CKEDITOR.replaceAll('item_content', {
			height:100,
			width:600,

			language : 'en',
			format_tags: 'p;h1;h2;h3;h4;h5;h6;pre;address;div',
			baseHref : <?php jecho($_GLOBALS["site_settings"]["home_uri"])?>,
			allowedContent: true,
			format_h2 : {
				element : "h2",
				attributes : {
					style : "background-color:#eee; border:1px solid #ccc; padding:5px 10px; color:#080; font-size:24px; font-weight:bold;"
				}
			},
			contentsCss : "https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css",
			bodyClass : "page_body",
			bodyId : "bd",
		});



});
//]]>
</script>
<table border="0" cellpadding="2" cellspacing="2" width="100%">
	<tr>
		<td class="pageMenu" width="20%">
			<div align="center">  Terms and Condition</div>
		</td>
		<td class="pageMenu" width="80%"><div align="right"><a href="?action=view" target="_blank">View</a></div></td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
</table>

<div id="optcontent">
<div><h2 style="text-align:center;">Update Terms And Condition</h2></div>
<div align="center" width="95%">
<?php

$user_details = $DB->query_first("SELECT * FROM `highwaygroup`.`users` WHERE `user` = '" . $_SERVER['REMOTE_USER'] . "'", MYSQLI_ASSOC);
$messages     = array();
$time_now     = time();
$posted       = array_merge($_GET, $_POST);
if (!function_exists('termsGenericsFromPost')) {
    function termsGenericsFromPost($posted, $table)
    {
         global $DB;
        if(!is_array($posted)) {
            return array();
        }
        $result = array();
        $fields = $DB->get_table_columns($table);
        foreach ($posted as $k1 => $v1) {
                if (array_key_exists($k1,$fields)) {
                    if($v1 || $v1==0){
                        $result[$k1] = $v1;
                    }    
                }
        }
        return $result;
    }
}
if (!function_exists('termsGenericToDatabase')) {
    function termsGenericToDatabase(&$item, $table, $primary_key = 'id') {
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
                   $set_vals['updated_at'] = "`updated_at` = NOW()";
                $DB->query(sprintf(
                    "UPDATE %s SET %s WHERE `%s` = %d LIMIT 1",
                    $table,
                    implode(', ', $set_vals),
                    $primary_key,
                    $id
                ));
            }
        } else {
            $item['created_by'] = $_GLOBALS['staff']['id'];
            $item['created_at'] = formatDateTime();
            $insert_vals = array();
            foreach ($fields as $k => $v) {
                if (array_key_exists($k, $item)) {
                    if (is_array($item[$k])) {
                        $insert_vals[$k] = $DB->escape(implode(",", $item[$k]));
                    } elseif (isset($item[$k])) {
                        $insert_vals[$k] = $DB->escape($item[$k]);
                    } else {
                        $insert_vals[$k] = "NULL";
                    }
                }
            }
            if (count($insert_vals)) {
                $DB->query(sprintf(
                    "INSERT INTO %s (`%s`) VALUES (%s)",
                    $table,
                    implode('`, `', array_keys($insert_vals)),
                    implode(", ", $insert_vals)
                ));

                $id = $DB->insert_id();
            }
        }
        return $id;
    }
}
// Handle action request
if ('do_edit' == $posted['action']) {
    $action = 'do_edit';
   	$postdata = termsGenericsFromPost($posted['item'],'`terms_condition_checkout`');
    termsGenericToDatabase($postdata,'`terms_condition_checkout`');
       
    $posted['action'] = 'edit';
}

if (count($messages) > 0) {
    foreach ($messages as $msg) {
        echo $msg . "<br />\n";
    }
}
if ('view' == $posted['action']) {
    $action = 'view';
} else {
    $action = 'edit';
}

switch ($action) {
    case 'view':
    ?>
		<table class="wide" style="border-collapse:collapse;border-style:none;">

			<tbody>
				<tr>
					<td style="font-size:11px;color:#000;vertical-align:top;">

						<div class="checkout_body">

							<div class="default_window">
								<div class="window_border">

									<div class="window_client">

										<h5>Please Review your order before confirming, as the goods will be shipped based on your selection, as shown in the right-hand menu.</h5>
										<h3>Shipping/Insurance :</h3>
										<ul>
											
											<li style="color:#f00;">Ship Insurance Not Available : You have selected Shipping without any insurance. Highway Entertainment shall not be liable for any loss or damage to the goods during transit.</li>

												<li>
													You have selected Shipping <span style="color:green;">with insurance : </span>. If you do not receive goods within the estimated Transit time quoted during the Checkout, please contact us immediately, so we can contact the freight company and get details regarding the status of the delivery. If you receive damaged goods, please retain all relevant documentation so that an insurance claim can be made against the freight forwarders and contact us immediately as an insurance claim has to be put ASAP, usually within 7 days of the date the goods were delivered.
												</li>
										</ul>

										<h3>Taxes :</h3>
										<ul>
											<li>Please Note: The prices mentioned in the checkout do not include any import taxes or local charges in your country. Please contact your local government for any charges that maybe applicable to you.</li>
										</ul>

										<h3>Warranty :</h3>
										<ul>
											<li>We offer a back to base warranty (you need to return the goods back to our warehouse) and if product cannot be fixed full replacement will be provided. We will pay for freight back to you once the goods have been repaired.</li>
											<li>
												Warranty for individual products may vary:
												<ul>
													[PRODUCT]
												</ul>
											</li>
										</ul>

										<h3>Returns/Exchange :</h3>
										<ul>
											<li>Exchange/Refunds: We want to make sure you are fully satisfied with the product and accept returns/refunds but only via a RMA (Return Materials Authorisation number). Please contact our sales team to receive this before returning any product.</li>
											<li>Freight costs (to and from you) are non refundable.</li>
											<li>There is no restocking fee.</li>
											<li>Refunds and exchanged products need to be returned in their original brand new condition, and the replacement value may be affected if the product is damaged, missing parts or modified.</li>
										</ul>

									</div>
								</div>
							</div>
						</div>


					</td>
				</tr>
			</tbody>
		</table>
    <?php
break;

    case 'edit':
        $row = $DB->query_first("SELECT * FROM terms_condition_checkout WHERE id=1 LIMIT 1");
?>
<form action="?action=do_edit" method="post">
	<table class="wide">
		<tr>
			<td class="left_title"> Header Text</td>
			<td>
				<input type="hidden" value="1" name="item[id]">
				<textarea name="item[header_text]" rows="5" class="item_content"><?= $row['header_text']?></textarea>
			</td>
		</tr>
		<tr>
			<td class="left_title">Text For Insurance</td>
			<td>
				<textarea name="item[with_insurance]" rows="5" class="item_content"><?= $row['with_insurance']?></textarea>
			</td>
		</tr>
		<tr>
			<td class="left_title">Text Without Insurance</td>
			<td>
				<textarea name="item[without_insurance]" rows="5" class="item_content"><?= $row['without_insurance']?></textarea>
			</td>
		</tr>
		<tr>
			<td class="left_title">Text For Tex Section</td>
			<td>
				<textarea name="item[texes]" rows="5" class="item_content"><?= $row['texes']?></textarea>
			</td>
		</tr>
		<tr>
			<td class="left_title">Text For Warranty Section</td>
			<td>
				<textarea name="item[warranty]" rows="5" class="item_content"><?= $row['warranty']?></textarea>
			</td>
		</tr>
		<tr>
			<td class="left_title">Text For Returns/Exchange</td>
			<td>
				<textarea name="item[return_policy]" rows="5" class="item_content"><?= $row['return_policy']?></textarea>
			</td>
		</tr>
	</table>
	<p align="center">
		<input type="submit" value="Save Changes" />
	</p>
</form>
<?php
break;
}
?>
</div>
<!-- footer.inc.php -->
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/webadmin/footer.inc.php";
?>