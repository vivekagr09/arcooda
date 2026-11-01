<?php 
include_once __DIR__ . '/common.inc.php';
error_reporting(E_ALL);
ini_set('display_errors', '1');

$member = memberFromDatabase($_GET['id']);
if (!isset($member['id'])) {
    die('Not a valid request. Member not found.');
}
moduleLoad('member');
$expire_time = strtotime('+2 hours');
$randnum =  mt_rand(0, 2147483647);
memberSetValue($member['id'], 'rand_num', $randnum);
memberSetValue($member['id'], 'link_expire_time',$expire_time);
// Fetch the last created reset link from the database
$reset_link = relative_url(sprintf(
    '/account/autologin?member_id=%d&rand_num=%d',
    $member['id'],
    $randnum
));
// Fetch the last created reset link from the database

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Reset Password Link</title>
</head>
<body>
    <div class="container mt-4">
        <h3>Reset Password Link</h3>
        <p class="text-danger"><b>Only the last created link is valid.</b></p>
        <div class="form-group">
            <label for="resetLink"><b>Your Reset Link</b></label>
            <input type="text" class="form-control" id="resetLink" value="<?= htmlspecialchars($reset_link) ?>" readonly>
        </div>
        <button class="btn btn-primary" onclick="copyToClipboard()">Copy Link</button>
    </div>

    <script>
    function copyToClipboard() {
        var copyText = document.getElementById("resetLink");
        copyText.select();
        document.execCommand("copy");
        alert("Reset link copied to clipboard");
    }
    </script>
</body>
</html>