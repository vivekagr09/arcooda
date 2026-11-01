<?php
function pop3Result($result)
{
    if (substr($result, 0, 1) == '-') {
        return 0;
    }

    if (substr($result, 0, 1) == '+') {
        return 1;
    }

    return 2;
}

function pop3Query($fp, $output)
{
    fputs($fp, $output . "\n");
//    echo $output."\n";
}

function pop3Fetch($fp)
{
    $input = fgets($fp);
//    echo $input;
    if (pop3Result($input) != 2) {
        return $input;
    }

    $result = '';
    while (substr($input, 0, 1) != '.') {
        $result .= $input;
        $input = fgets($fp);
//        echo $input;
    }
    return $result;
}

function pop3Connect($server, $user = '', $pass = '')
{
    @$fp = fsockopen("mail." . $server, 110);
    if (!$fp) {
        return $fp;
    }

    pop3Fetch($fp);
    if ($fp && '' != $user && '' != $pass) {
        pop3Query($fp, "USER " . $user . "@" . $server);
        pop3Fetch($fp);
        pop3Query($fp, "PASS " . $pass);
        $result = pop3Fetch($fp);
        if (pop3Result($result) == 0) {
            fclose($fp);
            return false;
        }
    }
    return $fp;
}

function pop3Close($fp)
{
    pop3Query($fp, 'QUIT');
    fclose($fp);
}
