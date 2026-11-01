<?php

// Get Icons
$query = $DB->query("SELECT * FROM `icons`");
$icons = array();
while ($row = $DB->fetch_assoc($query)) {
    $icons[] = $row;
}
$DB->free_result($query);
