<?php
include_once __DIR__ . '/common.inc.php';

global $DB;

include_once __DIR__ . '/header.inc.php';

$action = "do_add";
$id = 0;
if (!function_exists('sliderGenericsFromPost')) {
    function sliderGenericsFromPost($posted, $table)
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
if (!function_exists('SliderGenericToDatabase')) {
    function SliderGenericToDatabase(&$item, $table, $primary_key = 'id') {
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
        deleteMysqlCacheData($table, $id);
        return $id;
    }
}

if (!function_exists('sliderGenericsDelete')) {
    function sliderGenericsDelete($id, $table, $secondary_key = 'view_id') {
        global $DB;
        $DB->query(sprintf(
            "DELETE FROM %s  WHERE  `%s` = %d",
            $table,
            $secondary_key,
            $id
        ));
        deleteMysqlCacheData($table, $id);
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

	} else {
		die("Incorrect action");
	}
}
$slider_images=[];
if ($id > 0) {
	
	$item = $DB->query_first("SELECT * FROM `front_slider` WHERE `id` = " . $id);
	$query = $DB->query(sprintf(
		    "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s  Where `%s` = %d  %s",
		    '`front_slider_images`',
		    'slider_id',
		     $id,
		    'ORDER BY `rank` ASC'
        ));
	 while ($row = $DB->fetch_assoc($query)) {
	        if (is_null($primary_key)) {
	            $slider_images[] = $row;
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
		$error['name'] = "Please enter the slider name.";
	}

	if (isset($_POST['text']) && !empty($_POST['text'])) {
		$data['text'] = trim($_POST['text']);
	} else {
		$error['text'] = "Please enter text for Slider.";
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
	    	$DB->query("Update `front_slider` set `default`='n'");
	    }else{
	    	$posted['item']['default']=='n';
	    }
        $slider = sliderGenericsFromPost($posted['item'],'`front_slider`');
        $id =SliderGenericToDatabase($slider,'`front_slider`');
        if(count($posted['product_image'])){
            foreach ($posted['product_image'] as $key => $image) {
                $image['slider_id']=$id;
                $image['file_orig']=$image['name'];
                $images = sliderGenericsFromPost($image,'`front_slider_images`');
                SliderGenericToDatabase($images,'`front_slider_images`');
            }
        }
         if (array_key_exists('item_images_deleted_val', $_POST)) {
            foreach (preg_split('/[^0-9]+/', $_POST['item_images_deleted_val']) as $v) {
                if ($v) {
                          sliderGenericsDelete($v,'`front_slider_images`','id');
                }
            }
        }
	echo "<script>window.location.href='/webadmin/front_slider/addedit.php?action=do_edit&id=".$id."'</script>";
	
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

<h1 style="text-align:center;"><?= ($id > 0)?'Edit':'Add'?> Slider   <?= htmlspecialchars($data['name']) ?> - <?= htmlspecialchars(date(_('d/m/Y'))) ?></h1>

<form action="?action=<?= $action ?>&id=<?= $id ?>" method="post">
	<?php 
	include __DIR__ . '/details.tpl.php';
	include __DIR__ . '/product_images.php';
	include __DIR__ . '/status_detail.php';
	?>
	<div style="text-align:center;">
        <button type="submit" style="padding: 12px 81px;cursor:pointer;" ><?= ($id > 0)?'Update Slider':'Add Slider'?></button>
        <?php if($id > 0) {?>
        <button type="button" style="padding: 12px 81px;cursor:pointer;margin-left: 50px;" onclick='window.open("/webadmin/front_slider/preview.php?id=<?= $item['id']?>","_blank")'>PREVIEW</button>
    <?php } ?>
        <span style="float:right;margin-right: 50px;"><i><span style="color:red"><i><small>*</small></span> - Required Field</i></span>
    </div>
</form>

<script type="text/javascript" src="//code.jquery.com/jquery-1.9.0.min.js"></script>

<?php
include_once __DIR__ . '/footer.inc.php';
