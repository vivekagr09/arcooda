<?php
if (!function_exists('bcceil')) {
    /**
     * @param string $number
     *
     * @return string
     */
    function bcceil($number)
    {
        $diff = bcsub($number, bcadd($number, '0.0', 0), 4);
        if (bccomp($diff, '0.0', 4) != 0) {
            return bcadd($number, bcsub('1.0', $diff, 4), 0);
        }

        return bcadd($number, '0.0', 0);
    }
}
if (!function_exists('bcmax')) {
    /**
     * @param string[] $values
     * @param int      $scale
     *
     * @return string|false
     */
    function bcmax($values, $scale = 0)
    {
        if (!is_array($values)) {
            $values = func_get_args();
            $scale  = array_pop($values);
        }
        if (count($values) == 0) {
            return false;
        }

        $result = array_pop($values);
        while (count($values)) {
            $test = array_pop($values);
            if (bccomp($test, $result, $scale) > 0) {
                $result = $test;
            }
        }
        return $result;
    }
}
if (!function_exists('bcround')) {
    /**
     * @param string $number
     * @param int    $scale
     *
     * @return string
     */
    function bcround($number, $scale = 0)
    {
        $min = bcadd($number, '0.0', $scale);
        if (strpos($min, '.') === false) {
            $min .= '.';
        }

        $dif = bcadd('0.0', '0.0', $scale);
        if (strpos($dif, '.') === false) {
            $dif .= '.5';
        } else {
            $dif .= '5';
        }

        if (bccomp($number, bcadd($min, $dif, $scale + 1), $scale + 1) < 0) {
            $result = $min;
        } else {
            $result = bcadd($min, bcadd($dif, $dif, $scale), $scale);
        }

        //Stupid hack
        if ('-0.00' === $result) {
            return '0.00';
        }

        return $result;
    }
}
if (!function_exists('bcrounddown')) {
    /**
     * @param string $number
     * @param int    $scale
     *
     * @return string
     */
    function bcrounddown($number, $scale = 0)
    {
        if (substr($number, -2, 1) == '.') {
            $result = substr($number, 0, -2);
        } else {
            $result = substr($number, 0, -1);
        }

        return $result;
    }
}
if (!function_exists('bcroundup')) {
    /**
     * @param string $number
     *
     * @return string
     */
    function bcroundup($number)
    {
        $a      = substr($number, -1, 1);
        $result = substr($number, 0, -1);
        if (substr($result, -1, 1) == '.') {
            $result = substr($result, 0, -1);
        }

        if ($a > 0) {
            for ($i = strlen($result) - 1; $i > 0; $i--) {
                if (substr($result, $i, 1) == 9) {
                    $result = substr($result, 0, $i) . '0' . substr($result, $i + 1);
                } elseif (substr($result, $i, 1) == '.') {
                    //Do Nothing
                } else {
                    $result = substr($result, 0, $i) . (intval(substr($result, $i, 1)) + 1) . substr($result, $i + 1);
                    break;
                }
            }
        }

        return $result;
    }
}
