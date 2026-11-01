<?php
require "../includes/init.php";
$page->setPath("../../")->requirePermission('admin-access')->setPage(3)->setTitle("Security")->header();

if (isset($_POST['disallow-local-hostnames']) && !DEMO) {
    $bools = array(
        'allow-local-hostnames',
        'trusted-network-cloudflare'
    );

    $ints = array(
        "webrequest-download-limit",
        "webrequest-timeout"
    );

    $strings = array(
        'trusted-network-proxies',
        'curl-encoding-override'
    );

	if (isset($_POST['disallow-local-hostnames'])) {
		$_POST['allow-local-hostnames'] = $_POST['disallow-local-hostnames'] === 'On' ? 'Off' : 'On';
	}

    foreach ($bools as $bool) {
        if (!isset($_POST[$bool])) $studio->showFatalError("Missing POST parameter $bool");

        $val = $_POST[$bool];
        if ($val != "On" && $val != "Off") $val = "Off";

        $studio->setopt($bool, $val);
    }

    foreach ($ints as $int) {
        if (!isset($_POST[$int])) $studio->showFatalError("Missing POST parameter $int");

        $val = $_POST[$int];
        if (!is_numeric($val)) $val = 0;

        $studio->setopt($int, $val);
    }

    foreach ($strings as $string) {
        if (!isset($_POST[$string])) $studio->showFatalError("Missing POST parameter $string");

        $val = $_POST[$string];
        if (!is_string($val)) $val = 0;

        $studio->setopt($string, $val);
    }

    header("Location: security.php?success=1");
    die;
}

?>

<div class="heading">
    <h1>Security</h1>
    <h2>Change security settings</h2>
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
                    <h3>Tools</h3>

					<div class="setting toggle">
						<label data-switch="Ctl_Switch_2A">Disallow local network access <span class="help tooltip" title="This will prevent users from entering local hostnames, private IP addresses, or public hostnames which resolve to private IP addresses, into the script, which would allow an attacker to use these tools to infiltrate any private services in the network. If you do want to run the tools on local websites, disable this, but make sure you know what you're doing first. (Recommended value: ON)"><i class="material-icons">&#xE8FD;</i></span></label>

						<div class="switch" id="Ctl_Switch_2A">
							<input type="hidden" name="disallow-local-hostnames" value="<?php echo $studio->getopt("allow-local-hostnames", 'Off') === 'Off' ? 'On' : 'Off'; ?>">
							<div class="handle"></div>
						</div>
					</div>
                    <div class="setting int">
                        <label for="Ctl_Number_02">Resource download size limit (MB) <span class="help tooltip" title="The maximum amount of data the script will download from a single resource in megabytes. This prevents, for example, someone from entering a 100 MB file into the tools to attack your memory and bandwidth. (Recommended value: 4-16)"><i class="material-icons">&#xE8FD;</i></span></label>

                        <div class="int">
                            <input type="number" id="Ctl_Number_02" step="any" name="webrequest-download-limit" value="<?php echo $studio->getopt("webrequest-download-limit", 8); ?>">
                        </div>
                    </div>
                    <div class="setting int">
                        <label for="Ctl_Number_03">Request timeout (seconds) <span class="help tooltip" title="The maximum number of seconds to wait before timing out for a single outbound request. (Recommended value: 25-60)"><i class="material-icons">&#xE8FD;</i></span></label>

                        <div class="int">
                            <input type="number" id="Ctl_Number_03" step="any" name="webrequest-timeout" value="<?php echo $studio->getopt("webrequest-timeout", 30); ?>">
                        </div>
                    </div>

                    <?php if (ini_get('max_execution_time') !== '0') { ?>
                        <div class="error-label">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-circle" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <circle cx="12" cy="12" r="9" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            <span>
                                Your web host does not allow us to automatically increase the execution time limit for PHP scripts.
                                As such, all tools in the script currently have an enforced time limit of <?php echo ini_get('max_execution_time'); ?> seconds.
                                Please ask your web host to increase this if you feel it is too short. In general, we recommend a time limit of
                                at least 60 seconds.
                            </span>
                        </div>
                    <?php } ?>
                </div>
                <div class="setting-group">
                    <h3>Trusted network</h3>

					<div class="setting toggle">
						<label data-switch="Ctl_Switch_3A">Cloudflare proxying support <span class="help tooltip" title="When enabled, we'll automatically detect when the remote IP address is from the Cloudflare network, and will use the 'CF-Connecting-IP' header to determine the client's real IP address. (Recommended value: ON)&#013;&#013;NOTE: If you're running the script behind additional reverse proxies (in addition to Cloudflare), you must enter the IP addresses of the proxies in the 'trusted proxies' list below for this to work. An example scenario for this is if the script is running inside docker, so the resulting traffic chain looks like [cloudflare proxy] -> [nginx proxy] -> [docker container].&#013;&#013;NOTE2: If you are using a different proxy network than Cloudflare, you must enter all of their edge IP addresses in the 'trusted proxies' list below for proper IP detection. When traffic comes from those IPs, the 'X-Forwarded-For' header will be parsed."><i class="material-icons">&#xE8FD;</i></span></label>

						<div class="switch" id="Ctl_Switch_3A">
							<input type="hidden" name="trusted-network-cloudflare" value="<?php echo $studio->getopt("trusted-network-cloudflare", 'On'); ?>">
							<div class="handle"></div>
						</div>
					</div>
                    <div class="setting textarea">
                        <div style="float: right; padding: 18px 15px 0;">
                            <a href="../scripts/network.php" target="_blank">Click here to analyze</a>
                        </div>
                        <label for="Ctl_TextBox_03">Trusted proxies (one per line) <span class="help tooltip" title="Enter IP addresses, one per line, of reverse proxies that you trust. CIDR notation is allowed (such as 192.168.0.0/24).&#013;&#013;This will allow the script to accurately detect the IP address of the user when your website is running behind a reverse proxy. If you aren't sure what this means, it probably doesn't apply to you.&#013;&#013;Implementation: If the script detects the remote address to be one of these IP addresses, it will read the 'X-Forwarded-For' header. Then, it will look for the right-most IP address that is not in your trusted proxy list, and treat that as the client IP."><i class="material-icons">&#xE8FD;</i></span></label>

                        <div class="text">
                            <textarea id="Ctl_TextBox_03" name="trusted-network-proxies" rows="4" class="editor"><?php echo $studio->getopt("trusted-network-proxies", ""); ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="setting-group">
                    <h3>Networking</h3>

                    <div class="setting int">
                        <label for="Ctl_String_01">cURL encoding override <span class="help tooltip" title="Manually configures cURL encodings. Must be a comma-delimited list such as 'deflate, gzip, br'. Configuring this incorrectly can break tools and script updates. Set to blank for default."><i class="material-icons">&#xE8FD;</i></span></label>

                        <div class="int">
                            <input type="text" id="Ctl_String_01" name="curl-encoding-override" value="<?php echo $studio->getopt("curl-encoding-override", ''); ?>">
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

<script>
	var textarea = $('.editor');

	var headEditor = CodeMirror.fromTextArea(textarea[0], {
		lineNumbers: true,
		mode: 'text',
		lineSeparator: '\n',
		indentUnit: 4
	});

	headEditor.on('change', function () {
		textarea.eq(0).val(headEditor.doc.getValue());
	});
</script>

<?php
$page->footer();
?>
