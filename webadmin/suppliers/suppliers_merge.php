<?php
return;
set_time_limit(300);
include_once __DIR__ . '/suppliers_common.php';

if ('y' != $_GLOBALS['staff']['developer']) {
    die('Unauthorized');
}

die("Testing");
$mapping = array('name' => 'manufacturer', 'country' => '', 'url' => 'website', 'address' => '', 'phone' => '', 'fax' => '');
//$res=$DB->query("SELECT * FROM highway.manufacturers ORDER BY manufacturer_id ASC");
while ($row = $DB->fetch_assoc($res)) {
    $old_id = intval($row['manufacturer_id']);
    $sql    = "INSERT INTO suppliers";
    $set    = array();
    foreach ($mapping as $from => $to) {
        if ('' == $to) {
            $to = $from;
        }

        $set[] = $to . '=' . $DB->escape($row[$from]);
    }
    $set[] = "active='" . (('1' == $row['active']) ? 'y' : 'n') . "'";
    $set[] = "type='man'";
    $set[] = "site='1'";
    $sql .= " SET " . implode(', ', $set);
    //$DB->query($sql);
    echo $sql . '<br />';
    $new_id = $DB->insert_id();
    // now have to transfer the contacts.
    //$DB->query("UPDATE manufacturers SET hgr_id=".intval($new_id)." WHERE manufacturer_id=".$old_id);
    //$res2=$DB->query("SELECT * FROM man_contacts WHERE man_id=".$old_id);
    while ($row2 = $DB->fetch_assoc($res2)) {
        $sql2 = "INSERT INTO suppliers_contacts SET supplier_id=" . intval($new_id) . ", name=" . $DB->escape($row2['name']) . ", email=" . $DB->escape($row2['description']);
        //$DB->query($sql2);
        echo $sql2 . '<br />';
    }
    //now have to update the manufacturer address.
    //$res3=$DB->query("SELECT * FROM man_address WHERE man_id=".$old_id);
    while ($row3 = $DB->fetch_assoc($res3)) {
        $sql3 = "INSERT INTO suppliers_warehouse SET supplier_id=" . intval($new_id) . ", manufacturer=" . $DB->escape($row['name']) . ", address=" . $DB->escape($row3['address']) . ", phone=" . $DB->escape($row3['phone']) . ", fax=" . $DB->escape($row3['fax']) . ", country=" . $DB->escape($row3['country']) . ", website=" . $DB->escape($row3['web_address']);
        //$DB->query($sql3);
        echo $sql3 . '<br />';
    }

    echo '<hr />';
}
