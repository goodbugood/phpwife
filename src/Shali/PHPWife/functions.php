<?php

if (!file_exists('str2time')) {
    /**
     * 涉及到加减月份，解决跳月问题，例如
     * 2022-03-31 的下个月是 2022-04-01，而不是 2022-05-01
     * 2022-03-31 的上个月是 2022-02-28，而不是 2022-03-03
     * 这里就与MySQL保持一致
     * SELECT DATE_ADD( '2022-03-31', INTERVAL -1 MONTH ); // MySQL: 2022-02-28
     * @param string $datetime
     * @param int $now
     * @return int
     */
    function str2time($datetime, $now = null)
    {
        $now = $now ?: \time();
        if (false !== \strpos($datetime, 'month')) {
            $now_year_month = \strtotime(\date('Y-m', $now));
            $compute_year_month = \strtotime($datetime, $now_year_month);
            $now_day = \date('j', $now);
            $now_days = \date('t', $now_year_month);
            $now_days = $now_day < $now_days ? $now_day : $now_days;
            $compute_days = \date('t', $compute_year_month);
            $days = $compute_days < $now_days ? $compute_days : $now_days;
            $compute_year_month_day = \date(\sprintf('Y-m-%d', $days), $compute_year_month);
            return \strtotime(
                \sprintf(
                    '%s %s',
                    $compute_year_month_day,
                    \date('H:i:s', \strtotime($datetime, $now))
                )
            );
        }

        return \strtotime($datetime, $now);
    }
}

if (!function_exists('equals')) {
    /**
     * 修复PHP8.0版本以下 0 == 'php' 返回 true 的问题
     * @param mixed $var1
     * @param mixed $var2
     * @param false $strict
     * @return bool
     */
    function equals($var1, $var2, $strict = false)
    {
        if (true === $strict) {
            return $var1 === $var2;
        } elseif (true === version_compare(PHP_VERSION, '8.0', '<')) {
            $scope = [0, '0', '', ' ', "\r", "\n", "\r\n", "\t", null, [],];
            if (0 === $var1 && !in_array($var2, $scope, true)) {
                return false;
            } elseif (0 === $var2 && !in_array($var1, $scope, true)) {
                return false;
            }
        }

        return $var1 == $var2;
    }
}