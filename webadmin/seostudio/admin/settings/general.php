<?php
use Studio\Tools\SpeedTest;

require "../includes/init.php";
$page->setPath("../../")->requirePermission('admin-access')->setPage(3)->setTitle("Configuration")->header();

if (isset($_POST['show-errors']) && !DEMO) {
    # Update config file if needed

    if ( ($_POST['show-errors'] == "On" && $studio->config['errors']['show'] == false) || ($_POST['show-errors'] == "Off" && $studio->config['errors']['show'] == true) ) {
        $conf = file_get_contents("../../config.php");
        if ($conf === false) $studio->showFatalError("Failed to read /config.php file. Please ensure it has the proper permissions.");

        $current = (($studio->config['errors']['show'] == true) ? "true" : "false");
        $new = (($_POST['show-errors'] == "On") ? "true" : "false");
        $newconf = str_replace('"show" => ' . $current, '"show" => ' . $new, $conf);

        $w = file_put_contents("../../resources/bin/config.backup-".time().".php", $conf);
        if ($w === false) $studio->showFatalError("Failed to make a configuration backup to /resources/bin/, please ensure the directory is writeable.");

        $w = file_put_contents("../../config.php", $newconf);
        if ($w === false) $studio->showFatalError("Failed to overwrite /config.php file, please ensure it is writeable. If you are going to change the chmod, only add writing permissions, do not modify the read permissions.");
    }

    $studio->setopt('timezone', $_POST['timezone']);

    $bools = array(
        "automatic-updates", "automatic-updates-backup", "send-errors", "send-usage-info", "errors-anonymous", "private-mode", "disable-home-page"
    );

    foreach ($bools as $bool) {
        if (!isset($_POST[$bool])) $studio->showFatalError("Missing POST parameter $bool");

        $val = $_POST[$bool];
        if ($val != "On" && $val != "Off") $val = "Off";

        $studio->setopt($bool, $val);
    }

    header("Location: general.php?success=1");
    die;
}

?>

<div class="heading">
    <h1>Configuration</h1>
    <h2>Change general settings</h2>
</div>

<form action="" method="post">
    <div class="panel v2 back">
        <a href="../settings.php">
            <i class="material-icons">&#xE5C4;</i> Back
        </a>
    </div>
    <div class="save-container">
        <div class="saveable">
            <div class="panel v2">
                <div class="setting-group">
                    <h3>Studio</h3>

                    <div class="setting toggle">
                        <label data-switch="Ctl_Switch_01">Install updates automatically <span class="help tooltip" title="The application will automatically be updated to the latest version. This might overwrite any changes you make to the script's code."><i class="material-icons">&#xE8FD;</i></span></label>

                        <div class="switch" id="Ctl_Switch_01">
                            <input type="hidden" name="automatic-updates" value="<?php echo $studio->getopt("automatic-updates"); ?>">
                            <div class="handle"></div>
                        </div>
                    </div>

                    <div class="setting toggle">
                        <label data-switch="Ctl_Switch_02">Create backups before installing updates <span class="help tooltip" title="If enabled, a backup .zip file containing the previous version of all files modified by updates will be saved to the disk, in case something goes wrong or gets overwritten accidentally."><i class="material-icons">&#xE8FD;</i></span></label>

                        <div class="switch" id="Ctl_Switch_02">
                            <input type="hidden" name="automatic-updates-backup" value="<?php echo $studio->getopt("automatic-updates-backup"); ?>">
                            <div class="handle"></div>
                        </div>
                    </div>

                    <div class="setting toggle">
                        <label data-switch="Ctl_Switch_06">Send anonymous usage info <span class="help tooltip" title="Please consider keeping this enabled. Sends periodic usage telemetry to the developer. This telemetry includes basic server information (such as PHP and extension versions) and the number of times each tool is used. Sending this information allows the developer to consider your software versions in future updates, and to further improve the features that you and your users enjoy the most. This non-sensitive data is anonymized on our end, but could in theory be traced back to you by your server's IP address at the time of receipt."><i class="material-icons">&#xE8FD;</i></span></label>

                        <div class="switch" id="Ctl_Switch_06">
                            <input type="hidden" name="send-usage-info" value="<?php echo $studio->getopt("send-usage-info"); ?>">
                            <div class="handle"></div>
                        </div>
                    </div>

                    <div class="setting toggle">
                        <label data-switch="Ctl_Switch_07a">Disable the home page <span class="help tooltip" title="When enabled, the script's home page will be replaced with the tools page."><i class="material-icons">&#xE8FD;</i></span></label>

                        <div class="switch" id="Ctl_Switch_07a">
                            <input type="hidden" name="disable-home-page" value="<?php echo $studio->getopt("disable-home-page", "Off"); ?>">
                            <div class="handle"></div>
                        </div>
                    </div>

                    <div class="setting toggle">
                        <label data-switch="Ctl_Switch_07">Prevent search indexing <span class="help tooltip" title="When enabled, search engines will be asked to exclude the website from their results. This only affects pages within the script itself and will not affect other scripts or pages on your domain name."><i class="material-icons">&#xE8FD;</i></span></label>

                        <div class="switch" id="Ctl_Switch_07">
                            <input type="hidden" name="private-mode" value="<?php echo $studio->getopt("private-mode", "Off"); ?>">
                            <div class="handle"></div>
                        </div>
                    </div>
                </div>

                <div class="setting-group">
                    <h3>Errors</h3>

                    <div class="setting toggle">
                        <label data-switch="Ctl_Switch_04">Send errors to the developer <span class="help tooltip" title="When an error occurs in the script, it will be sent to the developer so that it may be fixed in future versions. If you don't want to send errors, or are modifying the script, you should turn this option off."><i class="material-icons">&#xE8FD;</i></span></label>

                        <div class="switch" id="Ctl_Switch_04">
                            <input type="hidden" name="send-errors" value="<?php echo $studio->getopt("send-errors"); ?>">
                            <div class="handle"></div>
                        </div>
                    </div>

                    <div class="setting toggle">
                        <label data-switch="Ctl_Switch_05">Anonymous error reports <span class="help tooltip" title="If enabled, any error reports your script sends to the developer will be anonymous. If disabled, the developer will be sent your Envato username so he can see the error if you contact support."><i class="material-icons">&#xE8FD;</i></span></label>

                        <div class="switch" id="Ctl_Switch_05">
                            <input type="hidden" name="errors-anonymous" value="<?php echo $studio->getopt("errors-anonymous"); ?>">
                            <div class="handle"></div>
                        </div>
                    </div>

                    <div class="setting toggle">
                        <label data-switch="Ctl_Switch_03">Show errors <span class="help tooltip" title="If enabled, any errors the script encounters will be shown in full detail. This should be turned off in production."><i class="material-icons">&#xE8FD;</i></span></label>

                        <div class="switch" id="Ctl_Switch_03">
                            <input type="hidden" name="show-errors" value="<?php if ($studio->config['errors']['show'] == true) echo "On"; else echo "Off"; ?>">
                            <div class="handle"></div>
                        </div>
                    </div>
                </div>

                <div class="setting-group">
                    <h3>Dashboard</h3>

                    <div class="setting select">
                        <label for="Ctl_Dropdown_01">Timezone for analytics <span class="help tooltip" title="Set this to your local or nearest timezone to get accurate time calculations for the analytics shown on the admin dashboard."><i class="material-icons">&#xE8FD;</i></span></label>

                        <div class="dropdown">
                            <select id="Ctl_Dropdown_01" name="timezone">
                                <?php
                                $current = $studio->getopt('timezone', 'UTC');
                                $timezones = timezone_identifiers_list();
                                $now = time();

                                foreach ($timezones as $index => $name) {
                                ?>
                                <option value="<?php echo $name; ?>" <?php if ($name == $current) echo "selected"; ?>><?php echo $name; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="save">
            <div class="save-box">
                <button type="submit">
                    <span>Save changes</span>
                    <span>Saved</span>
                    <img src="../../resources/images/load32.gif" width="16px" height="16px">
                </button>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
    $("input[name=url]").val(window.location.href);
</script>

<?php
$page->footer();
?>
