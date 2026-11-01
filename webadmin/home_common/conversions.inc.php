<?php
if (!function_exists('convertCurrency')) {
    /**
     * Converts a given amount to another currency.
     * Converts the provided currency from one currency to another,
     * with the option for rounding. Default input currency is 'AUD'.
     *
     * @param float  $amount
     * @param string $fromCur
     * @param string $toCur
     * @param bool   $round
     *
     * @return string|float
     */
    function convertCurrency($amount, $fromCur = 'AUD', $toCur = '', $round = false, $decimal = '2')
    {
        $subunits_0    = array("ADP", "BIF", "BYR", "CLF", "CLP", "DJF", "ESP", "GNF", "ITL", "JPY", "KMF", "KRW", "LUF", "MGF", "PYG", "RWF", "TRL", "VUV", "XAF", "XOF", "XPF");
        $subunits_5    = array("MRO", "MGA");
        $subunits_1000 = array("BHD", "IQD", "JOD", "KWD", "LYD", "OMR", "TND");
        if ($toCur == $fromCur || '' == $toCur) {
            if (in_array($fromCur, $subunits_0)) {
                return round($amount);
            }

            if (in_array($fromCur, $subunits_5)) {
                return sprintf('%.1f', ceil($amount * 5.0) / 5.0);
            }

            if (in_array($fromCur, $subunits_1000)) {
                return sprintf('%.3f', round($amount, 3));
            }

            if ($round) {
                return sprintf('%.' . $decimal . 'f', roundAmount($amount));
            }

            return sprintf('%.' . $decimal . 'f', round($amount, $decimal));
        }
        if ('' == $fromCur) {
            return $amount;
        }

        $exRates = getExchangeRateList();
        if (!isset($exRates[$toCur]) || !isset($exRates[$fromCur])) {
            return $amount;
        }

        if (in_array($toCur, $subunits_0)) {
            return round($amount * $exRates[$fromCur] / $exRates[$toCur], 0);
        }

        if (in_array($toCur, $subunits_5)) {
            return sprintf('%.1f', ceil($amount * $exRates[$fromCur] / $exRates[$toCur] * 5.0) / 5.0);
        }

        if (in_array($toCur, $subunits_1000)) {
            return sprintf('%.3f', round($amount * $exRates[$fromCur] / $exRates[$toCur], 3));
        }

        if ($round) {
            return sprintf('%.' . $decimal . 'f', roundAmount($amount * $exRates[$fromCur] / $exRates[$toCur]));
        }

        return sprintf('%.' . $decimal . 'f', round($amount * $exRates[$fromCur] / $exRates[$toCur], $decimal));
    }
}
if (!function_exists('convertCurrencyNEw')) {
    /**
     * Converts a given amount to another currency.
     * Converts the provided currency from one currency to another,
     * with the option for rounding. Default input currency is 'AUD'.
     *
     * @param float  $amount
     * @param string $fromCur
     * @param string $toCur
     * @param bool   $round
     *
     * @return string|float
     */
    function convertCurrencyNEw($amount, $fromCur = 'AUD', $toCur = '', $round = false, $decimal = '2')
    {
        $subunits_0    = array("ADP", "BIF", "BYR", "CLF", "CLP", "DJF", "ESP", "GNF", "ITL", "JPY", "KMF", "KRW", "LUF", "MGF", "PYG", "RWF", "TRL", "VUV", "XAF", "XOF", "XPF");
        $subunits_5    = array("MRO", "MGA");
        $subunits_1000 = array("BHD", "IQD", "JOD", "KWD", "LYD", "OMR", "TND");
        if ($toCur == $fromCur || '' == $toCur) {
            if (in_array($fromCur, $subunits_0)) {
                return round($amount);
            }

            if (in_array($fromCur, $subunits_5)) {
                return sprintf('%.1f', ceil($amount * 5.0) / 5.0);
            }

            if (in_array($fromCur, $subunits_1000)) {
                return sprintf('%.3f', round($amount, 3));
            }

            if ($round) {
                return sprintf('%.' . $decimal . 'f', roundAmount($amount));
            }

            return sprintf('%.' . $decimal . 'f', round($amount, $decimal));
        }
        if ('' == $fromCur) {
            return $amount;
        }

        $exRates = getExchangeRateList();
        if (!isset($exRates[$toCur]) || !isset($exRates[$fromCur])) {
            return $amount;
        }

        if (in_array($toCur, $subunits_0)) {
            return round($amount * $exRates[$fromCur] / $exRates[$toCur], 0);
        }

        if (in_array($toCur, $subunits_5)) {
            return sprintf('%.1f', ceil($amount * $exRates[$fromCur] / $exRates[$toCur] * 5.0) / 5.0);
        }

        if (in_array($toCur, $subunits_1000)) {
            return sprintf('%.3f', round($amount * $exRates[$fromCur] / $exRates[$toCur], 3));
        }

        if ($round) {
            return sprintf('%.' . $decimal . 'f', roundAmount($amount * $exRates[$fromCur] / $exRates[$toCur]));
        }

        return sprintf('%.' . $decimal . 'f', round($amount * $exRates[$fromCur] / $exRates[$toCur], $decimal));
    }
}
if (!function_exists('convertDimensions')) {
    /**
     * Convert dimensions from one unit to another.
     * Default output units is 'cm'.
     * @param string $inval
     * @param string $toUnits
     * @param string $fromUnits
     * @return mixed
     */
    function convertDimensions($inval, $toUnits = "cm", $fromUnits = '')
    {
        $inval       = strtolower(str_replace("\t", ' ', trim($inval)));
        $regexp      = '/([wdh])[ ]*([0-9\.]*)[^0-9\.]+([wdh])[ ]*([0-9\.]*)[^0-9\.]+([wdh])[ ]*([0-9\.]*)([^0-9\.]*)/i';
        $preg_result = array();
        preg_match($regexp, $inval, $preg_result);
        if (count($preg_result) < 7) {
            return false;
        }

        array_shift($preg_result);
        $result          = array();
        $key             = strtolower(array_shift($preg_result));
        $result[$key]    = floatval(array_shift($preg_result));
        $key             = strtolower(array_shift($preg_result));
        $result[$key]    = floatval(array_shift($preg_result));
        $key             = strtolower(array_shift($preg_result));
        $result[$key]    = floatval(array_shift($preg_result));
        $result['units'] = 'mm';
        if ('' != $fromUnits) {
            $result['units'] = $fromUnits;
        } elseif (count($preg_result) > 0) {
            $result['units'] = trim(array_shift($preg_result));
        }

        if ('' != $toUnits) {
            foreach ($result as $key => $value) {
                if (is_numeric($value)) {
                    $result[$key] = convertDistance($value, $fromUnits, $toUnits);
                }
            }

            $result['units'] = $toUnits;
        }
        return $result;
    }
}
if (!function_exists('convertDistance')) {
    /**
     * Convert distance value from one unit to another.
     * Returns float value of $inval if no units set.
     * @param mixed $inval
     * @param string $fromUnits
     * @param string $toUnits
     * @return float
     */
    function convertDistance($inval, $fromUnits = '', $toUnits = '')
    {
        if ('' == $fromUnits || $toUnits == $fromUnits || '' == $toUnits) {
            return floatval($inval);
        }

        $unRates = getDistanceList();
        if (!isset($unRates[$toUnits]) || !isset($unRates[$fromUnits])) {
            return floatval($inval);
        }

        return floatval($inval * $unRates[$fromUnits] / $unRates[$toUnits]);
    }
}
if (!function_exists('convertLinearInterpolate')) {
    /**
     * returns the linear interpolate of given input
     * @param float $x1
     * @param float $y1
     * @param float $x2
     * @param float $y2
     * @param float $x
     * @return float
     */
    function convertLinearInterpolate($x1, $y1, $x2, $y2, $x)
    {
        if ($x1 == $x2) {
            return $y1;
        }

        return ($y2 - $y1) * ($x - $x1) / ($x2 - $x1) + $y1;
        //return (($x-$x1) *$y2+($x2-$x) *$y1) /($x2-$x1);

    }
}
if (!function_exists('convertListToRanges')) {
    /**
     * Returns an array of the range of input.
     * Output array of size two containing the highest values
     * of the input array
     * @param array $list
     * @return array
     */
    function convertListToRanges($list)
    {
        asort($list);
        $ranges  = array();
        $low_id  = reset($list);
        $high_id = $low_id;
        foreach ($list as $value) {
            if ($value > ($high_id) + 1) {
                $ranges[] = array(0 => $low_id, 1 => $high_id);
                $low_id   = $value;
                $high_id  = $value;
            } else {
                $high_id = $value;
            }
        }

        $ranges[] = array(0 => $low_id, 1 => $high_id);
        return $ranges;
    }
}
if (!function_exists('convertSize')) {
    /**
     * Converts size of input to bytes.
     * @param mixed $size
     * @return float
     */
    function convertSize($size)
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        $size = preg_replace('/[^0-9\.]/', '', $size);
        if ($unit) {
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }

        return round($size);
    }
}
if (!function_exists('convertTimeOffset')) {
    /**
     * Adds the input time to the $timestamp.
     * Returns unix timestamp of result.
     * @param int $timestamp
     * @param int $years
     * @param int $months
     * @param int $days
     * @param int $hours
     * @param int $minutes
     * @param int $seconds
     * @return int
     */
    function convertTimeOffset($timestamp, $years = 0, $months = 0, $days = 0, $hours = 0, $minutes = 0, $seconds = 0)
    {
        $a = explode('/', date('G/i/s/n/j/Y', $timestamp));
        $b = array($hours, $minutes, $seconds, $months, $days, $years);
        $c = array();
        foreach (array_keys($a) as $key) {
            $c[$key] = $a[$key] + $b[$key];
        }

        return mktime($c[0], $c[1], $c[2], $c[3], $c[4], $c[5]);
    }
}
if (!function_exists('convertWeight')) {
    /**
     * Convert weight from one unit to another.
     * Default output units is 'kg'.
     * @param mixed $inval
     * @param string $toUnits
     * @param string $fromUnits
     * @return float
     */
    function convertWeight($inval, $toUnits = 'kg', $fromUnits = '')
    {
        if ('' == $fromUnits || $toUnits == $fromUnits || '' == $toUnits) {
            return floatval($inval);
        }

        $unRates = getWeightList();
        if (!isset($unRates[$toUnits]) || !isset($unRates[$fromUnits])) {
            return floatval($inval);
        }

        return floatval($inval * $unRates[$fromUnits] / $unRates[$toUnits]);
    }
}
if (!function_exists('findClosestKey')) {
    function findClosestKey($inArray, $inString)
    {
        $outKey = '';
        asort($inArray);
        foreach ($inArray as $key => $row) {
            if (strcmp($row, $inString) <= 0) {
                $outKey = $key;
            }
        }

        return $outKey;
    }
}
if (!function_exists('hexstr')) {
    /**
     * Converts a hexadecimal input to ASCII string.
     * @param string $inString
     * @return string
     */
    function hexstr($instring)
    {
        $output = '';
        foreach (str_split($instring, 2) as $key) {
            $output .= chr(hexdec($key));
        }

        return $output;
    }
}
if (!function_exists('ip2long_v6')) {
    function ip2longV6($ip)
    {
        $ip_n = inet_pton($ip);
        $bin  = '';
        for ($bit = strlen($ip_n) - 1; $bit >= 0; $bit--) {
            $bin = sprintf('%08b', ord($ip_n[$bit])) . $bin;
        }

        if (function_exists('gmp_init')) {
            return gmp_strval(gmp_init($bin, 2), 10);
        } elseif (function_exists('bcadd')) {
            $dec = '0';
            for ($i = 0; $i < strlen($bin); $i++) {
                $dec = bcmul($dec, '2', 0);
                $dec = bcadd($dec, $bin[$i], 0);
            }
            return $dec;
        } else {
            trigger_error('GMP or BCMATH extension not installed!', E_USER_ERROR);
        }
    }
}
if (!function_exists('long2ip_v6')) {
    function long2ipV6($dec)
    {
        if (function_exists('gmp_init')) {
            $bin = gmp_strval(gmp_init($dec, 10), 2);
        } elseif (function_exists('bcadd')) {
            $bin = '';
            do {
                $bin = bcmod($dec, '2') . $bin;
                $dec = bcdiv($dec, '2', 0);
            } while (bccomp($dec, '0'));
        } else {
            trigger_error('GMP or BCMATH extension not installed!', E_USER_ERROR);
        }

        $bin = str_pad($bin, 128, '0', STR_PAD_LEFT);
        $ip  = array();
        for ($bit = 0; $bit <= 7; $bit++) {
            $bin_part = substr($bin, $bit * 16, 16);
            $ip[]     = dechex(bindec($bin_part));
        }
        $ip = implode(':', $ip);
        return inet_ntop(inet_pton($ip));
    }
}
if (!function_exists('microtime_float')) {
    /**
     * Gets current UNIX timestamp
     * @return float
     */
    function microtimeFloat()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }
}
if (!function_exists('roundAmount')) {
    /**
     * Rounds a provided amount of currency.
     * Rounds to the nearest .05
     * @param string $amount
     * @return string
     */
    function roundAmount($amount)
    {
        $isneg = false;
        if ($amount < 0) {
            $amount = abs($amount);
            $isneg  = true;
        }
        $amount               = sprintf('%.2f', $amount);
        list($before, $after) = explode('.', $amount);
        $num                  = substr($after, 0, 1);
        $numLast              = substr($after, 1, 1);
        if (9 == $num && $numLast >= 7) {
            $before++;
            $num = $newLast = 0;
        } else {
            $convert = array(0 => 0, 1 => 0, 2 => 0, 3 => 5, 4 => 5, 5 => 5, 6 => 5, 7 => 0, 8 => 0, 9 => 0);
            $newLast = $convert[$numLast];
            if ($numLast > 6) {
                $num++;
            }

        }
        $newAmount = $before . '.' . $num . $newLast;
        if ($isneg) {
            return sprintf('%.2f', 0 - $newAmount);
        }

        return $newAmount;
    }
}
if (!function_exists('strhex')) {
    /**
     * Converts a string to hexadecimal
     * @param string $inString
     * @return string
     */
    function strhex($instring)
    {
        $output = '';
        for ($i = 0; $i < strlen($instring); $i++) {
            $output .= substr('00' . dechex(ord(substr($instring, $i, 1))), -2);
        }

        return $output;
    }
}
if (!function_exists('isUTF8')) {
    /**
     * Check if string is valid UTF-8.
     * @param string $str
     * @return int
     */
    function isUTF8($str)
    {
        return preg_match('/^([\x09\x0A\x0D\x20-\x7E]|[\xC2][\xA0-\xBF]|[\xC3-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})*$/', $str);
    }
}
if (!function_exists('isISO88591')) {
    /**
     * Check if string is valid ISO 8859-1
     * @param string $str
     * @return int
     */
    function isISO88591($str)
    {
        return preg_match('/^([\x09\x0A\x0D\x20-\x7E\xA0-\xFF])*$/', $str);
    }
}
if (!function_exists('isCP1252')) {
    /**
     * Check if string is valid CP1252
     * @param string $str
     * @return int
     */
    function isCP1252($str)
    {
        return preg_match('/^([\x09\x0A\x0D\x20-\x7E\x80\x82-\x8C\x8E\x91-\x9C\x9E-\xFF])*$/', $str);
    }
}
if (!function_exists('utf8Char')) {
    /**
     * Convert input unicode character to UTF-8 string.
     * @param int $codePoint
     * @return mixed
     */
    function utf8Char($codePoint)
    {
        $char = '';
        if ($codePoint < 0) {
            return false;
        } elseif ($codePoint <= 0x007f) {
            $char .= chr($codePoint);
        } elseif ($codePoint <= 0x07ff) {
            $char .= chr(0xc0 | ($codePoint >> 6));
            $char .= chr(0x80 | ($codePoint & 0x003f));
        } elseif (0xFEFF == $codePoint) {
            // nop -- zap the BOM

        } elseif ($codePoint >= 0xD800 && $codePoint <= 0xDFFF) {
            // found a surrogate
            return false;
        } elseif ($codePoint <= 0xffff) {
            $char .= chr(0xe0 | ($codePoint >> 12));
            $char .= chr(0x80 | (($codePoint >> 6) & 0x003f));
            $char .= chr(0x80 | ($codePoint & 0x003f));
        } elseif ($codePoint <= 0x10ffff) {
            $char .= chr(0xf0 | ($codePoint >> 18));
            $char .= chr(0x80 | (($codePoint >> 12) & 0x3f));
            $char .= chr(0x80 | (($codePoint >> 6) & 0x3f));
            $char .= chr(0x80 | ($codePoint & 0x3f));
        } else {
            // out of range
            return false;
        }
        return $char;
    }
}
if (!function_exists('utf8FromCP1252Char')) {
    /**
     * Convert input CP1252 character to UTF-8 string.
     * @param string $char
     * @return mixed
     */
    function utf8FromCP1252Char($char)
    {
        $utf8CodePoint   = array(128 => 0x20AC, 129 => '', 130 => 0x201A, 131 => 0x0192, 132 => 0x201E, 133 => 0x2026, 134 => 0x2020, 135 => 0x2021, 136 => 0x02C6, 137 => 0x2030, 138 => 0x0160, 139 => 0x2039, 140 => 0x0152, 141 => '', 142 => 0x017D, 143 => '', 144 => '', 145 => 0x2018, 146 => 0x2019, 147 => 0x201C, 148 => 0x201D, 149 => 0x2022, 150 => 0x2013, 151 => 0x2014, 152 => 0x02DC, 153 => 0x2122, 154 => 0x0161, 155 => 0x203A, 156 => 0x0153, 157 => '', 158 => 0x017E, 159 => 0x0178);
        $cp1252CodePoint = ord($char);
        return utf8Char($utf8CodePoint[$cp1252CodePoint]);
    }
}
if (!function_exists('utf8FromCP1252')) {
    /**
     * Convert input CP1252 string to UTF-8 string.
     * @param string $char
     * @return string
     */
    function utf8FromCP1252($string)
    {
        if (isCP1252($string)) {
            $utf8String = utf8_encode($string);
            return preg_replace_callback('|\xC2([\x80\x82-\x8C\x8E\x91-\x9C\x9E\x9F])|', function ($s) {return utf8FromCP1252Char($s[1]);}, $utf8String);
        } else {
            return '';
        }
    }
}
