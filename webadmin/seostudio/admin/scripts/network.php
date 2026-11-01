<?php

use Studio\Util\TrustedNetwork;
use Studio\Util\TrustedNetworkAddress;

require "../includes/init.php";

$page->setPath("../../")->requirePermission('admin-access')->setPage(0)->header();

$client = TrustedNetwork::getEffectiveClientIP();
$connection = TrustedNetwork::getEffectiveConnectionIP();
?>

<div class="script">
    <h1>Network analysis</h1>
    <h2>Active headers</h2>

    <table>
        <tr>
            <th>Header</th>
            <th>Value</th>
        </tr>
        <?php
            $headers = ['REMOTE_ADDR', 'HTTP_X_FORWARDED_FOR', 'HTTP_CF_CONNECTING_IP'];

            foreach ($headers as $name) {
                if (!isset($_SERVER[$name])) continue;
                $value = $_SERVER[$name];
        ?>
        <tr>
            <td><?php echo sanitize_html($name); ?></td>
            <td><code><?php echo sanitize_html($value); ?></code></td>
        </tr>
        <?php
            }
        ?>
    </table>

    <h2>Active trusted proxies</h2>

    <table>
        <tr>
            <th>Subnet</th>
            <th>Bits</th>
            <th>Type</th>
        </tr>
        <?php
            foreach (TrustedNetwork::getTrustedAddresses() as $address) {
                $type = strpos($address->input, ':') !== false ? 'IPv6' : 'IPv4';
                $subnet = $address->getSubnet();
                $bits = $address->getBits();
        ?>
        <tr>
            <td><code><?php echo sanitize_html($subnet); ?></code></td>
            <td><code>/<?php echo sanitize_html($bits); ?></code></td>
            <td><?php echo sanitize_html($type); ?></td>
        </tr>
        <?php
            }

            if (count(TrustedNetwork::getTrustedAddresses()) === 0) {
                echo "<tr><td colspan=\"3\">No trusted proxies have been configured.</td></tr>";
            }
        ?>
    </table>

    <h2>Client address</h2>
    <p>This IP address is what the script will use during its regular operation. You should ensure this is correct.</p>
    <ul>
        <li>Your current detected IP address is: <strong><?php echo sanitize_html($client['result']); ?></strong></li>
        <li>The method used to compute it was: <strong><?php echo sanitize_html($client['reason']); ?></strong></li>
        <li>Check <a href="https://whatismyipaddress.com/" target="_blank" rel="noopener nofollow">whatismyipaddress.com</a> to see if this is correct.</li>
    </ul>

    <?php
        $ip = sanitize_html($client['result']);
        $suggested = false;
        $privates = [
            new TrustedNetworkAddress('10.0.0.0/8'),
            new TrustedNetworkAddress('172.16.0.0/12'),
            new TrustedNetworkAddress('192.168.0.0/16')
        ];

        foreach ($privates as $address) {
            if ($address->matches($ip)) {
                $long = ip2long($ip);
                $long >>= 8;
                $long <<= 8;
                $cidr = long2ip($long) . '/24';
    ?>
    <div class="alert alert-danger">
        <strong><?php echo $ip; ?></strong> is a private IP address. If this is a public website, you will most likely want to add
        this address to your "trusted proxies" list. If the address can change (e.g. when running inside a docker
        container), then use a range such as <strong><?php echo $cidr; ?></strong>.
    </div>
    <?php
                break;
            }
        }
    ?>

    <h2>Connection address</h2>
    <p>
        This is the computed IP address of the underlying connection after applying trusted proxies. It will usually
        match the above client address, but if you're using Cloudflare, then it must be the IP address of a Cloudflare
        edge node for things to work properly.
    </p>
    <ul>
        <li>The IP address of the connection is: <strong><?php echo sanitize_html($connection['result']); ?></strong></li>
        <li>The method used to compute it was: <strong><?php echo sanitize_html($connection['reason']); ?></strong></li>
        <li>This IP address <?php
            if (TrustedNetwork::isCloudflareAddress($connection['result'])) {
                echo "is a valid address within the Cloudflare network.";
            }
            else {
                echo "does not belong to the Cloudflare network.";
            }
        ?></li>
    </ul>

    <?php
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && $client['result'] !== $_SERVER['HTTP_CF_CONNECTING_IP']) {
    ?>
    <div class="alert alert-danger">
        The <code>CF-Connecting-IP</code> header has been ignored by the script because it could not confirm that the
        connection actually came from Cloudflare. The "connection address" above must point to an IP address in the
        <a href="https://www.cloudflare.com/ips/" target="_blank" rel="noopener nofollow">Cloudflare network</a>.
        Is <strong><?php echo sanitize_html($connection['result']); ?></strong> a trusted reverse proxy?
    </div>
    <?php } else if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) { ?>
        <?php if (TrustedNetwork::isCloudflareAddress($connection['result'])) { ?>
            <div class="alert alert-success">
                Great! The connection address is trusted so the <code>CF-Connecting-IP</code> header has been applied.
            </div>
        <?php } else { ?>
            <div class="alert alert-success">
                Great! It looks like your webserver is already configured to detect and restore the client's IP address
                from Cloudflare, so further no action is necessary.
            </div>
        <?php } ?>
    <?php } ?>

</div>

<?php
$page->footer();
