<?php
/***********************************************************************
 ** Title.........:
 ** Version.......:
 ** Author........:    Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
 ** Filename......:
 ** Last changed..:
 ** Notes.........:
 ***********************************************************************/

include_once __DIR__ . '/uploader.php';
include_once __DIR__ . '/options.php';

$attachment_files = filelist();

/**
 * @param int $id
 *
 * @return array{subject:string, html_message:string, text_message:string}
 */
function get_archived_mailout($id)
{
    global $DB;
    if ($row = $DB->query_first(sprintf(
        "SELECT `subject`, `html_contents`, `text_contents` FROM `mailout_archive` WHERE `id` = %d LIMIT 1",
        $id
    ), MYSQLI_ASSOC)) {
        return [
            'subject'      => $row['subject'] ?? '',
            'html_message' => $row['html_contents'] ?? '',
            'text_message' => $row['text_contents'] ?? '',
        ];
    }
    return [
        'subject'      => '',
        'html_message' => '',
        'text_message' => '',
    ];
}

/**
 * @return string[]
 */
function get_mailout_vars()
{
    global $DB;

    $result = [];
    if ($query = $DB->query(sprintf(
        "SELECT `var`, `param` FROM `mailout_vars`"
    ))) {
        while ($row = $DB->fetch_assoc($query)) {
            $vars[$row['var']] = $row['param'];
        }
        $DB->free_result($query);
    }

    return $result;
}

/**
 * @param string[] $vars
 *
 * @return void
 */
function set_mailout_vars($vars)
{
    global $DB;

    $insert_vals = [];
    foreach ($vars as $k => $v) {
        $insert_vals[] = sprintf(
            "(%s, %s)",
            formatSQLString($k),
            formatSQLString($v)
        );
    }
    $DB->query(sprintf(
        "INSERT INTO `mailout_vars` (`var`, `param`) VALUES %s AS `new` ON DUPLICATE KEY UPDATE `param` = `new`.`param`",
        implode(', ', $insert_vals)
    ));
}

/**
 * @return void
 */
function clean_up()
{
    global $attachment_files, $attachment_path, $DB;
    //need to delete all the attchment files.
    $statement = "DELETE FROM mailout_vars";
    $DB->query($statement);
    if (count($attachment_files) > 0) {
        echo "<br />Cleaing up attachment files...";
        for ($i = 0; $i < count($attachment_files); $i++) {
            unlink($attachment_path . $attachment_files[$i]);
        }
        echo " OK<br />";
    }
    echo "Done<br />";
}

/**
 * @param mixed[][] $feature
 *
 * @return mixed[][]
 */
function sortByOrder($feature)
{
    $sortedProducts = array();
    // SORT THE FEATURED PRODUCTS AS SPECIFIED BY THE "ORDER" VALUE
    // USING A GENERAL INSERTION SORT
    $first = true;
    foreach ($feature as $product) {
        $order = $product['order'];
        if (!is_numeric($order)) {
            continue;
        }

        if (isset($product['number']) && (!is_numeric($product['number']) || intval($product['number']) < 0)) {
            continue;
        }

        if ($first) {
            $sortedProducts[0] = $product;
            $first             = false;
            continue;
        }
        for ($j = 0; $j < count($sortedProducts); $j++) {
            // special handling of last element in sorted array
            if (count($sortedProducts) - 1 == $j) {
                if ($sortedProducts[$j]['order'] < $order) {
                    $sortedProducts[$j + 1] = $product;
                } else {
                    $sortedProducts[$j + 1] = $sortedProducts[$j];
                    $sortedProducts[$j]     = $product;
                }
                break;
            }
            $min = $sortedProducts[$j]['order'];
            $max = $sortedProducts[$j + 1]['order'];
            // if the element is smaller than the first then shift all elements right
            if ($order < $min) {
                for ($k = count($sortedProducts) - 1; $k >= $j; $k--) {
                    $sortedProducts[$k + 1] = $sortedProducts[$k];
                }
                $sortedProducts[$j] = $product;
                break;
            }
            // if element is between then shift greater elements to the right
            else if ($order > $min && $order < $max) {
                for ($k = count($sortedProducts) - 1; $k > $j; $k--) {
                    $sortedProducts[$k + 1] = $sortedProducts[$k];
                }
                $sortedProducts[$j + 1] = $product;
                break;
            }
        }
    }
    return $sortedProducts;
}

/**
 * @return void
 */
function generate_email()
{
    $html_message = msg_header();
    $plain_text   = msg_header(true);
    $vars         = array();

    // PROCESS TOP OF PAGE SECTION
    if (trim($_POST['top']['title']) != "" && trim($_POST['top']['description']) != "") {
        ob_start();
?>
		<table width="100%" border="0" cellspacing="0" cellpadding="5"><tr><td bgcolor="#0000FF"><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td nowrap="nowrap"><font size="+1" color="#FFFFFF"><strong><?php echo nl2br(stripslashes($_POST['top']['title'])) ?></strong></font></td></tr></table></td></tr></table>
		<table border="0" cellspacing="0" cellpadding="1" align="center" bgcolor="#000099" width="100%"><tr><td><table border="0" cellspacing="0" cellpadding="15" align="center" bgcolor="#FFFFFF" width="100%"><tr><td><table border="0" cellspacing="1" cellpadding="5" align="center">
<?php
        $relImage = substr($_POST['top']['image'], strpos($_POST['top']['image'], "/img_coe/"));
        $imageTag = '';
        $absPath  = "https://www.coinopexpress.com" . $relImage;
        if (is_file($_POST['top']['image'])) {
            $w = 200;
            $h = 200;
            if (preg_match('/_sml/', $_POST['top']['image'])) {
                $w = 96;
                $h = 96;
            } elseif (preg_match('/_med/', $_POST['top']['image'])) {
                $w = 140;
                $h = 140;
            }
            $imageTag = "<img src=\"$absPath\" width=\"$w\" height=\"$h\" border=\"0\" />";
        }
?>
			<tr>
				<td width="<?= ("" != $imageTag) ? "20" : "0" ?>%" align="center"><?= $imageTag ?></td>
				<td width="<?= ("" != $imageTag) ? "80" : "100" ?>%" valign="top"><?= nl2br(stripslashes($_POST['top']['description'])) ?></td>
			</tr>
		</table></td></tr></table></td></tr></table>
		<br />
<?php
$html_message .= ob_get_clean();
    }
    $sortedSections = sortByOrder($_POST['section']);
    for ($i = 0; $i < count($sortedSections); $i++) {
        if ("featured" == $sortedSections[$i]['type']) {
            $pid = intval($sortedSections[$i]['id']);
            if ($pid > 0) {
                if ("SUMMARY" == $sortedSections[$i]['display']) {
                    $idList = "$pid";
                    while ("featured" == $sortedSections[$i + 1]['type'] && "SUMMARY" == $sortedSections[$i + 1]['display']) {
                        $idList .= "," . intval($sortedSections[$i + 1]['id']);
                        $i++;
                    }
                    $params = array('ids' => $idList, 'view' => 'summary');
                } else {
                    $params = array('ids' => $pid, 'view' => 'detail');
                }

                $html_message .= get_auto_contents('featured_products', $params);
                $plain_text .= get_auto_contents('featured_products', $params, true);
            }
        } elseif ("latest_products" == $sortedSections[$i]['type']) {
            $max_products = intval($sortedSections[$i]['number']);
            $category     = intval($sortedSections[$i]['category_id']);
            $view         = strtolower($sortedSections[$i]['display']);
            $params       = array('x' => $max_products, 'parent' => $category, 'view' => $view);
            $html_message .= get_auto_contents('latest_products', $params);
            $plain_text .= get_auto_contents('latest_products', $params, true);
        } elseif ("auto" == $sortedSections[$i]['type']) {
            $params   = array("x" => $sortedSections[$i]['number']);
            $category = $sortedSections[$i]['category'];
            $html_message .= get_auto_contents($category, $params);
            $plain_text .= get_auto_contents($category, $params, true);
        }
    }

    $html_message .= msg_footer();
    $plain_text .= msg_footer(true);

    $vars['subject']      = $_POST['subject'];
    $vars['files']        = fileList_text();
    $vars['html_message'] = $html_message;
    $vars['text_message'] = $plain_text;
    $vars['num_sent']     = '0';
    set_mailout_vars($vars);
}

/**
 * @return array{title:string, description:string, image:string}
 */
function addTopSection()
{
    $mailVars = get_mailout_vars();
    $imgPath  = $mailVars['topImagePath'];
    $fileName = "top_image";
    if ("" == $imgPath) {
        // VERIFY AND CREATE IMAGES AND INSERT PATH INTO THE DB
        $source_file = $_POST['top']['image'];
        $imgDest     = $_SERVER['DOCUMENT_ROOT'] . '/img_coe/mailout/';
        if (file_exists($source_file) && is_file($source_file)) {
            $img_file = process_image($source_file, $fileName, 200, 200, $imgDest);
            if ($img_file) {
                $t   = explode(".", $img_file);
                $ext = $t[1];
                if ('s' == $_POST['top']['imageSize']) {
                    $imgName = $fileName . "_sml." . $ext;
                } else if ('m' == $_POST['top']['imageSize']) {
                    $imgName = $fileName . "_med." . $ext;
                } else {
                    $imgName = $fileName . "." . $ext;
                }

                $imgPath             = $imgDest . $imgName;
                $arr['topImagePath'] = $imgPath;
                set_mailout_vars($arr);
            }
        }
    } else {
        // CHANGE THE IMAGE NAME IN THE DB TO THE CURRENTLY SELECTED IMAGE SIZE
        $ext  = substr($imgPath, strpos($imgPath, "."));
        $path = substr($imgPath, 0, strrpos($imgPath, "/") + 1);
        if ('s' == $_POST['top']['imageSize']) {
            $imgPath = $path . $fileName . "_sml" . $ext;
        } else if ('m' == $_POST['top']['imageSize']) {
            $imgPath = $path . $fileName . "_med" . $ext;
        } else {
            $imgPath = $path . $fileName . $ext;
        }

    }
    $description = preg_replace('/\\n/', '<br />', $_POST['top']['description']);
    $temp        = array('title' => $_POST['top']['title'], 'description' => $description, 'image' => $imgPath);
    return $temp;
}
