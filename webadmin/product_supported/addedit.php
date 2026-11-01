<style>
	.td-style{
		width: 25%;
    text-align: right;
    padding: 10px;
	}

	div#sectionMeta table {
		text-align: left;
		width: 95%;
		margin-left: auto;
		margin-right: auto;
		border-collapse: collapse;
		border: 1px #CCCCCC solid;
	}
	div#sectionMeta td.left_title {
		text-align: right;
		background-color: #EEEEE0;
		color: #000000;
		font-weight: bold;
	}
	div#sectionMeta td {
		padding: 2px 1px 2px 1px;
	}
	div#sectionMeta tr {
		background-color: #FFFFFF;
		color: #000000;
		font-family: Verdana;
		font-weight: normal;
	}
	textarea#meta-desc-input{
		width: 98%;
	}
	textarea#meta-keys-input{
		width: 98%;
	}
	div#sectionMeta td a {
		color: #000000;
	}
</style>
<?php
include_once __DIR__ . '/common.inc.php';

global $DB;

include_once __DIR__ . '/header.inc.php';

if (!function_exists('supportGenericsDelete')) {
    function supportGenericsDelete($id, $table, $secondary_key = 'view_id') {
        global $DB;
        $DB->query(sprintf(
            "DELETE FROM %s  WHERE  `%s` = %d",
            $table,
            $secondary_key,
            $id
        ));
        return $DB->affected_rows();
    }
}
if (!function_exists('getProductFromdatabase')) {
    function getProductFromdatabase(){
		global $DB;
		$results=[];
		 $sql = " SELECT id,product_name from products where `section`=2 ORDER BY `id` DESC";
		if ($query = $DB->query($sql)) {
			while ($row = $DB->fetch_assoc($query)) {
				$results[] = ['name'=>$row['product_name'],'id'=>$row['id']];
			}
			$DB->free_result($query);
		}
		return $results;
	}
}

if (!function_exists('supportGenericsFromPost')) {
    function supportGenericsFromPost($posted, $table)
    {
         global $DB;
        if (!is_array($posted)) {
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
function addAndUpdateSupportedProduct($products,$supported_id){
	global $DB;
	$oldSupportedProduct = [];
	$query = $DB->query(sprintf(
	    "SELECT id  FROM %s WHERE FIND_IN_SET(%d, product_supported)",
	    '`products`',
	     $supported_id,
    ));
    while ($row = $DB->fetch_assoc($query)) {
            $oldSupportedProduct[] = $row['id'];
    }
    $DB->free_result($query);
	foreach ($products as $key => $product) {
		if(!in_array($product, $oldSupportedProduct)){
			$DB->query(sprintf(
			    "UPDATE %s SET `product_supported` = CONCAT(COALESCE(product_supported, ''), ',', %d) WHERE `id` = %d",
			    '`products`',
			    $supported_id,
			    $product
		    ));
		}
	}
	foreach ($oldSupportedProduct as $key => $product) {
		if(!in_array($product, $products)){
			$DB->query(sprintf(
			    "UPDATE %s SET `product_supported` = TRIM(BOTH ',' FROM REPLACE(CONCAT(',', product_supported, ','), CONCAT(',', %d , ','), ',')) WHERE `id` = %d",
			    '`products`',
			    $supported_id,
			    $product
		    ));
		}
	}

}
if (!function_exists('supportGenericToDatabase')) {
    function supportGenericToDatabase(&$item, $table, $primary_key = 'id') {
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
$action = "do_add";
$id = 0;
$setup_images=[];
if (isset($_GET['action']) && !empty($_GET['action'])) {
	if ($_GET['action'] == 'do_add') {
		$action = "do_add";
	} else if ($_GET['action'] == 'do_edit') {
		$action = "do_edit";

		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$id = intval($_GET['id']);
		}

	} else {
		die("Incorrect action");
	}
}

$data = array(
	'name' => '',
	'text' => '',
	'img' =>'',
	'active' => ''
);


$error = array();
if ($id > 0) {
	$data = $DB->query_first("SELECT * FROM `product_supported` WHERE `id` = " . $id);
}
if (!empty($_POST)) {
	//$data = array();

	if (isset($_POST['name']) && !empty($_POST['name'])) {
		$data['name'] = trim($_POST['name']);
	} else {
		$error['name'] = "Please enter the icon name.";
	}

	if (isset($_POST['text']) && !empty($_POST['text'])) {
		$data['text'] = trim($_POST['text']);
	} else {
		$error['text'] = "Please enter text for name.";
	}

	if (isset($_POST['active']) && !empty($_POST['active'])) {
		$data['active'] = ($_POST['active'] == 'y') ? 'y' : 'n';
	} else {
		$data['active'] = 'n';
	}
	if (isset($_POST['description']) && !empty($_POST['description'])) {
		$data['description'] = trim($_POST['description']);
	} else {
		$data['description'] = '';
	}
	if (isset($_POST['slug']) && !empty($_POST['slug'])) {
		$data['slug'] = trim($_POST['slug']);
	} else {
		$data['slug'] = '';
	}
	if (isset($_POST['data']['meta_title']) && !empty($_POST['data']['meta_title'])) {
		$data['meta_title'] = trim($_POST['data']['meta_title']);
	} else {
		$data['meta_title'] = '';
	}
	if (isset($_POST['data']['meta_description']) && !empty($_POST['data']['meta_description'])) {
		$data['meta_description'] = trim($_POST['data']['meta_description']);
	} else {
		$data['meta_description'] = '';
	}
	if (isset($_POST['data']['meta_keyword']) && !empty($_POST['data']['meta_keyword'])) {
		$data['meta_keyword'] = trim($_POST['data']['meta_keyword']);
	} else {
		$data['meta_keyword'] = '';
	}
	if (empty($error)) {
		if (isset($_FILES['img']) && !empty($_FILES['img']['name'])) {
			$name = $_FILES['img']['name'];
			$nameChunk = explode('.', $name);
			$ext = end($nameChunk);
			$ext = strtolower($ext);

			if ($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg') {
				$old_img = $data['img'];

				$name = time();

				$uploadName = $name . "." . $ext;

				$data['img'] = '/webadmin/assets/icon/image/' . $uploadName;

				$dest = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/icon/image/' . $uploadName;

				if (!move_uploaded_file($_FILES['img']['tmp_name'], $dest)) {
					$error['img'] = 'Failed to upload file.';
				}

				$destination = imagecreatetruecolor(96, 96);

				if ($ext == 'jpg' || $ext == 'jpeg') {
					$source = imagecreatefromjpeg($dest);
				} else {
					$source = imagecreatefrompng($dest);
				}

				list($width, $height) = getimagesize($dest);

				if ($ext == 'png') {
					imagealphablending($destination, false);
					imagesavealpha($destination, true);
					$transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
					imagefilledrectangle($destination, 0, 0, 96, 96, $transparent);
				}

				imagecopyresampled($destination, $source, 0, 0, 0, 0, 96, 96, $width, $height);

				if ($ext == 'jpg' || $ext == 'jpeg') {
					imagejpeg($destination, $dest, 100);
				} else {
					imagepng($destination, $dest);
				}

				// Remove previouse image if edit
				/*if ($action == 'do_edit') {
					unlink($_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/icon/image/' . $old_img);
				}*/
			} else {
				$error['img'] = "Invalid image format.";
			}
		} else {
			if ($action == 'do_add') {
				$error['img'] = "Please choose image for icon.";
			}
		}
	}

	if (empty($error)) {
		if ($action == "do_add") {
			$DB->query(
				sprintf(
					"INSERT INTO `product_supported` (`name`, `text`, `img`, `description`, `active`, `slug`,`meta_title`,`meta_description`,`meta_keyword`) VALUES (%s, %s, %s, %s, %s, %s ,%s,%s,%s)",
					$DB->escape($data['name']),
					$DB->escape($data['text']),
					$DB->escape($data['img']),
					$DB->escape($data['description']),
					$DB->escape($data['active']),
					$DB->escape($data['slug']),
					$DB->escape($data['meta_title']),
					$DB->escape($data['meta_description']),
					$DB->escape($data['meta_keyword'])
				)
			);
			$id = $DB->insert_id();
			if(isset($_POST['products']) && count($_POST['products']) > 0){
	        	addAndUpdateSupportedProduct($_POST['products'],$id);
	        }
			echo "<script>window.location.href='/webadmin/product_supported/'</script>";
		}else {
			$DB->query(
				sprintf(
					"UPDATE `product_supported` SET `name` = %s, `text` = %s, `img` = %s, `description` = %s, `active` = %s, `slug` = %s ,`meta_title` = %s , `meta_description` = %s , `meta_keyword` = %s WHERE `id` = %d LIMIT 1",
					$DB->escape($data['name']),
					$DB->escape($data['text']),
					$DB->escape($data['img']),
					$DB->escape($data['description']),
					$DB->escape($data['active']),
					$DB->escape($data['slug']),
					$DB->escape($data['meta_title']),
					$DB->escape($data['meta_description']),
					$DB->escape($data['meta_keyword']),
					$id
				)
			);
		}
		 if (array_key_exists('item_images_deleted_val', $_POST)) {
            foreach (preg_split('/[^0-9]+/', $_POST['item_images_deleted_val']) as $v) {
                if ($v) {
                          supportGenericsDelete($v,'`product_supported_images`','id');
                }
            }
        }
        if(isset($posted['product_image']) && count($posted['product_image'])){
            foreach ($posted['product_image'] as $key => $image) {
                $image['supported_id']=$id;
                $image['file_orig']=$image['name'];
                $images = supportGenericsFromPost($image,'`product_supported_images`');
                supportGenericToDatabase($images,'`product_supported_images`');
            }
        }
        if(isset($_POST['products']) && count($_POST['products']) > 0){
        	addAndUpdateSupportedProduct($_POST['products'],$id);
        }
	}
}
$supported_product = [];
if ($id > 0) {
	$query = $DB->query(sprintf(
		    "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s  Where `%s` = %d  %s",
		    '`product_supported_images`',
		    'supported_id',
		     $id,
		    'ORDER BY `rank` ASC'
        ));
	 while ($row = $DB->fetch_assoc($query)) {
	        if (is_null($primary_key)) {
	            $setup_images[] = $row;
	        } 
	    }
	    $DB->free_result($query);
	$query = $DB->query(sprintf(
	    "SELECT id  FROM %s WHERE FIND_IN_SET(%d, product_supported)",
	    '`products`',
	     $id,
    ));
    while ($row = $DB->fetch_assoc($query)) {
            $supported_product[] = $row['id'];
    }
    $DB->free_result($query);
}
?>
<!-- <script src="https://cdn.ckeditor.com/4.13.1/standard/ckeditor.js"></script>
 --><script type="text/javascript">
$('body').on('change','.supported_text',function(){
	var name=$(this).val().toLowerCase();
	name = name.replace(/\s+/g, "-");
	setValue('slug',name);
})
function link_insert(name) {
    var theElem = document.getElementsByName(name);
    if (theElem.length == 0) return;
    theElem = theElem[0];
    if (theElem.selectionStart != null && theElem.selectionEnd != null) {
        var a = theElem.selectionStart;
        var b = theElem.selectionEnd;
        theElem.value = theElem.value.substring(0, a) + '<a href="http://www.WEBSITE" target="_blank">' + theElem.value.substring(a, b) + '</a>' + theElem.value.substring(b);
        theElem.selectionStart = a + 20;
        theElem.selectionEnd = a + 27;
        theElem.focus();
    }
    else {
        var repltext = null;
        var seltext = (document.all) ? document.selection.createRange() : document.getSelection();
        var selit = (document.all) ? document.selection.createRange().text : document.getSelection();
        if (seltext != null && selit.length >= 1)
            seltext.text = '<a href="http://www.WEBSITE" target="_blank">' + seltext.text + '</a>';
        else
            theElem.value += '<a href="http://www.WEBSITE" target="_blank">LINK</a>';
    }
}
function generic_insert(name, tag) {
    var theElem = document.getElementsByName(name);
    if (theElem.length == 0) return;
    theElem = theElem[0];
    if (theElem.selectionStart != null && theElem.selectionEnd != null) {
        var a = theElem.selectionStart;
        var b = theElem.selectionEnd;
        theElem.value = theElem.value.substring(0, a) + '<' + tag + '>' + theElem.value.substring(a, b) + '</' + tag + '>' + theElem.value.substring(b);
        theElem.selectionStart = a + 2 + tag.length;
        theElem.selectionEnd = 2 + tag.length + b;
        theElem.focus();
    }
    else {
        var repltext = null;
        var seltext = (document.all) ? document.selection.createRange() : document.getSelection();
        var selit = (document.all) ? document.selection.createRange().text : document.getSelection();
        if (seltext != null && selit.length >= 1)
            seltext.text = '<' + tag + '>' + seltext.text + '</' + tag + '>';
        else
            theElem.value += '<' + tag + '>BOLDTEXT</' + tag + '>';
    }
}
//<![CDATA[
/*$(function() {
			content_editor = CKEDITOR.replace('item_content', {
			height:300,
			width:600,

			language : 'en',
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
			bodyId : "bd"
		});

});*/
//]]>
</script>
<br>

<form action="?action=<?= $action ?>&id=<?= $id ?>" method="post" enctype="multipart/form-data">
	<center>
		<table border="0" cellpadding="2" style="width:100%;">
			<tr>
				<td class="td-style"><b>Supported product Name</b></td>
				<td>
					<input type="text" name="name" value="<?= $data['name'] ?>" size="20" style="width: 400px;" /> 
					<?php if (isset($error['name'])) : ?>
						<br><small style="color: red;"><?= $error['name'] ?></small>
					<?php endif; ?>
				</td>
			</tr>
			<tr><td colspan="2"><br/></td></tr>

			<tr>
				<td class="td-style"><b>Online Support Text</b></td>
				<td>
					<input type="text" name="text" value="<?= $data['text'] ?>" size="20" style="width: 400px;" class="supported_text"/>
					<?php if (isset($error['text'])) : ?>
						<br><small style="color: red;"><?= $error['text'] ?></small>
					<?php endif; ?>
				</td>
			</tr>
			<tr><td colspan="2"><br/></td></tr>

			<tr>
				<td class="td-style"><b>Website Url</b></td>
				<td>
					<?php 
					$isSecure = false;
						if (isset($_SERVER['HTTPS']) && 'on' == $_SERVER['HTTPS']) {
						    $isSecure = true;
						} elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' == $_SERVER['HTTP_X_FORWARDED_PROTO'] || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && 'on' == $_SERVER['HTTP_X_FORWARDED_SSL']) {
						    $isSecure = true;
						}
						$REQUEST_PROTOCOL = $isSecure ? 'https' : 'http';
						$url=$REQUEST_PROTOCOL."://" . $_SERVER['SERVER_NAME'];
					?>
					<?= $url.'/arcade-play/' ?>
					<input type="text" name="slug" value="<?= $data['slug'] ?>" size="20" style="width: 200px;"/>
				</td>
			</tr>
			<tr><td colspan="2"><br/></td></tr>

			<tr><td colspan="2"><br/></td></tr>

			<tr>
				<td class="td-style"><b>Image</b></td>
				<td>
				<div>
					<?php if($action == 'do_edit') : ?>
	
						<img src="<?= $data['img'] ?>" width="96" /> <br/>
											
					<?php endif; ?>
					<span style="color:red"><i>Image to be 16*9 minimum 200 size (prefer over 500)</i></span>
				</div>
					<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
					<input type="file" name="img" />
					<?php if (isset($error['img'])) : ?>
						<br><small style="color: red;"><?= $error['img'] ?></small>
					<?php endif; ?>

				</td>
			</tr>

			<tr><td colspan="2"><br/></td></tr>
			<tr>
				<td class="td-style"><b>Description</b></td>
				<td>
					<textarea id="item_content" name="description" style="width:90%;height:auto;min-height: 192px;"><?= $data['description'] ?></textarea>
					<br />
					<input type="button" value="Insert Link" onclick="javascript:link_insert('description');" />
					<input type="button" value="Bold" onclick="javascript:generic_insert('description','b');" />
					<input type="button" value="Italics" onclick="javascript:generic_insert('description','i');" />
					<input type="button" value="Underline" onclick="javascript:generic_insert('description','u');" />
					<input style="width:96px;" type="button" value="Bullet Point" onclick="generic_insert('description','li');" />
				</td>
			</tr>
			
			<tr><td colspan="2"><br/></td></tr>
			<tr><td colspan="2">
			 <?php include "setup_screenshot.php"; ?>
			</td></tr>
			<tr><td colspan="2"><br/></td></tr>
			<tr><td colspan="2">
			 <?php if($data['id'] > 0 ) include "meta_tags.tpl.php"; ?>
			</td></tr>
			<tr><td colspan="2"><br/></td></tr>
			<tr><td colspan="2">
			 <?php include "product_supported.tpl.php"; ?>
			</td></tr>
			<tr><td colspan="2"><br/></td></tr>
			<tr>
				<td class="td-style"><b>Active Online</b></td>
				<td>
					<select name="active" style="width:50%;text-align:center;<?= ($data['active'] == 'n') ? 'background: #FF0000;':'background: #00FF00;'?>" onchange="this.style.backgroundColor=((this.value=='n')?'#FF0000':'#00FF00');">
						<option value="y" <?= ($data['active'] == 'y') ? 'selected' : '' ?>>Yes</option>
						<option value="n" <?= ($data['active'] == 'n') ? 'selected' : '' ?>>No</option>
					</select>
					<?php if (isset($error['active'])) : ?>
						<br><small style="color: red;"><?= $error['active'] ?></small>
					<?php endif; ?>
				</td>
			</tr>

			<tr><td colspan="2"><br/></td></tr>

			<tr>
				<td align="center" colspan="2">
					<input type="submit" value="Save" style="width:20%" />
				</td>
			</tr>
		</table>
	</center>
</form>

<script type="text/javascript" src="//code.jquery.com/jquery-1.9.0.min.js"></script>

<?php
include_once __DIR__ . '/footer.inc.php';
