<?php
include_once __DIR__ . '/common.inc.php';

global $DB;

include_once __DIR__ . '/header.inc.php';

$action = "do_add";
$id = 0;
if (!function_exists('crouselGenericsFromPost')) {
    function crouselGenericsFromPost($posted, $table)
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
if (!function_exists('crouselGenericToDatabase')) {
    function crouselGenericToDatabase(&$item, $table, $primary_key = 'id') {
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

if (!function_exists('crouselGenericsDelete')) {
    function crouselGenericsDelete($id, $table, $secondary_key = 'view_id') {
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
if (isset($_GET['action']) && !empty($_GET['action'])) {
	if ($_GET['action'] == 'do_add') {
		$action = "do_add";
	} else if ($_GET['action'] == 'do_edit') {
		$action = "do_edit";

		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$id = intval($_GET['id']);
		}

	}elseif($_GET['action'] == 'deactivate_ss'){
        global $DB;
        $DB->query("UPDATE `product_page_carousel_images` SET `active`='n' WHERE `id`=".$_GET['shot_id']);
        echo '<script>window.location.href="/webadmin/product_carousel/addedit.php?action=do_edit&id='.$_GET['id'].'"</script>';
    }elseif($_GET['action'] == 'activate_ss'){
        global $DB;
        $DB->query("UPDATE `product_page_carousel_images` SET `active`='y' WHERE `id`=".$_GET['shot_id']);
        echo '<script>window.location.href="/webadmin/product_carousel/addedit.php?action=do_edit&id='.$_GET['id'].'"</script>';
    }else {
		die("Incorrect action");
	}
}
$crousel_images=[];
if ($id > 0) {
	
	$item = $DB->query_first("SELECT * FROM `product_page_carousel` WHERE `id` = " . $id);
	$query = $DB->query(sprintf(
		    "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s  Where `%s` = %d  %s",
		    '`product_page_carousel_images`',
		    'crousel_id',
		     $id,
		    'ORDER BY `rank` ASC'
        ));
	 while ($row = $DB->fetch_assoc($query)) {
	        if (is_null($primary_key)) {
	            $crousel_images[] = $row;
	        } 
	    }
	    $DB->free_result($query);
}
$error = array();

if (!empty($_POST)) {
	//$data = array();

	if (isset($_POST['name']) && !empty($_POST['name'])) {
		$data['name'] = trim($_POST['name']);
	} else {
		$error['name'] = "Please enter the carousel name.";
	}

	if (isset($_POST['text']) && !empty($_POST['text'])) {
		$data['text'] = trim($_POST['text']);
	} else {
		$error['text'] = "Please enter text for carousel.";
	}

	if (isset($_POST['show_front']) && !empty($_POST['show_front'])) {
		$data['show_front'] = ($_POST['show_front'] == 'y') ? 'y' : 'n';
	} else {
		$data['show_front'] = 'n';
	}
	if (empty($error)) {
		if (isset($_FILES['img']) && !empty($_FILES['img']['name'])) {
		
		} else {
			if ($action == 'do_add') {
				$error['img'] = "Please choose image for icon.";
			}
		}
	}
	    $posted['item']['id']=$posted['id'];
	    if($posted['item']['default']=='y' && $posted['item']['status']=='y'){
	    	$DB->query("Update `product_page_carousel` set `default`='n'");
	    }else{
	    	$posted['item']['default']=='n';
	    }
        if (array_key_exists('center_image', $_FILES) && $_FILES['center_image']['error'] == UPLOAD_ERR_OK) {
            $file_ext=substr($_FILES['center_image']['name'],strrpos($file_orig,'.'));
            if(substr($file_ext,0,1)=='.') 
            $file_ext=substr($file_ext,1);
              $name = time().'_'.rand();
            $file_new  = $name . "." . $file_ext;
            $dir = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/icon/image/' ;
            if (move_uploaded_file($_FILES['center_image']['tmp_name'], $dir.$file_new)) {

                $posted['item']['center_image']=$file_new;
            }
        }
        $crousel = crouselGenericsFromPost($posted['item'],'`product_page_carousel`');
        $id =crouselGenericToDatabase($crousel,'`product_page_carousel`');
        if(count($posted['product_image']) && $posted['item']['type']=='image'){
             global $DB;
        $DB->query(
            "DELETE FROM `product_page_carousel_images`  WHERE  `crousel_id` = ".$id." AND `type`='video'"
           );
            foreach ($posted['product_image'] as $key => $image) {
                $image['crousel_id']=$id;
                $image['file_orig']=$image['name'];
                $images = crouselGenericsFromPost($image,'`product_page_carousel_images`');
                crouselGenericToDatabase($images,'`product_page_carousel_images`');
            }
        }else if(count($posted['product_video']) && $posted['item']['type']=='video'){
            global $DB;
            $DB->query(
            "DELETE FROM `product_page_carousel_images`  WHERE  `crousel_id` = ".$id." AND `type`='image'"
           );
           foreach ($posted['product_video'] as $key => $image) {
                if(isset($_FILES['video_image'])){
                if(isset($_FILES['video_image']['name'][$key])){
                        $file_orig = $_FILES['video_image']['name'][$key];
                        $file_temp = $_FILES['video_image']['tmp_name'][$key];
                        $error = $_FILES['video_image']['error'][$key];
                        $size = intval($_FILES['video_image']['size'][$key]);
                        $file_ext=substr($file_orig,strrpos($file_orig,'.'));
                        if(substr($file_ext,0,1)=='.') 
                        $file_ext=substr($file_ext,1);
                        $file_new = "video_image_".rand().'_'.rand().'.'.$file_ext; 
                        $dir = $_SERVER['DOCUMENT_ROOT'].'/webadmin/assets/icon/image/';
                        if(move_uploaded_file($file_temp,$dir.$file_new)){
                            $image['video_image']=$file_new;
                        }
                    }
                } 
                $image['crousel_id']=$id;
                $image['file_orig']=$image['name'];
                $images = crouselGenericsFromPost($image,'`product_page_carousel_images`');
                crouselGenericToDatabase($images,'`product_page_carousel_images`');
            } 
        }else{

        }
         if (array_key_exists('item_images_deleted_val', $_POST)) {
            foreach (preg_split('/[^0-9]+/', $_POST['item_images_deleted_val']) as $v) {
                if ($v) {
                          crouselGenericsDelete($v,'`product_page_carousel_images`','id');
                }
            }
        }
	echo "<script>window.location.href='/webadmin/product_carousel/addedit.php?action=do_edit&id=".$id."'</script>";
	
}
function url($name,$id)
{
    return sprintf(
        '/brand/%s-%d',
        urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $name), '-')),
        $id
    );
}
?>

<h1 style="text-align:center;"><?= ($id > 0)?'Edit':'Add'?> Carousel   <?= htmlspecialchars($data['name']) ?> - <?= htmlspecialchars(date(_('d/m/Y'))) ?></h1>
<?php
$imagestyle=($item['type']=='video')?'style="display:none;"':'';
$videostyle=($item['type']=='image' || $id==0)?'style="display:none;"':'';
?>
<form action="?action=<?= $action ?>&id=<?= $id ?>" method="post" enctype="multipart/form-data">
	<?php 
	include __DIR__ . '/details.tpl.php';
    echo '<div id="image_div" '.$imagestyle.'>';
	include __DIR__ . '/product_images.php';
     echo '</div><div id="video_div" '.$videostyle.'>';
    include __DIR__ . '/crausel_video.php';
    echo '</div>';
    include __DIR__ .'/carousel_footer.php';
    include __DIR__ .'/carousel_setting.php';
    if ($id > 0) {
    include __DIR__.'/carousel_view.php';
    }
    include __DIR__ . '/front_page_setting.php';
	include __DIR__ . '/status_detail.php';
	?>
	<div style="text-align:center;">
        <button type="submit" style="padding: 12px 81px;cursor:pointer;" ><?= ($id > 0)?'Update Carousel':'Add Carousel'?></button>
        <?php if($id > 0) {?>
        <button type="button" style="padding: 12px 81px;cursor:pointer;margin-left: 50px;" onclick='window.open("/webadmin/product_carousel/preview.php?id=<?= $item['id']?>","_blank")'>PREVIEW</button>
    <?php } ?>
        <span style="float:right;margin-right: 50px;"><i><span style="color:red"><i><small>*</small></span> - Required Field</i></span>
    </div>
</form>

<script type="text/javascript" src="//code.jquery.com/jquery-1.9.0.min.js"></script>
<script type="text/javascript">
    $('#carousel_type').change(function(){
        if($('#carousel_type').val()=='image'){
            setValue("item_images_deleted_val",'');
            $('#image_div').show();
            $('#video_div').hide();
        }else{
            $('#image_div').hide();
            $('#video_div').show();
        }
    })
</script>
<?php
include_once __DIR__ . '/footer.inc.php';
