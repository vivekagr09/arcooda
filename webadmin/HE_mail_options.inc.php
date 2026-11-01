<!-- BUSINESS TYPES -->
<?php
?>
		<table width="100%" border="0">
          <tr>
            <td><font face="Verdana, Arial, Helvetica, sans-serif" SIZE="2"><B>Please
              choose which options best describe you</B></FONT></TD>
          </TR>
          <TR>
            <TD>
              <table width="100%" align="center">
<?php
$statement = "SELECT * FROM business_types";
$results   = $DB->query($statement);
$count     = 0;

while ($row = $DB->fetch_array($results)) {
    if ($count % 2 == 0) {
        echo "<TR>";
    }

    echo '<TD WIDTH="50%" nowrap>';
    echo "<INPUT TYPE=\"checkbox\" NAME=\"types[$row[id]]\" VALUE=\"1\"";
    if (1 == $types[$row[id]]) {
        echo "checked";
    }

    echo ">\n";
    echo "<FONT FACE=\"Verdana, Arial, Helvetica, sans-serif\" SIZE=\"2\">$row[type]</FONT>\n";
    echo "</TD>\n";

    if ($count % 2 != 0) {
        echo "</TR>";
    }

    $count++;
}

?>
              </TABLE>
            </TD>
          </TR>
        </TABLE>
<!-- //business types -->

<br><br>
<!-- INTERESTS -->
        <TABLE width="100%" BORDER="0">
          <TR>
            <TD><FONT FACE="Verdana, Arial, Helvetica, sans-serif" SIZE="2"><B>I
              am interested in</B></FONT></TD>
          </TR>
          <TR>
            <TD>

              <TABLE WIDTH="100%" ALIGN="CENTER">
<?php
$statement = "SELECT * FROM business_interests";
$results   = $DB->query($statement);
$count     = 0;
while ($row = $DB->fetch_array($results)) {
    if ($count % 2 == 0) {
        echo "<TR>";
    }

    echo '<TD WIDTH="50%" nowrap>';
    echo "<INPUT TYPE=\"checkbox\" NAME=\"interests[$row[id]]\" VALUE=\"1\"";
    if (1 == $interests[$row[id]]) {
        echo "checked";
    }

    echo ">\n";
    echo "<FONT FACE=\"Verdana, Arial, Helvetica, sans-serif\" SIZE=\"2\">$row[interest]</FONT>\n";
    echo "</TD>\n";

    if ($count % 2 != 0) {
        echo "</TR>";
    }

    $count++;
}

?>
              </TABLE>
            </TD>
          </TR>
        </TABLE>
<!-- //INTERESTS -->