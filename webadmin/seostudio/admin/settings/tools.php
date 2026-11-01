<?php
use Studio\Tools\SpeedTest;

require "../includes/init.php";
$page->setPath("../../")->requirePermission('admin-access')->setPage(3)->setTitle("Tools")->header();

if (isset($_POST['speedtest-default-region']) && !DEMO) {
    $studio->setopt('speedtest-default-region', $_POST['speedtest-default-region']);
    $studio->setopt('tools-default-country', $_POST['tools-default-country']);

    $bools = array(
        'show-tools-without-site', 'allow-siteless-tools',
        'experimental-tool-design', 'no-cookie-siteselector',
        'disable-result-favicons', 'disable-tool-usage-history'
    );

    foreach ($bools as $bool) {
        if (!isset($_POST[$bool])) $studio->showFatalError("Missing POST parameter $bool");

        $val = $_POST[$bool];
        if ($val != "On" && $val != "Off") $val = "Off";

        $studio->setopt($bool, $val);
    }

    if ($_POST['show-tools-without-site'] == 'On') {
        $studio->setopt('experimental-tool-design', 'On');
    }

    header("Location: tools.php?success=1");
    die;
}

?>

<div class="heading">
    <h1>Tools</h1>
    <h2>Change tool settings</h2>
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
                    <h3>General</h3>

                    <div class="setting toggle">
                        <label data-switch="Ctl_Switch_061">Show tools when a site is not selected</label>

                        <div class="switch" id="Ctl_Switch_061">
                            <input type="hidden" name="show-tools-without-site" value="<?php echo $studio->getopt("show-tools-without-site"); ?>">
                            <div class="handle"></div>
                        </div>
                    </div>

                    <div class="setting toggle">
                        <label data-switch="Ctl_Switch_07">Allow some tools to be used without selecting a site <span class="help tooltip" title="Some tools may not require a site to work, such as the keyword research tool. If you enable this option, these tools will work without selecting a site."><i class="material-icons">&#xE8FD;</i></span></label>

                        <div class="switch" id="Ctl_Switch_07">
                            <input type="hidden" name="allow-siteless-tools" value="<?php echo $studio->getopt("allow-siteless-tools"); ?>">
                            <div class="handle"></div>
                        </div>
                    </div>

                    <div class="setting toggle">
                        <label data-switch="Ctl_Switch_08">Enable fast tool switching <span class="help tooltip" title="Changes the design of tool pages to let users easily use the tool on another site or quickly switch to a different tool."><i class="material-icons">&#xE8FD;</i></span></label>

                        <div class="switch" id="Ctl_Switch_08">
                            <input type="hidden" name="experimental-tool-design" value="<?php echo $studio->getopt("experimental-tool-design"); ?>">
                            <div class="handle"></div>
                        </div>
                    </div>

					<div class="setting toggle">
						<label data-switch="Ctl_Switch_09">Disable cookies for site selection <span class="help tooltip" title="The script stores the user's current selected website using a cookie. Enabling this option will prevent that behavior, and instead use query parameters to track the website. Warning: This will degrade user experience."><i class="material-icons">&#xE8FD;</i></span></label>

						<div class="switch" id="Ctl_Switch_09">
							<input type="hidden" name="no-cookie-siteselector" value="<?php echo $studio->getopt("no-cookie-siteselector", 'Off'); ?>">
							<div class="handle"></div>
						</div>
					</div>

					<div class="setting toggle">
						<label data-switch="Ctl_Switch_11">Disable tool usage tracking <span class="help tooltip" title="When enabled, the script will stop recording tool usage and inputs to the database. You will still need to manually clear existing usage data from the [Admin > Users > Tool usage] page."><i class="material-icons">&#xE8FD;</i></span></label>

						<div class="switch" id="Ctl_Switch_11">
							<input type="hidden" name="disable-tool-usage-history" value="<?php echo $studio->getopt("disable-tool-usage-history", 'Off'); ?>">
							<div class="handle"></div>
						</div>
					</div>
				</div>

                <div class="setting-group">
                    <h3>Defaults</h3>

					<div class="setting select">
						<label for="Ctl_Dropdown_02">Default speed test location <span class="help tooltip" title="These are the locations currently offered by the upstream speed test service (pingdom.com)."><i class="material-icons">&#xE8FD;</i></span></label>

						<div class="dropdown">
							<select id="Ctl_Dropdown_02" name="speedtest-default-region">
								<?php
								$tool = new SpeedTest();
								$regions = $tool->getRegions();

								$current = $studio->getopt('speedtest-default-region');
								if (!$current) $current = 'us-east-1';

								foreach ($regions as $name => $id) {
								?>
								<option value="<?php echo $id; ?>" <?php if ($id == $current) echo "selected"; ?>><?php echo $name; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>

					<div class="setting select">
						<label for="Ctl_Dropdown_020">Default country <span class="help tooltip" title="Tools which require a country selection will select this as the default."><i class="material-icons">&#xE8FD;</i></span></label>

						<div class="dropdown">
							<select id="Ctl_Dropdown_020" name="tools-default-country">
								<?php
								$path = $studio->bindir . '/countries/en.json';
								$countries = json_decode(file_get_contents($path), true)['countries'];

								$current = $studio->getopt('tools-default-country');
								if (!$current) $current = 'US';

								foreach ($countries as $code => $name) {
									if (is_array($name)) {
										$name = $name[0];
									}

								?>
								<option value="<?php echo $code; ?>" <?php if ($code == $current) echo "selected"; ?>><?php echo $name; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				</div>

                <div class="setting-group">
                    <h3>Rendering</h3>
                    <div class="setting toggle">
                        <label data-switch="Ctl_Switch_09a">Disable favicons in tool results</label>

                        <div class="switch" id="Ctl_Switch_09a">
                            <input type="hidden" name="disable-result-favicons" value="<?php echo $studio->getopt("disable-result-favicons", 'Off'); ?>">
                            <div class="handle"></div>
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
