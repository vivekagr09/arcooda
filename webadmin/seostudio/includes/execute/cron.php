<?php

use Studio\Util\Ratelimit;

if (isset($_GET['check'])) die(" check ok ");

require_once dirname(dirname(__FILE__)) . "/init.php";

if ($studio->errors instanceof Studio\Base\ErrorHandler) {
    $old = $studio->errors->setMode('cron');
}

require dirname(dirname(__FILE__)) . "/update.php";
require dirname(dirname(__FILE__)) . "/itemupdate.php";

@ini_set('max_execution_time', 120);

# Clean rate limits

try {
    Ratelimit::cron();
}
catch (Exception $e) {}

# Update URL
# Only runs if you've joined the Google Network or enabled the cron service.
# (The URL to your Studio is required for these services to work - they have to connect to your app)

if ($studio->getopt("google-enabled") == "On" || $studio->getopt("cron-token") != "") {
    try {
        $lastReportTime = $studio->getopt('public-url-updated-at', 0);
        $lastReportURL = $studio->getopt('public-url-reported', '');
        $url = $studio->getopt("public-url");

        if ($lastReportURL !== $url || $lastReportTime < time() - (86400 * 7)) {
            $api->updateURL($url);
            $studio->setopt('public-url-updated-at', time());
            $studio->setopt('public-url-reported', $url);
        }
    }
    catch (Exception $e) {
        // do nothing...
    }
}

# Clean expired cache

$days = (int)$studio->getopt("cache-duration");
$expired = time() - (86400 * $days);
$studio->sql->query("DELETE FROM cache WHERE `time` < $expired;");

# Usage stats

$sent = "not sent (disabled)";
if ($studio->getopt('send-usage-info') == "On") {
    $lastUsageReport = $studio->getopt('last-uinfo-report');

    if ($lastUsageReport <= (time() - (86400*2.7))) {
        $usage = new Studio\Common\Usage();
        $usage->generate()->saveFile()->send();
        $sent = "sent";
    }
    else $sent = "not sent (delayed)";
}

# Check for updates

$speed = isset($_GET['fast']) ? 60 : 3600;
$studio->checkUpdates($speed);

# Perform automatic upgrades if enabled

$backups = ($studio->getopt("automatic-updates-backup") == "On");
$numUpdates = 0;
$successfulUpdates = 0;
$maxUpdatesPerRun = 15;
$extra = "";

if ($studio->getopt('automatic-updates') == "On") {
    $q = $studio->sql->query("SELECT * FROM updates WHERE updateStatus = 0 ORDER BY updateTime ASC"); //oldest first (have to go in order)

    while ($row = $q->fetch_array()) {
        if ($numUpdates > $maxUpdatesPerRun) continue;
        if (!empty($row['updateWarning'])) {
            $extra = ", halted automatic updates (one or more updates require review by admin)";
            break;
        }

        $update = new Update($row['token'], true);
        $success = $update->run($backups);
        $numUpdates++;

        if ($success) {
            $successfulUpdates++;

            $studio->addActivity(new Studio\Common\Activity(
                Studio\Common\Activity::SUCCESS,
                "Installed new update v" . $row['updateVersion'] . "."
            ));
        }
        else {
            $studio->addActivity(new Studio\Common\Activity(
                Studio\Common\Activity::ERROR,
                "Failed to install new update v" . $row['updateVersion'] . " due to an error."
            ));
        }
    }
}

$studio->getPluginManager()->call("cron_run");

echo $successfulUpdates . " successful updates, $numUpdates total updates, backups " . ($backups ? "enabled" : "disabled") . ", cache cleared with interval ($days days), usage report $sent" . $extra;

if (!isset($key)) $studio->setopt("cron-last-run", time());

if (isset($key)) {
    unlink("cron._{$key}.key");
}

// Delete usage history older than 90 days
// We'll limit the number of deletions per run to avoid locking the table for too long
$studio->sql->query("DELETE FROM `history` WHERE `useTime` < " . (time() - (86400 * 90)) . " LIMIT 5000");

$files = array();
$keys = array();
$deletions = array();

// Find all matching backup files
foreach (scandir($studio->bindir) as $file) {
    if (preg_match('/^backup-update-([a-f0-9]{13}-[a-f0-9]{18})-(\d+)\.zip$/', $file, $matches)) {
        $key = $matches[1];
        $timestamp = +$matches[2];
        $path = $studio->bindir . DIRECTORY_SEPARATOR . $file;
        $files[$path] = $timestamp;

        if (!array_key_exists($key, $keys)) {
            $keys[$key] = array();
        }

        $keys[$key][$path] = $timestamp;
    }
}

// Sort the array by value in ascending order
asort($files);

// Mark all files for deletion that are older than 60 days
foreach ($files as $path => $timestamp) {
    if ($timestamp < time() - (86400 * 60)) {
        $deletions[$path] = true;
    }
}

// For all keys with multiple backups, keep only the first and last
foreach ($keys as $key => $keyFiles) {
    asort($keyFiles);

    if (count($keyFiles) > 2) {
        foreach (array_slice(array_keys($keyFiles), 1, -1) as $path) {
            $timestamp = $keyFiles[$path];

            if ($timestamp < time() - (86400 * 2)) {
                $deletions[$path] = true;
            }
        }
    }
}

// If the total number of backups is over 100, delete all except the last 50
if (count($files) > 100) {
    foreach (array_slice(array_keys($files), 0, -100) as $path) {
        $deletions[$path] = true;
    }
}

// Delete all marked files
foreach ($deletions as $path => $true) {
    if (file_exists($path) && substr($path, -4) == '.zip') {
        @unlink($path);
    }
}

?>
