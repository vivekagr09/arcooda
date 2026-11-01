<?php

//dv at josheli.com

$user   = 'ointpol1'; //your cpanel username
$pass   = 'coinopexpress2020'; //your cpanel password
$domain = 'coinopexpress.com'; //do not include 'http://' or 'www.'

$spam_words = array('mortgage', 'sex', 'porn');

/*
NO NEED TO TOUCH ANYTHING BELOW HERE
 */

/**
 * retrieves the file, either .pl or .png
 *
 * @param string $fileQuery
 *
 * @return string
 */
function getFile($fileQuery)
{
    global $user, $pass, $domain;
    return file_get_contents("http://$user:$pass@$domain:2082/" . $fileQuery);
}

//it's a .png file...
if (strpos($_SERVER['QUERY_STRING'], '.png') !== false) {
    $fileQuery = $_SERVER['QUERY_STRING'];
}
//probably first time to access page...
elseif (empty($_SERVER['QUERY_STRING'])) {
    $fileQuery = "awstats.pl?config=$domain";
}
//otherwise, all other accesses
else {
    $fileQuery = 'awstats.pl?' . $_SERVER['QUERY_STRING'];
}

//now get the file
$file = getFile($fileQuery);

//check again to see if it was a .png file
//if it's not, replace the links
if (strpos($_SERVER['QUERY_STRING'], '.png') === false) {
    $file = str_replace('awstats.pl', basename($_SERVER['PHP_SELF']), $file);
    $file = str_replace('="/images', '="' . basename($_SERVER['PHP_SELF']) . '?images', $file);
    $file = str_replace($spam_words, 'SPAM', $file);
}
//if it is a png, output appropriate header
else {
    header("Content-type: image/png");
}

//output the file
echo $file;
