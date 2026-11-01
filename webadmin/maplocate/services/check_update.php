<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
require_once(__DIR__."/../db/connection.php");

//UPDATE 1.2
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'icon';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD icon varchar(50) DEFAULT NULL;");
    }
}

//UPDATE 1.4
$result = $mysqli->query("SHOW INDEX FROM sml_markers WHERE Key_name = 'lat_lon';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD UNIQUE KEY `lat_lon` (`id_map`,`lat`,`lon`,`name`);");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_users LIKE 'role';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_users ADD `role` varchar(50) DEFAULT 'customer';");
        $result2 = $mysqli->query("SELECT * FROM sml_users WHERE role='administrator';");
        if ($result2->num_rows==0) {
            $mysqli->query("UPDATE sml_users SET role='administrator' LIMIT 1;");
        }
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_users LIKE 'active';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_users ADD `active` tinyint(1) NOT NULL DEFAULT '1';");
    }
}

//UPDATE 1.5
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'ga_tracking_id';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `ga_tracking_id` varchar(50) DEFAULT NULL;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'friendly_url';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `friendly_url` varchar(200) DEFAULT NULL;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'active';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `active` tinyint(1) NOT NULL DEFAULT '1';");
    }
}

//UPDATE 1.6
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'active';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `active` tinyint(1) NOT NULL DEFAULT '1';");
    }
}

//UPDATE 1.8
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'color_hex';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `color_hex` varchar(20) NOT NULL DEFAULT '';");
    }
}

//UPDATE 1.9
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'show_list';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `show_list` tinyint(1) NOT NULL DEFAULT '0';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'default_zoom';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `default_zoom` int(11) NOT NULL DEFAULT '0';");
    }
}

//UPDATE 2.0
$result = $mysqli->query("SHOW TABLES LIKE 'sml_categories';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("CREATE TABLE IF NOT EXISTS `sml_categories` (
                              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                              `id_map` bigint(20) unsigned DEFAULT NULL,
                              `name` varchar(250) DEFAULT NULL,
                              PRIMARY KEY (`id`),
                              KEY `id_map` (`id_map`),
                              CONSTRAINT `sml_categories_ibfk_1` FOREIGN KEY (`id_map`) REFERENCES `sml_maps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'id_category';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `id_category` bigint(20) unsigned DEFAULT NULL;");
        $mysqli->query("ALTER TABLE `sml_markers` ADD FOREIGN KEY (`id_category`) REFERENCES `sml_categories` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;");
    }
}

//UPDATE 2.0.1
$result = $mysqli->query("SHOW COLUMNS FROM sml_images LIKE 'id_marker';");
if($result) {
    if ($result->num_rows==1) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $type = $row['Type'];
        if (strpos($type, '20') === false) {
            $mysqli->query("ALTER TABLE `sml_images` CHANGE `id_marker` `id_marker` BIGINT(20)  UNSIGNED  NULL  DEFAULT NULL;");
            $mysqli->query("DELETE FROM sml_images WHERE id_marker NOT IN (SELECT id FROM sml_markers);");
            $mysqli->query("ALTER TABLE `sml_images` ADD FOREIGN KEY (`id_marker`) REFERENCES `sml_markers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");
        }
    }
}

//UPDATE 2.1
$result = $mysqli->query("SHOW TABLES LIKE 'sml_icons';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("CREATE TABLE IF NOT EXISTS `sml_icons` (
                                  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                                  `id_map` bigint(20) unsigned DEFAULT NULL,
                                  `image` varchar(50) DEFAULT NULL,
                                  PRIMARY KEY (`id`),
                                  KEY `id_map` (`id_map`),
                                  CONSTRAINT `sml_icon_ibfk_1` FOREIGN KEY (`id_map`) REFERENCES `sml_maps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'id_icon_library';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `id_icon_library` bigint(20) unsigned NOT NULL DEFAULT '0' AFTER `icon`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'website_caption';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `website_caption` varchar(200) DEFAULT NULL AFTER `website`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'extra_field_value_1';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_value_1` text;");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_icon_1` varchar(50) DEFAULT 'far fa-circle';");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_value_2` text;");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_icon_2` varchar(50) DEFAULT 'far fa-circle';");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_value_3` text;");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_icon_3` varchar(50) DEFAULT 'far fa-circle';");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_value_4` text;");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_icon_4` varchar(50) DEFAULT 'far fa-circle';");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_value_5` text;");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_icon_5` varchar(50) DEFAULT 'far fa-circle';");
    }
}

//UPDATE 2.2
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'density_color';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `density_color` tinyint(1) NOT NULL DEFAULT '0';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_categories LIKE 'default_selected';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_categories ADD `default_selected` tinyint(1) NOT NULL DEFAULT '0';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'password';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `password` varchar(200) DEFAULT NULL;");
    }
}

//UPDATE 2.5
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'markers_size';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `markers_size` float NOT NULL DEFAULT '1.1';");
    }
}
$result = $mysqli->query("SHOW TABLES LIKE 'sml_reviews';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("CREATE TABLE IF NOT EXISTS `sml_reviews` (
                                  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                                  `identifier` varchar(250) DEFAULT NULL,
                                  `id_marker` bigint(20) unsigned NOT NULL,
                                  `create_date` datetime NOT NULL,
                                  `email` varchar(250) DEFAULT '',
                                  `name` varchar(250) DEFAULT NULL,
                                  `rating` int(1) DEFAULT NULL,
                                  `comment` text DEFAULT NULL,
                                  PRIMARY KEY (`id`),
                                  KEY `id_marker` (`id_marker`),
                                  CONSTRAINT `sml_reviews_ibfk_1` FOREIGN KEY (`id_marker`) REFERENCES `sml_markers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'enable_reviews';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `enable_reviews` tinyint(1) NOT NULL DEFAULT '1';");
    }
}

//UPDATE 2.7
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'whatsapp';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `whatsapp` varchar(50) DEFAULT NULL AFTER `phone`;");
    }
}
$result = $mysqli->query("SHOW TABLES LIKE 'sml_markers_categories_assoc';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("CREATE TABLE IF NOT EXISTS `sml_markers_categories_assoc` (
                              `id_marker` bigint(20) unsigned NOT NULL,
                              `id_category` bigint(20) unsigned NOT NULL,
                              UNIQUE KEY `id_marker_2` (`id_marker`,`id_category`),
                              KEY `id_marker` (`id_marker`),
                              KEY `id_category` (`id_category`),
                              CONSTRAINT `sml_markers_categories_assoc_ibfk_1` FOREIGN KEY (`id_marker`) REFERENCES `sml_markers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                              CONSTRAINT `sml_markers_categories_assoc_ibfk_2` FOREIGN KEY (`id_category`) REFERENCES `sml_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $query_c = "SELECT id,id_category FROM sml_markers WHERE id_category IS NOT NULL;";
        $result_c = $mysqli->query($query_c);
        if($result_c) {
            if($result_c->num_rows>0) {
                while($row_c=$result_c->fetch_array(MYSQLI_ASSOC)) {
                    $id_marker = $row_c['id'];
                    $id_category = $row_c['id_category'];
                    $mysqli->query("INSERT IGNORE INTO sml_markers_categories_assoc(id_marker,id_category) VALUES($id_marker,$id_category);");
                }
            }
        }
        $mysqli->query("ALTER TABLE sml_markers DROP FOREIGN KEY sml_markers_ibfk_2;");
        $mysqli->query("ALTER TABLE sml_markers DROP COLUMN id_category;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_categories LIKE 'id_category_parent';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_categories ADD `id_category_parent` bigint(20) unsigned DEFAULT NULL;");
        $mysqli->query("ALTER TABLE `sml_categories` ADD FOREIGN KEY (`id_category_parent`) REFERENCES `sml_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'access_count';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `access_count` int(11) NOT NULL DEFAULT '0';");
    }
}

//UPDATE 2.8
$result = $mysqli->query("SHOW TABLES LIKE 'sml_settings';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("CREATE TABLE IF NOT EXISTS `sml_settings` (
                                `id` int(11) NOT NULL DEFAULT '0',
                                `name` varchar(200) DEFAULT 'Simple Map Locator',
                                `theme_color` varchar(25) NOT NULL DEFAULT '#0b5394',
                                `font_backend` varchar(50) DEFAULT 'Nunito',
                                `logo` varchar(50) DEFAULT NULL,
                                `background` varchar(50) DEFAULT NULL,
                                `language` varchar(10) NOT NULL DEFAULT 'en_US',
                                `language_domain` varchar(50) NOT NULL DEFAULT 'default',
                                `help_url` varchar(250) DEFAULT NULL,
                                `languages_enabled` text,
                                `welcome_msg` text,
                                PRIMARY KEY (`id`)
                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $mysqli->query("INSERT IGNORE INTO `sml_settings` (`id`) VALUES(1);");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_users LIKE 'language';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_users ADD `language` varchar(10) DEFAULT NULL;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'language';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `language` varchar(10) DEFAULT NULL;");
    }
}

//UPDATE 2.9
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'street_basemap';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `street_basemap` varchar(250) DEFAULT NULL;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'street_maxzoom';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `street_maxzoom` int(2) NOT NULL DEFAULT '20';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'satellite_basemap';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `satellite_basemap` varchar(250) DEFAULT NULL;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'satellite_maxzoom';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `satellite_maxzoom` int(2) NOT NULL DEFAULT '20';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'icon_color_hex';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `icon_color_hex` varchar(20) NOT NULL DEFAULT '#ffffff' AFTER `color_hex`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'marker_size';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `marker_size` float NOT NULL DEFAULT '0' AFTER `icon_color_hex`;");
    }
}
$result = $mysqli->query("SHOW TABLES LIKE 'sml_markers_connects';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("CREATE TABLE IF NOT EXISTS `sml_markers_connects` (
                                  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                                  `id_marker_source` bigint(20) unsigned NOT NULL,
                                  `id_marker_dest` bigint(20) unsigned NOT NULL,
                                  `color` varchar(50) NOT NULL DEFAULT '#ff0000',
                                  `width` int(11) NOT NULL DEFAULT 2,
                                  PRIMARY KEY (`id`),
                                  KEY `id_marker_source` (`id_marker_source`),
                                  KEY `id_marker_dest` (`id_marker_dest`),
                                  CONSTRAINT `sml_markers_connects_ibfk_1` FOREIGN KEY (`id_marker_source`) REFERENCES `sml_markers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                                  CONSTRAINT `sml_markers_connects_ibfk_2` FOREIGN KEY (`id_marker_dest`) REFERENCES `sml_markers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                                ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_settings LIKE 'purchase_code';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_settings ADD `purchase_code` varchar(250) DEFAULT NULL;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_settings LIKE 'license';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_settings ADD `license` varchar(250) DEFAULT NULL;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_settings LIKE 'version';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_settings ADD `version` varchar(10) NOT NULL DEFAULT '';");
    }
}

//UPDATE 2.9.1
$result = $mysqli->query("SHOW COLUMNS FROM sml_settings LIKE 'footer_link_1';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_settings ADD `footer_link_1` varchar(200) DEFAULT NULL;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_settings LIKE 'footer_value_1';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_settings ADD `footer_value_1` text;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_settings LIKE 'footer_link_2';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_settings ADD `footer_link_2` varchar(200) DEFAULT NULL;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_settings LIKE 'footer_value_2';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_settings ADD `footer_value_2` text;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_settings LIKE 'footer_link_3';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_settings ADD `footer_link_3` varchar(200) DEFAULT NULL;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_settings LIKE 'footer_value_3';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_settings ADD `footer_value_3` text;");
    }
}

//UPDATE 3.0
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'cluster_distance';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `cluster_distance` int(11) NOT NULL DEFAULT '25';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'centered';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `centered` tinyint(1) NOT NULL DEFAULT '0';");
    }
}
$result = $mysqli->query("SHOW TABLES LIKE 'sml_assign_maps';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("CREATE TABLE IF NOT EXISTS `sml_assign_maps` (
                                `id_user` int(11) unsigned DEFAULT NULL,
                                `id_map` bigint(20) unsigned DEFAULT NULL,
                                `edit_map` tinyint(1) NOT NULL DEFAULT 1,
                                `create_markers` tinyint(1) NOT NULL DEFAULT 1,
                                `edit_markers` tinyint(1) NOT NULL DEFAULT 1,
                                `delete_markers` tinyint(1) NOT NULL DEFAULT 0,
                                `connections` tinyint(1) NOT NULL DEFAULT 1,
                                `categories` tinyint(1) NOT NULL DEFAULT 1,
                                `icons_library` tinyint(1) NOT NULL DEFAULT 1,
                                `review` tinyint(1) NOT NULL DEFAULT 1,
                                `publish` tinyint(1) NOT NULL DEFAULT 1,
                                UNIQUE KEY `id_user` (`id_user`,`id_map`),
                                KEY `id_map` (`id_map`),
                                CONSTRAINT `sml_assign_maps_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `sml_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                                CONSTRAINT `sml_assign_maps_ibfk_2` FOREIGN KEY (`id_map`) REFERENCES `sml_maps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'selected_zoom';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `selected_zoom` int(11) NOT NULL DEFAULT '18' AFTER `default_zoom`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'enable_directions';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `enable_directions` tinyint(1) NOT NULL DEFAULT '1' AFTER `enable_reviews`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'enable_streetview';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `enable_streetview` tinyint(1) NOT NULL DEFAULT '1' AFTER `enable_directions`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers_connects LIKE 'title';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers_connects ADD `title` varchar(250) DEFAULT NULL;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers_connects LIKE 'description';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers_connects ADD `description` longtext;");
    }
}

//UPDATE 3.1
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'cat_filter';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `cat_filter` tinyint(1) NOT NULL DEFAULT '0';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'nav_markers';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `nav_markers` tinyint(1) NOT NULL DEFAULT '1';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'order';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `order` bigint(20) NOT NULL DEFAULT '0' AFTER `active`;");
        $query_maps = "SELECT id FROM sml_maps;";
        $result_maps = $mysqli->query($query_maps);
        if($result_maps) {
            while($row_maps = $result_maps->fetch_array(MYSQLI_ASSOC)) {
                $id_map = $row_maps['id'];
                $order = 0;
                $query_order = "SELECT id FROM sml_markers WHERE id_map=$id_map ORDER BY id DESC;";
                $result_order = $mysqli->query($query_order);
                if($result_order) {
                    while ($row_order = $result_order->fetch_array(MYSQLI_ASSOC)) {
                        $id_marker = $row_order['id'];
                        $mysqli->query("UPDATE sml_markers SET `order`=$order WHERE id=$id_marker;");
                        $order++;
                    }
                }
            }
        }
        $mysqli->query("ALTER TABLE sml_markers DROP COLUMN `pinned`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'to_validate';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `to_validate` tinyint(1) NOT NULL DEFAULT '0' AFTER `active`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'enable_add_marker';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `enable_add_marker` tinyint(1) NOT NULL DEFAULT '0';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'extra_button_value_1';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_button_value_1` text AFTER `extra_field_icon_5`;");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_button_icon_1` varchar(50) DEFAULT 'fas fa-info' AFTER `extra_button_value_1`;");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_button_title_1` varchar(50) DEFAULT '' AFTER `extra_button_icon_1`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'view_directions';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `view_directions` tinyint(1) NOT NULL DEFAULT '1' AFTER `to_validate`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'view_street_view';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `view_street_view` tinyint(1) NOT NULL DEFAULT '1' AFTER `view_directions`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'view_review';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `view_review` tinyint(1) NOT NULL DEFAULT '1' AFTER `view_street_view`;");
    }
}

//UPDATE 3.2
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'font_viewer';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `font_viewer` varchar(50) DEFAULT 'Roboto';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'sheet_detail';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `sheet_detail` tinyint(1) NOT NULL DEFAULT '1';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'default_view';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `default_view` enum('street','satellite') DEFAULT 'street';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'quality';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `quality` enum('low','medium','high') DEFAULT 'medium';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'my_location_zoom';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `my_location_zoom` int(11) NOT NULL DEFAULT '0' AFTER `selected_zoom`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'default_my_location';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `default_my_location` tinyint(1) NOT NULL DEFAULT '0' AFTER `my_location_zoom`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'enable_share';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `enable_share` tinyint(1) NOT NULL DEFAULT '1' AFTER `enable_streetview`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'view_share';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `view_share` tinyint(1) NOT NULL DEFAULT '1' AFTER `view_street_view`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'extra_field_value_6';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_value_6` text;");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_icon_6` varchar(50) DEFAULT 'far fa-circle';");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_value_7` text;");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_icon_7` varchar(50) DEFAULT 'far fa-circle';");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_value_8` text;");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_icon_8` varchar(50) DEFAULT 'far fa-circle';");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_value_9` text;");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_icon_9` varchar(50) DEFAULT 'far fa-circle';");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_value_10` text;");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_icon_10` varchar(50) DEFAULT 'far fa-circle';");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_value_11` text;");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_icon_11` varchar(50) DEFAULT 'far fa-circle';");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_value_12` text;");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_icon_12` varchar(50) DEFAULT 'far fa-circle';");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_value_13` text;");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_icon_13` varchar(50) DEFAULT 'far fa-circle';");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_value_14` text;");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_icon_14` varchar(50) DEFAULT 'far fa-circle';");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_value_15` text;");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_icon_15` varchar(50) DEFAULT 'far fa-circle';");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_value_16` text;");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_icon_16` varchar(50) DEFAULT 'far fa-circle';");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_value_17` text;");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_icon_17` varchar(50) DEFAULT 'far fa-circle';");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_value_18` text;");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_icon_18` varchar(50) DEFAULT 'far fa-circle';");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_value_19` text;");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_icon_19` varchar(50) DEFAULT 'far fa-circle';");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_value_20` text;");
        $mysqli->query("ALTER TABLE sml_markers ADD `extra_field_icon_20` varchar(50) DEFAULT 'far fa-circle';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'enable_list';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `enable_list` tinyint(1) NOT NULL DEFAULT '1' AFTER `active`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'enable_search';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `enable_search` tinyint(1) NOT NULL DEFAULT '1' AFTER `enable_list`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'enable_categories';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `enable_categories` tinyint(1) NOT NULL DEFAULT '1' AFTER `enable_search`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'density_color_tolerance';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `density_color_tolerance` int(11) NOT NULL DEFAULT '1' AFTER `density_color`;");
    }
}
$result = $mysqli->query("SHOW TABLES LIKE 'sml_story';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("CREATE TABLE IF NOT EXISTS `sml_story` (
                                `id_map` bigint(20) unsigned DEFAULT NULL,
                                `id_marker` bigint(20) unsigned DEFAULT NULL,
                                `description` text,
                                `zoom` int(11) NOT NULL DEFAULT 10,
                                `priority` int(11) NOT NULL,
                                CONSTRAINT `sml_story_sml_maps_id_fk` FOREIGN KEY (`id_map`) REFERENCES `sml_maps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                                CONSTRAINT `sml_story_sml_markers_id_fk` FOREIGN KEY (`id_marker`) REFERENCES `sml_markers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_assign_maps LIKE 'story';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_assign_maps ADD `story` tinyint(1) NOT NULL DEFAULT '1' AFTER `connections`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'enable_story';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `enable_story` tinyint(1) NOT NULL DEFAULT '1' AFTER `enable_categories`;");
    }
}

//UPDATE 3.3
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'enable_search_location';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `enable_search_location` tinyint(1) NOT NULL DEFAULT '0' AFTER `enable_search`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'street_attributions';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `street_attributions` varchar(500) DEFAULT NULL AFTER `street_basemap`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'satellite_attributions';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `satellite_attributions` varchar(500) DEFAULT NULL AFTER `satellite_basemap`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'min_zoom_level';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `min_zoom_level` int(11) NOT NULL DEFAULT '0';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'markers_color_hex';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `markers_color_hex` varchar(20) NOT NULL DEFAULT '' AFTER `markers_size`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'markers_icon_color_hex';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `markers_icon_color_hex` varchar(20) NOT NULL DEFAULT '#ffffff' AFTER `markers_color_hex`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'markers_icon';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `markers_icon` varchar(50) DEFAULT NULL AFTER `markers_icon_color_hex`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'markers_id_icon_library';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `markers_id_icon_library` bigint(20) unsigned NOT NULL DEFAULT 0 AFTER `markers_icon`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'geofence_radius';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `geofence_radius` int(11) NOT NULL DEFAULT '0';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'geofence_color';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `geofence_color` varchar(50) NOT NULL DEFAULT 'rgba(0,0,255,0.1)';");
    }
}

//UPDATE 3.5
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'featured';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `featured` tinyint(1) NOT NULL DEFAULT '0' AFTER `active`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'search_highlight';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `search_highlight` int(11) NOT NULL DEFAULT '-1' AFTER `selected_zoom`;");
    }
}

//UPDATE 3.6
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'order_by';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `order_by` enum('priority','name','city') DEFAULT 'priority';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'show_in_first_page';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `show_in_first_page` tinyint(1) NOT NULL DEFAULT '0';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'geoJSON';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `geoJSON` varchar(500) DEFAULT NULL;");
    }
}

//UPDATE 3.7
$result = $mysqli->query("SHOW COLUMNS FROM sml_categories LIKE 'markers_size';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_categories ADD `markers_size` float NOT NULL DEFAULT '1.1';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_categories LIKE 'markers_color_hex';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_categories ADD `markers_color_hex` varchar(20) NOT NULL DEFAULT '';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_categories LIKE 'markers_icon_color_hex';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_categories ADD `markers_icon_color_hex` varchar(20) NOT NULL DEFAULT '#ffffff';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_categories LIKE 'markers_icon';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_categories ADD `markers_icon` varchar(50) DEFAULT NULL;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_categories LIKE 'markers_id_icon_library';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_categories ADD `markers_id_icon_library` bigint(20) unsigned NOT NULL DEFAULT 0;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'weather';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `weather` enum('celsius','fahrenheit','none') DEFAULT 'celsius';");
    }
}

//UPDATE 3.8
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'cat_filter_type';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `cat_filter_type` enum('or','and') DEFAULT 'or';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'enable_globe';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `enable_globe` tinyint(1) NOT NULL DEFAULT '0';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'meta_title';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `meta_title` varchar(100) DEFAULT NULL;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'meta_description';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `meta_description` text DEFAULT NULL;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'meta_image';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `meta_image` varchar(50) DEFAULT NULL;");
    }
}

//UPDATE 3.9
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'draw_geometries';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `draw_geometries` longtext DEFAULT NULL;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_assign_maps LIKE 'geometries';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_assign_maps ADD `geometries` tinyint(1) NOT NULL DEFAULT '1' AFTER `connections`;");
    }
}

//UPDATE 4.0
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'open_sheet';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `open_sheet` tinyint(1) NOT NULL DEFAULT '1';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'view_popup';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `view_popup` tinyint(1) NOT NULL DEFAULT '1';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'enable_popup';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `enable_popup` tinyint(1) NOT NULL DEFAULT '1';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'popup_image_height';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `popup_image_height` int(11) NOT NULL DEFAULT '60';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'popup_background';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `popup_background` varchar(20) NOT NULL DEFAULT '#ffffff';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'popup_color';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `popup_color` varchar(20) NOT NULL DEFAULT '#000000';");
    }
}

//UPDATE 4.1
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'sheet_mobile';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `sheet_mobile` enum('reduced','opened') DEFAULT 'reduced';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'logo_mobile';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `logo_mobile` varchar(100) DEFAULT NULL AFTER `logo`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'logo_height';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `logo_height` int(11) DEFAULT '40' AFTER `logo_mobile`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'logo_height_mobile';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `logo_height_mobile` int(11) DEFAULT '30' AFTER `logo_height`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'logo_link';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `logo_link` varchar(200) DEFAULT NULL AFTER `logo_height`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'color_hex_hover';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `color_hex_hover` varchar(20) NOT NULL DEFAULT '' AFTER `color_hex`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'icon_color_hex_hover';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `icon_color_hex_hover` varchar(20) NOT NULL DEFAULT '' AFTER `color_hex_hover`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'markers_color_hex_hover';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `markers_color_hex_hover` varchar(20) NOT NULL DEFAULT '' AFTER `markers_icon_color_hex`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'markers_icon_color_hex_hover';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `markers_icon_color_hex_hover` varchar(20) NOT NULL DEFAULT '' AFTER `markers_color_hex_hover`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_categories LIKE 'markers_color_hex_hover';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_categories ADD `markers_color_hex_hover` varchar(20) NOT NULL DEFAULT '' AFTER `markers_icon_color_hex`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_categories LIKE 'markers_icon_color_hex_hover';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_categories ADD `markers_icon_color_hex_hover` varchar(20) NOT NULL DEFAULT '' AFTER `markers_color_hex_hover`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_settings LIKE 'style_login';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_settings ADD `style_login` tinyint(1) NOT NULL DEFAULT '1';");
    }
}

//UPDATE 4.2
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'pin_marker_size';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `pin_marker_size` float NOT NULL DEFAULT '1.1' AFTER `markers_id_icon_library`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'pin_marker_color_hex';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `pin_marker_color_hex` varchar(20) NOT NULL DEFAULT '#ff0000' AFTER `pin_marker_size`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'pin_marker_icon_color_hex';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `pin_marker_icon_color_hex` varchar(20) NOT NULL DEFAULT '#ffffff' AFTER `pin_marker_color_hex`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'pin_marker_icon';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `pin_marker_icon` varchar(50) DEFAULT 'fas fa-map-marker-alt|f3c5' AFTER `pin_marker_icon_color_hex`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'pin_marker_id_icon_library';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `pin_marker_id_icon_library` bigint(20) unsigned NOT NULL DEFAULT 0 AFTER `pin_marker_icon`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'pin_marker_align';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `pin_marker_align` enum('bottom','middle','top') DEFAULT 'middle' AFTER `pin_marker_id_icon_library`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'pin_marker_enable';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `pin_marker_enable` tinyint(1) NOT NULL DEFAULT '0' AFTER `pin_marker_align`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_settings LIKE 'font_provider';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_settings ADD `font_provider` enum('systems','google','collabs') DEFAULT 'google';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'open_extra_content';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `open_extra_content` tinyint(1) NOT NULL DEFAULT '0' AFTER `extra_button_title_1`;");
    }
}

//UPDATE 4.3
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'direction_map';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `direction_map` enum('google','bing','baidu','yandex') DEFAULT 'google';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'streetview_map';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `streetview_map` enum('google','bing','yandex') DEFAULT 'google';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'background_image';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `background_image` varchar(50) DEFAULT NULL;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'background_image_mobile';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `background_image_mobile` varchar(50) DEFAULT NULL;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'auto_start';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `auto_start` tinyint(1) NOT NULL DEFAULT '1';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'loading_background_color';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `loading_background_color` varchar(50) DEFAULT '#343434';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'loading_text_color';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `loading_text_color` varchar(50) DEFAULT '#ffffff';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'map_type';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `map_type` enum('default','large') DEFAULT 'default' AFTER `map_style`;");
    }
}
$result = $mysqli->query("SHOW TABLES LIKE 'sml_showcases';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("CREATE TABLE IF NOT EXISTS `sml_showcases` (
                           `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                           `id_user` int(11) unsigned DEFAULT NULL,
                           `code` varchar(100) DEFAULT NULL,
                           `name` varchar(250) DEFAULT NULL,
                           `friendly_url` varchar(100) DEFAULT NULL,
                           `banner` varchar(100) DEFAULT NULL,
                           `logo` varchar(100) DEFAULT NULL,
                           `bg_color` varchar(10) NOT NULL DEFAULT '#EEEEEE',
                           `header_html` longtext DEFAULT NULL,
                           `footer_html` longtext DEFAULT NULL,
                           `meta_title` varchar(100) DEFAULT NULL,
                           `meta_description` text DEFAULT NULL,
                           `meta_image` varchar(50) DEFAULT NULL,
                           `show_in_first_page` tinyint(1) NOT NULL DEFAULT '0',
                           `sort_settings` text DEFAULT NULL,
                           PRIMARY KEY (`id`),
                           KEY `id_user` (`id_user`),
                           CONSTRAINT `sml_showcases_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `sml_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }
}
$result = $mysqli->query("SHOW TABLES LIKE 'sml_showcase_list';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("CREATE TABLE IF NOT EXISTS `sml_showcase_list` (
                       `id_showcase` bigint(20) unsigned DEFAULT NULL,
                       `id_map` bigint(20) unsigned DEFAULT NULL,
                       `priority` int(11) NOT NULL DEFAULT 0,
                       KEY `id_showcase` (`id_showcase`),
                       KEY `id_map` (`id_map`),
                       CONSTRAINT `sml_showcase_list_ibfk_1` FOREIGN KEY (`id_showcase`) REFERENCES `sml_showcases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                       CONSTRAINT `sml_showcase_list_ibfk_2` FOREIGN KEY (`id_map`) REFERENCES `sml_maps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }
}

//UPDATE 4.4
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'direction_map';");
if($result) {
    if ($result->num_rows==1) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $type = $row['Type'];
        if (strpos($type, 'openrouteservice') === false) {
            $mysqli->query("ALTER TABLE sml_maps MODIFY COLUMN `direction_map` enum('openrouteservice','google','bing','baidu','yandex') DEFAULT 'google';");
        }
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_settings LIKE 'openrouteservice_api_key';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_settings ADD `openrouteservice_api_key` varchar(250) DEFAULT NULL;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_markers LIKE 'schedule';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_markers ADD `schedule` varchar(250) DEFAULT NULL;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_settings LIKE 'timezone';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_settings ADD `timezone` varchar(50) DEFAULT NULL;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'search_radius_enabled';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `search_radius_enabled` tinyint(1) NOT NULL DEFAULT '0';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'search_radius_m';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `search_radius_m` int(11) NOT NULL DEFAULT '10000';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'default_extra_fields';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `default_extra_fields` text DEFAULT NULL;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'default_am_fields';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `default_am_fields` text DEFAULT NULL;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'default_am_placeholders';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `default_am_placeholders` text DEFAULT NULL;");
    }
}

//UPDATE 4.5
$result = $mysqli->query("SHOW COLUMNS FROM sml_settings LIKE 'share_providers';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_settings ADD `share_providers` varchar(250) NOT NULL DEFAULT 'copy_link,email,whatsapp,facebook,twitter,linkedin,telegram,facebook_messenger,pinterest,reddit,line,viber,vk,qzone,wechat,threads';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'share_providers';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `share_providers` varchar(250) NOT NULL DEFAULT 'copy_link,email,whatsapp,facebook,twitter,linkedin,telegram,facebook_messenger,pinterest,reddit,line,viber,vk,qzone,wechat,threads';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'enable_share_map';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `enable_share_map` tinyint(1) NOT NULL DEFAULT '1' AFTER `enable_share`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'map_style_satellite';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `map_style_satellite` varchar(50) NOT NULL DEFAULT 'arcgis' AFTER `map_style`;");
        $mysqli->query("UPDATE sml_maps SET map_style_satellite='custom' WHERE map_style='custom' AND satellite_basemap<>'';");
        $mysqli->query("UPDATE sml_maps SET maptiler_api_satellite=maptiler_api WHERE maptiler_api<>'';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'enable_map_street';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `enable_map_street` tinyint(1) NOT NULL DEFAULT '1' AFTER `satellite_maxzoom`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'enable_map_satellite';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `enable_map_satellite` tinyint(1) NOT NULL DEFAULT '1' AFTER `enable_map_street`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'use_street_attributions';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `use_street_attributions` tinyint(1) NOT NULL DEFAULT '1' AFTER `street_attributions`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'use_satellite_attributions';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `use_satellite_attributions` tinyint(1) NOT NULL DEFAULT '1' AFTER `satellite_attributions`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'maptiler_api_satellite';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `maptiler_api_satellite` varchar(200) DEFAULT NULL AFTER `maptiler_api`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'map_street_provider';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `map_street_provider` enum('default','maptiler','here','custom') DEFAULT 'default' AFTER `enable_map_satellite`;");
        $mysqli->query("UPDATE sml_maps SET map_street_provider='custom' WHERE map_style='custom' AND street_basemap<>'';");
        $mysqli->query("UPDATE sml_maps SET map_street_provider='maptiler' WHERE maptiler_api<>'' AND map_style!='custom' && map_style!='osm_free';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'map_satellite_provider';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `map_satellite_provider` enum('default','maptiler','here','custom') DEFAULT 'default' AFTER `map_street_provider`;");
        $mysqli->query("UPDATE sml_maps SET map_satellite_provider='custom' WHERE map_style_satellite='custom' AND satellite_basemap<>'';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'here_api';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `here_api` varchar(200) DEFAULT NULL AFTER `maptiler_api_satellite`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'here_api_satellite';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `here_api_satellite` varchar(200) DEFAULT NULL AFTER `here_api`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'enable_intro';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `enable_intro` tinyint(1) NOT NULL DEFAULT '0';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'intro_image';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `intro_image` varchar(100) DEFAULT NULL;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'intro_params';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `intro_params` text;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_showcases LIKE 'open_target';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_showcases ADD `open_target` enum('self','new') NOT NULL DEFAULT 'self';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_settings LIKE 'terms_and_conditions';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_settings ADD `terms_and_conditions` longtext;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_settings LIKE 'privacy_policy';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_settings ADD `privacy_policy` longtext;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_settings LIKE 'cookie_policy';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_settings ADD `cookie_policy` longtext;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_settings LIKE 'cookie_consent';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_settings ADD `cookie_consent` tinyint(1) NOT NULL DEFAULT '0';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_settings LIKE 'ga_tracking_id';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_settings ADD `ga_tracking_id` varchar(25) DEFAULT NULL;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_showcases LIKE 'ga_tracking_id';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_showcases ADD `ga_tracking_id` varchar(25) DEFAULT NULL;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_showcases LIKE 'cookie_consent';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_showcases ADD `cookie_consent` tinyint(1) NOT NULL DEFAULT '0';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_maps LIKE 'cookie_consent';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_maps ADD `cookie_consent` tinyint(1) NOT NULL DEFAULT '0';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_settings LIKE 'furl_blacklist';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_settings ADD `furl_blacklist` text;");
        $mysqli->query("UPDATE sml_settings SET furl_blacklist='ajax,assets,css,facicons,header,img,js,tmp,vendor';");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_settings LIKE 'sidebar';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_settings ADD `sidebar` enum('gradient','flat') DEFAULT 'gradient' AFTER `font_backend`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_settings LIKE 'sidebar_color_1';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_settings ADD `sidebar_color_1` varchar(25) DEFAULT NULL AFTER `sidebar`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_settings LIKE 'sidebar_color_2';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_settings ADD `sidebar_color_2` varchar(25) DEFAULT NULL AFTER `sidebar_color_1`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_settings LIKE 'sidebar_color_1_dark';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_settings ADD `sidebar_color_1_dark` varchar(25) DEFAULT NULL AFTER `sidebar_color_2`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_settings LIKE 'sidebar_color_2_dark';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_settings ADD `sidebar_color_2_dark` varchar(25) DEFAULT NULL AFTER `sidebar_color_1_dark`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_settings LIKE 'theme_color_dark';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_settings ADD `theme_color_dark` varchar(25) DEFAULT NULL AFTER `theme_color`;");
    }
}
$result = $mysqli->query("SHOW COLUMNS FROM sml_settings LIKE 'dark_mode';");
if($result) {
    if ($result->num_rows==0) {
        $mysqli->query("ALTER TABLE sml_settings ADD `dark_mode` tinyint(1) NOT NULL DEFAULT '1' AFTER `theme_color_dark`;");
    }
}