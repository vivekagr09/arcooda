<?php
/******************************************************************************
 ** Title.........: Exchange Rate Updater
 ** Version.......:
 ** Author........: Stephen Jang / Bede Pavan
 ** Filename......: adminCurrency.php
 ** Last changed..: 29/09/03
 ** Notes.........: Provides functions for looking up exchange rates from an
 **                 external source and updating the database with it.
 *****************************************************************************/

$section_code = "OPTIONS";
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

//    $ExternalWebsite = "http://pacific.commerce.ubc.ca/xr/today.html";
$ExternalWebsite = "http://fx.sauder.ubc.ca/supplement.html";

function updateDatabase()
{

    //updates the exhange rates residing in table 'currency'
    global $ExternalWebsite, $DB;

    $ptrExternalSource = fopen($ExternalWebsite, 'r');

    if (!$ptrExternalSource) {
        //if no connection

        echo "Error:updateDatabase(): Failed to open connection to $ExternalWebsite<br />";
        return;
    }

    /*****************************************************************************************
    Code below is site specific
     *****************************************************************************************/

    $arrExchangeRates = [];
    //keep reading and look for currency code and exchange rates until done
    while (!feof($ptrExternalSource)) {
        $code    = "";
        $name    = "";
        $US_TO   = 0;
        $US_FROM = 0;
        $line    = "";
        $arrCode = [];
        $arrName = [];
        $arrUSto = [];

        // look for currency code in '>XXX<' format
        while (!preg_match("/>([A-Z][A-Z][A-Z])</i", $line, $arrCode) && !feof($ptrExternalSource)) {
            $line = fgets($ptrExternalSource, 1024);
        }

        //get rid of the '>,<' in the country codes
        preg_match("/([A-Z][A-Z][A-Z])/i", $arrCode[0], $arrCode);
        $code = $arrCode[0];

        $line = fgets($ptrExternalSource, 1024);
        // look for currency name in '&nbsp;<country name>&nbsp;' format
        while (!preg_match("/&nbsp;.+&nbsp;/", $line, $arrName) && !feof($ptrExternalSource)) {
            $line = fgets($ptrExternalSource, 1024);
        }

        //get rid of the '&nbsp;' in the country codes
        //$name =  ereg_replace("&nbsp;","",$arrName[0]);
        $name = str_replace("&nbsp;", "", $arrName[0]);
        $name = addslashes($name);

        $line = fgets($ptrExternalSource, 1024);
        // search for the canadian conversion rate and then ignore it
        // as we are only interested in USTO
        while (!preg_match("/\\d+\\.\\d+/", $line) && !feof($ptrExternalSource)) {
            $line = fgets($ptrExternalSource, 1024);
        }

        $line = fgets($ptrExternalSource, 1024);
        // search for USTO
        while (!preg_match("/\\d+\\.\\d+/", $line, $arrUSto) && !feof($ptrExternalSource)) {
            $line = fgets($ptrExternalSource, 1024);
        }

        if (!feof($ptrExternalSource)) {
            $US_TO   = $arrUSto[0];
            $US_FROM = round(1 / $US_TO, 4);
            //echo "CODE = $code US_TO = $US_TO US_FROM = $US_FROM<br />";
            $arrExchangeRates[] = [$code, $name, $US_TO, $US_FROM];
        }

    }

    // for each nations exchange rate update database
    for ($index = 0; $index < count($arrExchangeRates); $index++) {
        $array = $arrExchangeRates[$index];
        //putRatesIntoTable($array[0],$array[1],$array[2],$array[3]);
    }
    fclose($ptrExternalSource);
}

// inserts the exhange rates in table 'currency'
function putRatesIntoTable($code, $name, $usto, $usfrom)
{
    global $DB;

    $querySQL = sprintf(
        "UPDATE `highwaygroup`.`currency` SET `usto` = '%s', `usfrom` = '%s' WHERE `code` = '%s' LIMIT 1",
        $usto,
        $usfrom,
        $code
    );

    $DB->query($querySQL);
    //update the database
}

function showExchangeRates()
{
    global $DB;
?>
		<br />
		<table align="center" width="70%" border="0" cellspacing="1" cellpadding="3">
			<tr class="tableHeader">
				<td nowrap="nowrap">Currency</td>
				<td nowrap="nowrap">Code</td>
				<td nowrap="nowrap">To USD</td>
				<td nowrap="nowrap">From USD</td>
			</tr>
<?php
$toggle = true;
    $result = $DB->query("SELECT * FROM `highwaygroup`.`currency` ORDER BY `name`");
    while ($row = $DB->fetch_array($result)) {
        $class  = $toggle ? "tableRow" : "tableRowAlt";
        $toggle = !$toggle;
?>
			<tr class="<?=$class;?>">
				<td nowrap="nowrap"><?=stripslashes($row['name']);?></td>
				<td nowrap="nowrap"><?=strtoupper($row['code']);?></td>
				<td nowrap="nowrap"><?=$row['usfrom'];?></td>
				<td nowrap="nowrap"><?=$row['usto'];?></td>
			</tr>
<?php
}
    echo "</table><br />\r\n";
}

echo "<br /><font color=\"#FF0000\"><b>Updating exchange rates please wait...</b></font><br />\r\n";
updateDatabase();
echo "<br /><font color=\"#FF0000\"><b>Update complete. The new rates are shown below.</b></font><br />\r\n";
showExchangeRates();

include_once __DIR__ . '/footer.inc.php';
