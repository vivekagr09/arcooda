#!/usr/bin/env php
<?php
include_once __DIR__ . '/common.inc.php';

global $DB;

/**
 * Undocumented function
 *
 * @param string $content
 * @param int    $table_id
 *
 * @return string[][]
 */
function read_table_from_content($content, $table_id)
{
    /**
     * Remove html tag attributes, they don't mean anything here
     */
    $content = preg_replace('/(<[A-Za-z]+) [^>]+(>)/', '$1$2', $content);

    /**
     * Remove any html entities
     */
    $content = preg_replace('/&[a-z]{3,6};/', '', $content);
    $content = preg_replace('/&#[0-9]+;/', '', $content);

    /**
     * Remove white-space characters, our data should still be seperated by tags
     */
    $content = preg_replace('/[\\0-\\x20]+/', '', $content);

    /**
     * Fetch the nth table in the content
     */
    $tables = preg_split('/<table>/i', $content);
    if ($table_id >= 0) {
        $content = "<table>" . $tables[$table_id];
    } else {
        $content = "<table>" . $tables[count($tables) + $table_id];
    }

    /**
     * Trim the table
     */
    $content = preg_split('/<\\/table>/i', $content);
    $content = reset($content) . "</table>";

    /**
     * Format each row onto a new line
     */
    $content = preg_replace('/<tr>/i', "\n<tr>", $content);

    /**
     * Split each cell by tabs
     */
    $content = preg_replace('/<(t[dh])>/i', "\t" . '<$1>', $content);

    /**
     * We now have a table ready to convert to plain text
     */
    $content = strip_tags($content);

    $result = explode("\n", $content);
    foreach ($result as $k => $v) {
        $result[$k] = explode("\t", $v);
    }

    return $result;
}

/**
 * Undocumented function
 *
 * @param string $file_name
 *
 * @return string
 */
function fetch_file($file_name)
{
    $ch = curl_init($file_name);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36',
        CURLOPT_HTTPHEADER => [
            'Cache-Control: no-cache',
            'Pragma: no-cache'
        ]
    ]);

    $result = curl_exec($ch);

    curl_close($ch);

    return $result;
}

$currencies = array();

//$ExternalWebsite = "http://www.x-rates.com/cgi-bin/cgicalc.cgi?value=1&base=USD";
//$ExternalWebsite = "http://fx.sauder.ubc.ca/supplement.html";
//$ExternalWebsite = "http://fx.sauder.ubc.ca/today.html";
//$ExternalWebsite = "http://www.commbank.com.au/personal/other/foreignexchangerates.asp";

$content = fetch_file("http://www.x-rates.com/cgi-bin/cgicalc.cgi?value=1&base=USD");
if ("" != $content) {
    $content = preg_replace("@<a href='https://www.x-rates.com/graph/\\?from=USD&amp;to=[A-Z][A-Z][A-Z]'>(.*)</a>@", "$1", $content);
    $content = preg_replace("@<a href='https://www.x-rates.com/graph/\\?from=([A-Z][A-Z][A-Z])&amp;to=USD'>.*</a>@", "$1", $content);
    $table   = read_table_from_content($content, 1);
    array_shift($table);
    array_shift($table);
    foreach ($table as $row) {
        if (!array_key_exists($row[3], $currencies)) {
            $currencies[$row[3]] = $row[2];
        }
    }
    $table = read_table_from_content($content, 2);
    array_shift($table);
    array_shift($table);
    foreach ($table as $row) {
        if (!array_key_exists($row[3], $currencies)) {
            $currencies[$row[3]] = $row[2];
        }
    }
}

/*
$content = fetch_file("http://fx.sauder.ubc.ca/supplement.html");
if ("" != $content) {
    $table = read_table_from_content($content, 3);
    array_shift($table);
    array_shift($table);
    foreach ($table as $row) {
        if (!array_key_exists($row[1], $currencies)) {
            $currencies[$row[1]] = $row[4];
        }
    }
}
*/

/*
$content = fetch_file("http://fx.sauder.ubc.ca/today.html");
if ("" != $content) {
    $table = read_table_from_content($content, 3);
    array_shift($table);
    array_shift($table);
    foreach ($table as $row) {
        if (!array_key_exists($row[1], $currencies)) {
            $currencies[$row[1]] = $row[5];
        }
    }
}
*/

$content = fetch_file("https://open.er-api.com/v6/latest/USD");
if ("" != $content) {
    if (!is_null($decoded = json_decode($content, true))) {
        foreach ($decoded['rates'] as $key => $row) {
            if (!array_key_exists($key, $currencies)) {
                 $currencies[$key] = $row;
               
            }
        }
    }
}
if (count($currencies) == 0) {
    die("FAIL: Unable to fetch any currencies\n");
}

foreach (array('AUD', 'CAD', 'EUR', 'GBP', 'JPY') as $code) {
    if (!array_key_exists($code, $currencies) || $currencies[$code] <= 0.0) {
        die("FAIL: Important currencies missing\n");
    }
}

$old_rates = $DB->query_list(sprintf(
    "SELECT `code`, `usto` FROM `currency`"
));

$database_list = $DB->query_list(sprintf(
    "SELECT `site` FROM `currency_update`"
));

$sql         = array();
$update_list = array();
$log_insert  = array();
foreach ($currencies as $k => $v) {
    if (array_key_exists($k, $old_rates)) {
        if (bccomp($v, $old_rates[$k], 6) != 0) {
            foreach ($database_list as $database) {
                $sql[] = sprintf(
                    "UPDATE `%s`.`currency` SET `usto` = %s, `usfrom` = %s WHERE `code` = %s LIMIT 1; #Was %s",
                    $database,
                    $DB->escape($v),
                    $DB->escape(bcdiv("1.0", $v, 5)),
                    $DB->escape($k),
                    $old_rates[$k]
                );
            }

            $log_insert[]  = "(" . $DB->escape($k) . ", " . $DB->escape($v) . ", NOW())";
            $update_list[] = $k;
        }
    }
}

if (count($log_insert)) {
    foreach (array_chunk($log_insert, 50) as $chunk) {
        $sql[] = sprintf(
            "INSERT INTO `currency_log` (`code`, `usto`, `change_date`) VALUES %s",
            implode(", ", $chunk)
        );
    }
}

$sql[] = sprintf(
    "UPDATE `currency_update` SET `user` = 'hwgroup', `date` = NOW()"
);

foreach ($sql as $query_string) {
    if ($DB->query($query_string)) {
        //printf("%s; # %d affected rows\n", $query_string, $DB->affected_rows);
    } else {
        printf("%s; # FAILED\n", $query_string);
    }
}

if (count($update_list)) {
    die("SUCCESS: Currencies updated for " . implode(", ", $update_list) . "\n");
}

die("SUCCESS: No currency changes detected\n");
