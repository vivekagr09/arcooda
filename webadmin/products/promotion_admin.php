<?php
$section_code = "PRD_CFG";

include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

include_once dirname(__FILE__) . '/config.inc.php';

switch ($action) {
    case "do_add":

        if (isset($_POST)) {

            $promotion_name = "";
            if (isset($_POST['promotion_name']) && !empty($_POST['promotion_name'])) {
                $promotion_name = $_POST['promotion_name'];
            }

            $promotion_image_link = '/img_promotions/' . uniqid();

            if (isset($_FILES['promotion_image']) && !empty($_FILES['promotion_image'])) {
                $promotion_image = $_FILES['promotion_image'];
                $chunk           = explode(".", $promotion_image['name']);
                $promotion_image_link .= "." . end($chunk);

                move_uploaded_file($promotion_image['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $promotion_image_link);
            } else {
                die("404");
            }

            $DB->query("INSERT INTO `product_promotion` (`name`, `img`) VALUES (" . formatSQLString($promotion_name) . ", '" . $promotion_image_link . "')");

            if (isset($_GET['popup'])) {
                $promotionOptions = array();

                $promotionQuery = $DB->query("SELECT * FROM `product_promotion`");
                while ($row = $DB->fetch_assoc($promotionQuery)) {
                    $promotionOptions[] = array(
                        "id"   => $row['id'],
                        "name" => $row['name'],
                    );
                }

                echo "<button onclick='window.close(); window.onunload=window.opener.location.reload();'>Close this window</button>";
            } else {
                echo "Promotion has been added.";
            }
        }

        break;
    case "do_edit":

        if (isset($_POST)) {

            $id = intval($_POST['id']);

            $promotion = $DB->query_first('SELECT * FROM `product_promotion` WHERE `id` = ' . $id, MYSQLI_ASSOC);

            $promotion_name = "";
            if (isset($_POST['promotion_name']) && !empty($_POST['promotion_name'])) {
                $promotion_name = $_POST['promotion_name'];

                $DB->query("UPDATE `product_promotion` SET `name` = " . formatSQLString($promotion_name) . " WHERE `id` = " . $id);
            }

            $promotion_image_link = '';

            if (isset($_FILES['promotion_image']) && !empty($_FILES['promotion_image'])) {

                $promotion_image_link = '/img_promotions/' . uniqid();
                $promotion_image      = $_FILES['promotion_image'];

                if (!empty($promotion_image['name'])) {
                    $chunk = explode(".", $promotion_image['name']);
                    $promotion_image_link .= "." . end($chunk);

                    move_uploaded_file($promotion_image['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $promotion_image_link);

                    unlink($_SERVER['DOCUMENT_ROOT'] . $promotion['img']);

                    $DB->query("UPDATE `product_promotion` SET `img` = " . formatSQLString($promotion_image_link) . " WHERE `id` = " . $id);
                }
            }

            if (isset($_GET['popup'])) {
                $promotionOptions = array();

                $promotionQuery = $DB->query("SELECT * FROM `product_promotion`");
                while ($row = $DB->fetch_assoc($promotionQuery)) {
                    $promotionOptions[] = array(
                        "id"   => $row['id'],
                        "name" => $row['name'],
                    );
                }

                echo "<button onclick='window.close(); window.onunload=window.opener.location.reload();'>Close this window</button>";
            } else {
                echo "Promotion has been edited.";
            }
        }

        break;
    case "do_delete":

        break;
    case "add":
        include dirname(__FILE__) . '/promotion_add_form.inc.php';
        break;
    case "edit":
        include dirname(__FILE__) . '/promotion_edit_form.inc.php';
        break;
    case "delete":

        break;
}
