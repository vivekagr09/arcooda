<?php

include_once __DIR__ . '/common.inc.php';

global $DB;

/* Printing results in HTML */
if ($query = $DB->query(sprintf(
    "SELECT * FROM `products` WHERE `id` IN(3789, 2868, 2942, 2867, 3787, 2873, 3774, 2898, 2875, 2933, 3773, 2954, 3784, 2936, 3794, 2949, 2890, 3797, 2928, 2904, 2874, 2880, 2869, 2888, 2888, 2935, 2922, 2963, 2918, 3792, 3776, 2901, 3796, 2946, 2956, 3793, 3101, 2876, 3795, 2951, 3778, 2955, 2934, 2883, 2878, 2903, 3799, 3779, 3788, 2897, 2921, 2952, 2925, 2950, 2923, 2924, 1935, 3786, 2899, 2927, 2929, 2882, 2957, 3770, 2900, 2931, 3785, 3790, 3791, 2962, 2905, 2871, 2930, 2945, 2947, 2870, 2961, 2919, 2932, 3100, 3102, 2902, 4582, 3777, 2943, 2926, 2894, 3771, 2953, 2917, 3772, 3798, 2948, 2915, 2914, 2908, 2907, 2872, 2910, 3775, 2920, 2877)"
))) {
    while ($line = $DB->fetch_assoc($query)) {
        if (isset($line['image']) && ("" != $line['image'])) {
            $set_vals = [];
            foreach ($line as $k => $v) {
                if ('id' !== $k) {
                    $set_vals[] = sprintf(
                        "`%s` = %s",
                        $k,
                        $DB->escape($v)
                    );
                }
            }
            echo htmlspecialchars(sprintf(
                "UPDATE `products` SET %s WHERE `id` = %d LIMIT 1",
                implode(", ", $set_vals),
                $line['id']
            )) . "<br />\n";
        }
    }
    $DB->free_result($query);
}
