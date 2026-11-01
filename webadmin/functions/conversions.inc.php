<?php
if (!function_exists("convertDimensions")) {
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
if (!function_exists("convertDistance")) {
    function convertDistance($inval, $fromUnits = '', $toUnits = '')
    {
        if ('' == $fromUnits || $toUnits == $fromUnits || '' == $toUnits) {
            return floatval($inval);
        }

        $unRates = array(
            'angstrom'   => 1e-7,
            'centimeter' => 10,
            'cm'         => 10,
            'decimeter'  => 100,
            'dm'         => 100,
            'kilometer'  => 1000000,
            'km'         => 1000000,
            'm'          => 1000,
            'meter'      => 1000,
            'micrometer' => 0.001,
            'micron'     => 0.001,
            'millimeter' => 1,
            'mm'         => 1,
            'nanometer'  => 0.000001,
            'nm'         => 0.000001,
            'chain'      => 20120,
            'foot'       => 304.8,
            'ft'         => 304.8,
            'furlong'    => 201200,
            'hand'       => 101.6,
            'in'         => 25.4,
            'inch'       => 25.4,
            'league'     => 4828000,
            'line'       => 2.117,
            'link'       => 201.2,
            'mi'         => 1609000,
            'mil'        => 0.0254,
            'mile'       => 1609000,
            'perch'      => 5029,
            'rd'         => 5029,
            'rod'        => 5029,
            'yard'       => 914.4,
            'yd'         => 914.4,
            'cho'        => 109100,
            'jyo'        => 3030,
            'kairi'      => 1852000,
            'ken'        => 1818,
            'mon'        => 24,
            'ri'         => 3927000,
            'shyaku'     => 303,
            'sun'        => 30.3,
            'pt'         => 0.352,
            'px'         => 0.26458,
        );
        if (!isset($unRates[$toUnits]) || !isset($unRates[$fromUnits])) {
            return floatval($inval);
        }

        return floatval($inval * $unRates[$fromUnits] / $unRates[$toUnits]);
    }
}
if (!function_exists("convertWeight")) {
    function convertWeight($inval, $toUnits = 'kg', $fromUnits = '')
    {
        if ('' == $fromUnits || $toUnits == $fromUnits || '' == $toUnits) {
            return floatval($inval);
        }

        $unRates = array(
            'N'          => 102,
            'carat'      => 0.2,
            'centigram'  => 0.01,
            'centner'    => 100000,
            'g'          => 1,
            'gram'       => 1,
            'kN'         => 102000,
            'kg'         => 1000,
            'kilogram'   => 1000,
            'kilonewton' => 102000,
            'kilotonne'  => 1000000000,
            'mcg'        => 0.000001,
            'mg'         => 0.001,
            'microgram'  => 0.000001,
            'milligram'  => 0.001,
            'newton'     => 102,
            'tonne'      => 1000000,
            'dr'         => 1.772,
            'dram'       => 1.772,
            'gr'         => 0.0648,
            'grain'      => 0.0648,
            'lb'         => 453.6,
            'ounce'      => 28.35,
            'pound'      => 453.6,
            'stone'      => 6350,
        );
        if (!isset($unRates[$toUnits]) || !isset($unRates[$fromUnits])) {
            return floatval($inval);
        }

        return floatval($inval * $unRates[$fromUnits] / $unRates[$toUnits]);
    }
}
if (!function_exists("findClosestKey")) {
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
if (!function_exists("hexstr")) {
    function hexstr($instring)
    {
        return hex2bin($instring);
    }
}
if (!function_exists("strhex")) {
    function strhex($instring)
    {
        return bin2hex($instring);
    }
}
if (!function_exists('excludedGST')) {
    /**
     * @param string $exc_gst
     *
     * @return string
     */
    function excludedGST($exc_gst)
    {
        return bcmul($exc_gst, '0.1', 2);
    }
}
if (!function_exists('includedGST')) {
    /**
     * @param string $inc_gst
     *
     * @return string
     */
    function includedGST($inc_gst)
    {
        return bcmul($inc_gst, '0.0909090909', 2);
    }
}
if (!function_exists('includeGST')) {
    /**
     * @param string $exc_gst
     *
     * @return string
     */
    function includeGST($exc_gst)
    {
        return bcadd($exc_gst, excludedGST($exc_gst), 2);
    }
}
if (!function_exists('excludeGST')) {
    /**
     * @param string $inc_gst
     *
     * @return string
     */
    function excludeGST($inc_gst)
    {
        return bcsub($inc_gst, includedGST($inc_gst), 2);
    }
}
