<?php

if (isset($_POST['groupId'])) {
    $new = (int)$_POST['groupId'];
    $studio->sql->query("UPDATE accounts SET groupId=$new WHERE id = $user_id");

    $plan = null;
    $plans = json_decode($studio->getopt("sub-plans"), true);

    foreach ($plans as $i => $p) {
        if (in_array($new, $p['groups'])) {
            $p['id'] = $i;
            $plan = $p;
        }
    }

    if (isset($plan)) {
        if (!$studio->getopt("sub-user-$user_id")) {
            if ($plan['cost']['onetime'] == 0 && $plan['cost']['monthly'] == 0 && $plan['cost']['annually'] == 0) {
                $studio->setopt("sub-user-$user_id", json_encode([
                    "active" => true,
                    "expires_on" => 0,
                    "subscribed_on" => time(),
                    "duration" => "onetime"
                ]));
            }
            else {
                $studio->setopt("sub-user-$user_id", json_encode([
                    "active" => false
                ]));
            }
        }
    }

    header("Location: user.php?id=$user_id");
    die;
}

if (is_null($plan)) {
?>
<div class="panel">
    <h3>Subscription Plan (None)</h3>

    <form action="" method="post">
        <p style="margin: 0 0 5px;">Set new plan:</p>
        <select class="fancy" name="groupId">
            <?php foreach ($plans as $p) { ?>
                <option value="<?php echo $p['assign']; ?>" <?php
                    if ($p['assign'] == $row->groupId) echo " selected";
                ?>><?php echo $p['name']; ?></option>
            <?php } ?>
        </select>

        <input type="submit" class="btn blue" value="Save">
    </form>
</div>
<?php } else { ?>
<div class="panel">
    <h3>Subscription Plan <a href="../sub-plans.php?edit=<?php echo $plan['id']; ?>">(<?php echo sanitize_html($plan['name']); ?>)</a></h3>

    <form action="" method="post">
        <p style="margin: 0 0 5px;">Set new plan:</p>
        <select class="fancy" name="groupId">
            <?php foreach ($plans as $p) { ?>
                <option value="<?php echo $p['assign']; ?>" <?php
                    if ($p['assign'] == $row->groupId) echo " selected";
                ?>><?php echo $p['name']; ?></option>
            <?php } ?>
        </select>

        <input type="submit" class="btn blue" value="Save">
    </form>
</div>
<?php } ?>
