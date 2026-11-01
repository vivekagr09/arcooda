<?php
/**
 * @var int     $asset_id
 * @var string  $asset_type
 * @var mixed[] $table_info
 */
include_once __DIR__ . '/common.inc.php';

$action = 'view';

$asset = assetFromDatabase($asset_type, $asset_id);
$from_file = $table_info['directory'] . $asset['filename'];
$to_file   = $asset['fileorig'];
if (file_exists($from_file)) {
    $bufsize        = 20000;
    $content_type   = mime_content_type($from_file);
    $content_length = filesize($from_file);
    $last_modified  = filemtime($from_file);

    if (isset($_SERVER["HTTP_IF_MODIFIED_SINCE"]) && (strtotime($_SERVER["HTTP_IF_MODIFIED_SINCE"]) == $last_modified)) {
        header("HTTP/1.1 304 Not Modified");
        exit();
    }

    header("Date: " . date('r'));
    header("Last-Modified: " . date('r', $last_modified ?: null));

    if (isset($_SERVER['HTTP_RANGE'])) {
        if (preg_match("/^bytes=(\\d+)-(\\d*)$/", $_SERVER['HTTP_RANGE'], $matches)) {
            $from = $matches[1];
            $to   = $matches[2];
            if (empty($to)) {
                $to = $content_length - 1;
            }

            $content_size = $to - $from + 1;
            header("HTTP/1.1 206 Partial Content");
            header("Content-Range: " . $from . "-" . $to . "/" . $content_length);
            header("Content-Length: " . $content_size);
            header("Content-Type: " . $content_type);
            header('Content-Disposition: inline; filename="' . urlencode($to_file) . '"');
            header("Content-Transfer-Encoding: binary");
            if ($fh = fopen($from_file, "rb")) {
                fseek($fh, $from);
                $cur_pos = ftell($fh);
                while (false !== $cur_pos && ftell($fh) + $bufsize < $to + 1) {
                    $buffer = fread($fh, $bufsize);
                    print $buffer;
                    $cur_pos = ftell($fh);
                }
                $buffer = fread($fh, $to + 1 - $cur_pos);
                print $buffer;
                fclose($fh);
                die();
            }
        }
    } else {
        header("HTTP/1.1 200 OK");
        header("Content-Length: " . $content_length);
        header("Content-Type: " . $content_type);
        header('Content-Disposition: inline; filename="' . urlencode($to_file) . '"');
        header("Content-Transfer-Encoding: binary");
        readfile($from_file);
        die();
    }
}

header("HTTP/1.1 404 Not Found");
die();
