<?php

use Studio\Util\Algorithm\Punycode;

require "../../includes/init.php";
$page->setPath("../../../")->requirePermission('admin-access')->setPage(9)->header('users');

if (isset($_POST['doClearData'])) {
    $studio->sql->query("TRUNCATE `history`;");
}

# Get total number of rows

$q = $studio->sql->query("SELECT COUNT(*) FROM history");
$r = $q->fetch_array();
$total = $r[0];

# Pagination

$curPage = ((isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1);
$pagination = new Studio\Display\Pagination($curPage, 20, $total);
$p = $pagination->getQuery();

# Sorting

$sort = "id DESC";

# Run the query

$q = $studio->sql->query("SELECT * FROM history ORDER BY $sort $p");
$users = array();
?>

<section>
    <div class="table-heading">
        <div class="title">
            <h1>Tool usage</h1>
            <p>
                <?php echo $total; ?>
                result<?php echo $total !== 1 ? 's' : ''; ?>
            </p>
        </div>
        <div class="actions align-self-end">
			<?php
				$pagination->show("m-0");
			?>
        </div>
    </div>

    <div class="table-responsive-md">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="60px" class="center">Id</th>
                    <th>User</th>
                    <th>Tool</th>
                    <th>Attributes</th>
                    <th>Domain</th>
                    <th>Extra info</th>
                    <th width="150px" class="right">Time</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $q->fetch_array()) {
                ?>
                <tr>
                    <td class="center"><?php echo $row['id']; ?></td>
                    <td>
                        <?php

                        if ($row['userId'] > 0) {
                            $userId = $row['userId'];
                            if (!isset($users[$userId])) {
                                $o = $studio->sql->query("SELECT email, id FROM accounts WHERE id='$userId'");

                                if ($o->num_rows == 0) echo "Unknown";
                                else {
                                    $r = $o->fetch_array();
                                    $users[$userId] = $r['email'];

                                    echo "<a href=\"../user.php?id=$userId\">" . $r['email'] . "</a>";
                                }
                            }
                            else echo "<a href=\"../user.php?id=$userId\">" . $users[$userId] . "</a>";
                        }
                        else {
                            if (DEMO) echo 'Demo guest';
                            else echo $row['address'] . " (guest)";
                        }

                        ?>
                    </td>
                    <td><?php echo $row['toolId']; ?></td>
                    <td>
                        <?php if (!DEMO && !is_null($row['embed_url'])) { ?>
                            <?php if (substr($row['embed_url'], -3) !== '...' && $row['embed_url'] !== 'Unknown') { ?>
                                <a href="<?php echo sanitize_attribute($row['embed_url']); ?>" target="_blank" rel="noopener nofollow">
                                    <span class="badge bg-primary" title="Embedded on <?php echo sanitize_attribute($row['embed_url']); ?>">
                                        Embedded
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-external-link" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M11 7h-5a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-5" />
                                            <line x1="10" y1="14" x2="20" y2="4" />
                                            <polyline points="15 4 20 4 20 9" />
                                        </svg>
                                    </span>
                                </a>
                            <?php } else { ?>
                                <span class="badge bg-primary" title="Embedded on <?php echo sanitize_attribute($row['embed_url']); ?>">Embedded</span>
                            <?php } ?>
                        <?php } ?>
                    </td>
                    <td><?php echo DEMO ? 'hidden.xyz' : Punycode::toUnicode($row['domain']); ?></td>
                    <td><?php echo DEMO ? 'Tool input hidden on demo' : sanitize_html($row['data']); ?></td>
                    <td>
                        <div class="time right">
                            <?php echo (new \Studio\Display\TimeAgo($row['useTime']))->get(); ?>
                            <span data-time="<?php echo $row['useTime']; ?>"><i class="material-icons">access_time</i></span>
                        </div>
                    </td>
                </tr>
                <?php
                }

				if ($q->num_rows === 0) {
				?>
				<tr>
					<td colspan="6" class="center">
						Nothing to show.
					</td>
				</tr>
				<?php
				}
                ?>
            </tbody>
        </table>
    </div>

    <div class="d-flex">
        <div>
            <a class="btn btn-sm btn-danger" href="#" style="margin-top: 10px;" onclick="doConfirmClearHistory(event)">
                Clear all history
            </a>
        </div>
        <div class="flex-grow-1">
            <?php
            $pagination->show("right no-margin-bottom");
            ?>
        </div>
    </div>
</div>

<form id="clearForm" action="" method="post">
    <input type="hidden" name="doClearData" value="1">
</form>

<script>
    function doConfirmClearHistory(e) {
        e.preventDefault();

        if (confirm('Are you sure? This will permanently delete ALL usage history, and it might take a minute to complete.')) {
            document.getElementById('clearForm').submit();
        }
    }
</script>

<?php
$page->footer();
?>
