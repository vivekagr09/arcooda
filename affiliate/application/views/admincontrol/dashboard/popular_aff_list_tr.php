
<?php
$db =& get_instance();
$products = $db->Product_model;
?>
<?php foreach ($populer_users as $key => $users) { ?>
    <tr>
        <?php
        $flag = '';
        if ($users['sortname'] != '') {
            $flag = base_url('assets/vertical/assets/images/flags/' . strtolower($users['sortname']) . '.png');
        }
        ?>
        <td><img class="top-affiliate-image" src="<?= $products->getAvatar($users['avatar']); ?>" alt="<?= $users['firstname'].' '.$users['lastname']; ?>" /><?= $users['firstname'].' '.$users['lastname']; ?></td>
        <td><img class="top-affiliate-country-flag" src="<?= $flag; ?>" alt="<?= strtoupper($users['sortname']) ?>"></td>
        <td><?= $fun_c_format($users['amount']); ?></td>
        <td><?= $fun_c_format($users['all_commition']); ?></td>
    </tr>
<?php } ?>