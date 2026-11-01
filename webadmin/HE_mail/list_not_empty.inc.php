<?php
global $DB;

?>
<P>The temporay list create didn't finish sending. That is, there was an error
  in sending the emails previously.</P>
<UL>
  <LI><A HREF="HE_mail.php?action=send&resume=yes">Click here to resume sending using the
    last created email message</A></LI>
  <LI><A HREF="HE_mail.php?action=reset">Click here to clear the list.</A></LI>
</UL>

Following is a list of the states:<br><br>
<UL>
<?php
if ($result = $DB->query(sprintf(
    "SELECT `response`, COUNT(*) AS `num` FROM `HE_mailout` GROUP BY `response`"
))) {
    while ($row = $DB->fetch_assoc($result)) {
        $response = $row['response'];
        if ('' == $response) {
            $response = 'Pending';
        }
        echo '<li>' . $response . ' : ' . $row['num'] . '</li>';
    }
    $DB->free_result($result);
}

?>
</UL>
Following is a list of the variables:<br><BR><hr>
<?php
if ($result = $DB->query(sprintf(
    "SELECT * FROM `HE_mailout_vars`"
))) {
    while ($row = $DB->fetch_assoc($result)) {
        echo '<b>' . $row['var'] . ':</b><pre>' . $row['param'] . '</pre><hr />';
    }
    $DB->free_result($result);
}
