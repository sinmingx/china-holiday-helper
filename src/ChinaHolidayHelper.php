<?php
namespace SinMing\ChinaHoliday;

class ChinaHolidayHelper
{
    const SECONDS_IN_DAY  = 86400;
    const SECONDS_IN_WEEK = 604800;

    const HOLIDAY_TYPE          = 1;
    const OFFDAY_TYPE           = 2;
    const ADJUSTED_WORKDAY_TYPE = 3;
    const WORKDAY_TYPE          = 4;

    const DEFAULT_HOLIDAY_SATRT_DATE = '2006-12-01';
    const DEFAULT_START_DATE         = '1970-01-01';

    private static $data = null;

    /**
     * 获取法定节假日
     *
     * @param string $startDt 起始时间
     * @param string $endDt 结束时间
     * @param string $format 格式化
     * @return array 日期列表
     */
    public static function getHolidays($startDt = self::DEFAULT_HOLIDAY_SATRT_DATE, $endDt = '00:00', $format = 'Y-m-d')
    {
        $startDt = strtotime($startDt);
        $endDt   = strtotime($endDt);
        $days    = [];

        foreach (array_keys(self::getHolidayList()) as $date) {
            $unixTimestamp = strtotime($date);
            if ($unixTimestamp >= $startDt && $unixTimestamp <= $endDt) {
                $days[] = date($format, $unixTimestamp);
            }
        }

        return $days;
    }

    /**
     * 获取调休工作日
     *
     * @param string $startDt 起始时间
     * @param string $endDt 结束时间
     * @param string $format 格式化
     * @return array 日期列表
     */
    public static function getAdjustedWorkdays($startDt = self::DEFAULT_HOLIDAY_SATRT_DATE, $endDt = '00:00', $format = 'Y-m-d')
    {
        $startDt = strtotime($startDt);
        $endDt   = strtotime($endDt);
        $days    = [];

        foreach (array_keys(self::getAdjustedWorkdayList()) as $date) {
            $unixTimestamp = strtotime($date);
            if ($unixTimestamp >= $startDt && $unixTimestamp <= $endDt) {
                $days[] = date($format, $unixTimestamp);
            }
        }

        return $days;
    }

    /**
     * 获取休息日
     *
     * @param string $startDt 起始时间
     * @param string $endDt 结束时间
     * @param boolean $includeHolidays 包含法定节假日
     * @param string $format 格式化
     * @return array 日期列表
     */
    public static function getOffdays($startDt = self::DEFAULT_START_DATE, $endDt = '00:00', $includeHolidays = true, $format = 'Y-m-d')
    {
        $startDt          = strtotime($startDt);
        $endDt            = strtotime($endDt);
        $days             = $includeHolidays ? self::getHolidays($startDt, $endDt, $format) : [];
        $days             = [];
        $adjustedWorkdays = self::getAdjustedWorkdays();

        while ($startDt <= $endDt) {
            if (self::isWeekend($startDt)) {
                $days[] = date($format, $startDt);
            }
            $startDt += self::SECONDS_IN_DAY;
        }

        $days = array_unique(array_diff($days, $adjustedWorkdays));

        return $days;
    }

    /**
     * 获取工作日
     *
     * @param string $startDt 起始时间
     * @param string $endDt 结束时间
     * @param boolean $includeAdjustedWorkdays 包含调休工作日
     * @param string $format 格式化
     * @return array 日期列表
     */
    public static function getWorkdays($startDt = self::DEFAULT_START_DATE, $endDt = '00:00', $includeAdjustedWorkdays = true, $format = 'Y-m-d')
    {
        $startDt  = strtotime($startDt);
        $endDt    = strtotime($endDt);
        $days     = $includeAdjustedWorkdays ? array_keys(self::getAdjustedWorkdays($startDt, $endDt, $format)) : [];
        $holidays = self::getHolidays();

        while ($startDt <= $endDt) {
            if (self::isWeekday($startDt)) {
                $days[] = date($format, $startDt);
            }
            $startDt += self::SECONDS_IN_DAY;
        }

        $days = array_unique(array_diff($days, $holidays));

        return $days;
    }

    /**
     * 是否为休息日日
     *
     * @param string $dt 日期字符串
     * @param boolean $includeHolidays 包含法定节假日
     * @return boolean
     */
    public static function isOffday($dt, $includeHolidays = true)
    {
        //周末且不是调休工作日
        $isOffday = (self::isWeekend($dt) && !self::isAdjustedWorkday($dt));
        if ($includeHolidays && !$isOffday) {
            $isOffday = self::isHoliday($dt);
        }

        return $isOffday;
    }

    /**
     * 是否为工作日
     *
     * @param string $dt 日期字符串
     * @param boolean $includeAdjustedWorkdays 包含调休工作日
     * @return boolean
     */
    public static function isWorkday($dt, $includeAdjustedWorkdays = true)
    {
        // 平常日且不是法定节假日
        $isWorkday = (self::isWeekday($dt) && !self::isHoliday($dt));
        if ($includeAdjustedWorkdays && !$isWorkday) {
            $isWorkday = self::isAdjustedWorkday($dt);
        }

        return $isWorkday;
    }

    /**
     * 是否为法定节假日
     *
     * @param string $dt 日期字符串
     * @return boolean
     */
    public static function isHoliday($dt)
    {
        return array_key_exists($dt, self::getHolidayList());
    }

    /**
     * 是否为调休工作日
     *
     * @param string $dt 日期字符串
     * @return boolean
     */
    public static function isAdjustedWorkday($dt)
    {
        return array_key_exists($dt, self::getAdjustedWorkdayList());
    }

    /**
     * 获取日期类型
     *
     * @param string $dt 日期字符串
     * @return int
     */
    public static function getType($dt)
    {
        if (self::isHoliday($dt)) {
            return self::HOLIDAY_TYPE;
        }

        if (self::isOffday($dt)) {
            return self::OFFDAY_TYPE;
        }

        if (self::isAdjustedWorkday($dt)) {
            return self::ADJUSTED_WORKDAY_TYPE;
        }

        if (self::isWorkday($dt)) {
            return self::WORKDAY_TYPE;
        }
    }

    /**
     * 获取指定日期信息
     *
     * @param string $dt 日期字符串
     * @return DateInfo
     */
    public static function info($dt)
    {
        return new DateInfo($dt);
    }

    /**
     * 是否为周末（周六、周日）
     *
     * @param string $dt 日期字符串
     * @return boolean
     */
    public static function isWeekend($dt)
    {
        $dt = is_string($dt) ? strtotime($dt) : $dt;
        return date("N", $dt) >= 6;
    }

    /**
     * 是否为平常日（除周六、周日之外）
     *
     * @param string $dt 日期字符串
     * @return boolean
     */
    public static function isWeekday($dt)
    {
        $dt = is_string($dt) ? strtotime($dt) : $dt;
        return date("N", $dt) < 6;
    }

    public static function getHolidayList()
    {
        return self::loadData()['holidays'];
    }

    public static function getAdjustedWorkdayList()
    {
        return self::loadData()['adjusted_workdays'];
    }

    public static function loadData()
    {
        if (!isset(self::$data)) {
            self::$data = (include_once 'Data.php');
        }

        return self::$data;
    }
}
