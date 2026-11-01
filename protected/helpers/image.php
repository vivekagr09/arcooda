<?php
namespace app\helpers;

class Image
{
    static $fit_strategy = 'app\helpers\Image::fitScaleDown';

    /**
     * Apply boundary clipping on a single axis while preserving scale
     *
     * @param int $sx
     * @param int $dst_x
     * @param int $src_x
     * @param int $dst_width
     * @param int $src_width
     *
     * @return int[]
     */
    public static function trimAxisSafe($sx, $dst_x, $src_x, $dst_width, $src_width)
    {
        if ($dst_x < 0 || ($dst_x + $dst_width) > $sx) {
            $ratio = $src_width / (float)$dst_width;
            if ($dst_x < 0) {
                $src_x     -= intval($ratio * $dst_x);
                $src_width += intval($ratio * $dst_x);
                $dst_width += $dst_x;
                $dst_x     = 0;
            }
            if (($dst_x + $dst_width) > $sx) {
                $src_width += intval($ratio * ($sx - $dst_width - $dst_x));
                $dst_width = $sx - $dst_x;
            }
        }
        return [$dst_x, $src_x, $dst_width, $src_width];
    }

    /**
     * Overflow save version of imagecopyresampled
     *
     * @param resource $dst_image
     * @param resource $src_image
     * @param int      $dst_x
     * @param int      $dst_y
     * @param int      $src_x
     * @param int      $src_y
     * @param int      $dst_width
     * @param int      $dst_height
     * @param int      $src_width
     * @param int      $src_height
     *
     * @return mixed[] Arguments ready to pass to imagecopyresampled function
     */
    public static function copyResampledSafe($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_width, $dst_height, $src_width, $src_height)
    {
        list($dst_x, $src_x, $dst_width, $src_width)   = self::trimAxisSafe(imagesx($dst_image), $dst_x, $src_x, $dst_width, $src_width);
        list($dst_y, $src_y, $dst_height, $src_height) = self::trimAxisSafe(imagesy($dst_image), $dst_y, $src_y, $dst_height, $src_height);
        list($src_x, $dst_x, $src_width, $dst_width)   = self::trimAxisSafe(imagesx($src_image), $src_x, $dst_x, $src_width, $dst_width);
        list($src_y, $dst_y, $src_height, $dst_height) = self::trimAxisSafe(imagesy($src_image), $src_y, $dst_y, $src_height, $dst_height);
        return [$dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_width, $dst_height, $src_width, $src_height];
    }

    /**
     * @param int $dst_width
     * @param int $dst_height
     * @param int $src_width
     * @param int $src_height
     *
     * @return int[]
     */
    public static function fitContain($dst_width, $dst_height, $src_width, $src_height)
    {
        if ($src_width * $dst_height > $dst_width * $src_height) {
            $new_height = intdiv($dst_width * $src_height, $src_width);
            return [
                0, intdiv($dst_height - $new_height, 2),
                0, 0,
                $dst_width, $new_height,
                $src_width, $src_height,
            ];
        }

        $new_width = intdiv($dst_height * $src_width, $src_height);
        return [
            intdiv($dst_width - $new_width, 2), 0,
            0, 0,
            $new_width, $dst_height,
            $src_width, $src_height,
        ];
    }

    /**
     * @param int $dst_width
     * @param int $dst_height
     * @param int $src_width
     * @param int $src_height
     *
     * @return int[]
     */
    public static function fitCover($dst_width, $dst_height, $src_width, $src_height)
    {
        if ($src_width * $dst_height < $dst_width * $src_height) {
            $new_height = intdiv($dst_width * $src_height, $src_width);
            return [
                0, intdiv($dst_height - $new_height, 2),
                0, 0,
                $dst_width, $new_height,
                $src_width, $src_height,
            ];
        }

        $new_width = intdiv($dst_height * $src_width, $src_height);
        return [
            intdiv($dst_width - $new_width, 2), 0,
            0, 0,
            $new_width, $dst_height,
            $src_width, $src_height,
        ];
    }

    /**
     * @param int $dst_width
     * @param int $dst_height
     * @param int $src_width
     * @param int $src_height
     *
     * @return int[]
     */
    public static function fitFill($dst_width, $dst_height, $src_width, $src_height)
    {
        return [
            0, 0,
            0, 0,
            $dst_width, $dst_height,
            $src_width, $src_height,
        ];
    }

    /**
     * @param int $dst_width
     * @param int $dst_height
     * @param int $src_width
     * @param int $src_height
     *
     * @return int[]
     */
    public static function fitNone($dst_width, $dst_height, $src_width, $src_height)
    {
        return [
            intdiv($dst_width - $src_width, 2), intdiv($dst_height - $src_height, 2),
            0, 0,
            $src_width, $src_height,
            $src_width, $src_height,
        ];
    }

    /**
     * @param int $dst_width
     * @param int $dst_height
     * @param int $src_width
     * @param int $src_height
     *
     * @return int[]
     */
    public static function fitScaleDown($dst_width, $dst_height, $src_width, $src_height)
    {
        if ($src_width > $dst_width || $src_height > $dst_height) {
            return self::fitContain($dst_width, $dst_height, $src_width, $src_height);
        }
        return self::fitNone($dst_width, $dst_height, $src_width, $src_height);
    }

    /**
     * Test if cache file exists or has expired, and recreate as necessary
     *
     * Returns true on success
     *
     * @param string $from_file
     * @param string $to_file
     * @param string $profile_file
     * @param string $watermark_file
     * @param int    $width
     * @param int    $height
     * @param int    $want_time
     *
     * @return bool
     */
    public static function copyToStatic($from_file, $to_file, $profile_file = "", $watermark_file = "", $width = 0, $height = 0, $want_time = 0)
    {
        $ext1 = explode('.', $from_file);
        $ext1 = end($ext1);
        $ext2 = explode(".", $to_file);
        $ext2 = end($ext2);

        if (is_file($profile_file)) {
            $want_time = max($want_time, filemtime($profile_file));
        }

        if (is_file($watermark_file)) {
            $want_time = max($want_time, filemtime($watermark_file));
        }

        if (substr($from_file, 0, 4) != "http") {
            if (is_file($from_file)) {
                $want_time = max($want_time, filemtime($from_file));
            } else {
                return false;
            }
        }
        if (is_file($to_file) && filemtime($to_file) < $want_time) {
            unlink($to_file);
        }

        if (!is_file($to_file)) {
            if (substr($from_file, 0, 4) == "http") {
                shell_exec(sprintf(
                    "wget -q %s -O %s",
                    escapeshellarg($from_file),
                    escapeshellarg("/tmp/copy_to_static.wget")
                ));
                $from_file = "/tmp/copy_to_static.wget";
                if (!is_file($from_file)) {
                    return false;
                }
            }

            if (!is_dir(dirname($to_file))) {
                @mkdir(dirname($to_file), 0777, true);
            }
            if (0 == $width && 0 == $height && "" == $profile_file && "" == $watermark_file && $ext1 == $ext2) {
                if (copy($from_file, $to_file)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                if (in_array($ext2, array("png"))) {
                    $image = self::getContent($from_file, $width, $height, $ext2, $watermark_file, $profile_file, "");
                } elseif (in_array($ext2, array("jpg"))) {
                    $image = self::getContent($from_file, $width, $height, $ext2, $watermark_file, $profile_file);
                } else {
                    $image = file_get_contents($from_file);
                }

                if (false !== $image && file_put_contents($to_file, $image) !== false) {
                    if ($want_time > 0) {
                        touch($to_file, $want_time);
                    }

                    return true;
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Produce an optimal, watermarked, profiled and resized image
     *
     * Returns the resized image as a string
     *
     * @param string $src_file
     * @param int    $width
     * @param int    $height
     * @param string $type
     * @param string $water_file
     * @param string $profile_file
     * @param string $background
     *
     * @return string|false
     */
    public static function getContent($src_file, $width = 0, $height = 0, $type = "png", $water_file = "", $profile_file = "", $background = "xc:white")
    {
        /**
         * Check all specified files exist
         */
        if ("" == $src_file || !is_file($src_file) || filesize($src_file) == 0) {
            return false;
        }

        if ("" != $water_file && !is_file($water_file)) {
            return false;
        }

        if ("" != $profile_file && !is_file($profile_file)) {
            return false;
        }

        /**
         * Collect the image layers
         */
        $layers = [];
        foreach ([$src_file, $water_file] as $file) {
            if ('' != $file) {
                $content = file_get_contents($file);
                if (false === $content) {
                    if (function_exists('imagedestroy')) {
                        foreach ($layers as $layer) {
                            imagedestroy($layer);
                        }
                    }
                    $layers = [];
                    return false;
                }

                $layer = imagecreatefromstring($content);
                if (false === $layer) {
                    if (function_exists('imagedestroy')) {
                        foreach ($layers as $layer) {
                            imagedestroy($layer);
                        }
                    }
                    $layers = [];
                    return false;
                }

                if (0 == $width) {
                    $width = imagesx($layer);
                }
                if (0 == $height) {
                    $height = imagesy($layer);
                }

                $layers[] = $layer;
            }
        }

        /**
         * Generate the best quality result
         */
        $img = imagecreatetruecolor($width, $height);
        if (false === $img) {
            if (function_exists('imagedestroy')) {
                foreach ($layers as $layer) {
                    imagedestroy($layer);
                }
            }
            $layers = [];
            return false;
        }

        if ('png' === $type) {
            imagealphablending($img, false);
            $transparent = imagecolorallocatealpha($img, 255, 0, 255, 127);
            imagefilledrectangle($img, 0, 0, $width - 1, $height - 1, $transparent);
            imagesavealpha($img, true);
        }

        /**
         * Apply white background
         */
        if ('xc:white' === $background) {
            $white = imagecolorallocate($img, 255, 255, 255);
            if (false === $white) {
                if (function_exists('imagedestroy')) {
                    imagedestroy($img);
                    foreach ($layers as $layer) {
                        imagedestroy($layer);
                    }
                }
                $img    = false;
                $layers = [];
                return false;
            }
            imagefilledrectangle($img, 0, 0, $width - 1, $height - 1, $white);
        }

        /**
         * Smoosh the layers together
         */
        foreach ($layers as $layer) {
            imagecopyresampled(...self::copyResampledSafe(
                $img,
                $layer,
                ...(self::$fit_strategy)(
                    $width,
                    $height,
                    imagesx($layer),
                    imagesy($layer)
                )
            ));
            if (function_exists('imagedestroy')) {
                imagedestroy($layer);
            }
            $layer = false;
        }

        ob_start();
        imageinterlace($img, 1);
        if ('jpg' === $type) {
            imagejpeg($img, null, 100);
        } else {
            imagepng($img, null, 9);
        }
        $result = ob_get_clean();

        if ('jpg' === $type) {
            $quality   = 64;
            $step      = 32;
            $want_size = max(25600, $width * $height * /*0.390625*/0.26312629132231404958677685950375/*0.084628099173553719008264462809917*/);
            while ($step > 0) {
                ob_start();
                imagejpeg($img, null, $quality);
                $test = ob_get_clean();

                if (false === $test) {
                    break;
                } elseif (strlen($test) > $want_size) {
                    $quality -= $step;
                } else {
                    $result = $test;
                    if (100 == $quality) {
                        break;
                    }
                    $quality = min(100, $quality + $step);
                }
                $step = intval($step / 2);
            }
        }

        if (function_exists('imagedestroy')) {
            imagedestroy($img);
        }
        $img = false;

        /*
        if ('' != $profile_file) {
            $result = iptcembed(file_get_contents($profile_file), $result);
        }
        */

        return $result;
    }

    /**
     * @param string $src_file
     *
     * @return void
     */
    public static function HTTPSendFile($src_file,$filedownloadname = null)
    {
        header_remove();
        //header(sprintf('Date: %s', gmdate(DATE_RFC7231)), true);

        $max_age = max(min(
            (time() - filemtime($src_file)) / 10,
            365 * 24 * 60 * 60
        ), 0);
        $expires = gmdate(DATE_RFC7231, time() + $max_age);
        header(sprintf(
            'Cache-Control: no-transform, public, max-age=%d',
            $max_age
        ), true);
        header(sprintf('Expires: %s', $expires), true);

        $last_modified = gmdate(DATE_RFC7231, filemtime($src_file) ?: time());
        header(sprintf('Last-Modified: %s', $last_modified), true);
        if ($last_modified == ($_SERVER['HTTP_IF_MODIFIED_SINCE'] ?? '')) {
            http_response_code(HTTP_STATUS_NOT_MODIFIED);
            return;
        }

        /*
        $etag = '"' . md5_file($src_file) . '"';
        header(sprintf('Etag: %s', $etag), true);
        if ($etag == $_SERVER['HTTP_IF_NONE_MATCH'] ?? '') {
            http_response_code(HTTP_STATUS_NOT_MODIFIED);
            return;
        }
        */

        $content_type = mime_extension_type($src_file);
        $ext = explode('.', trim($src_file));
        $ext = strtolower(end($ext));
        $filedownloadname = $filedownloadname ? ($filedownloadname.'.'.$ext) : basename($src_file);
        header(sprintf('Content-Type: %s', $content_type), true);
        if($content_type == 'application/octet-stream'){
            header('Content-Disposition: attachment; filename="' .$filedownloadname . '"');
        }else{
            header('Content-Disposition: filename="' .$filedownloadname . '"');
        }
        header(sprintf('Content-Length: %d', filesize($src_file)), true);

        readfile($src_file);
        return;
    }
}
