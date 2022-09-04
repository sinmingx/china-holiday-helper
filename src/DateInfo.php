<?php
namespace SinMing\ChinaHoliday;

use SinMing\ChinaHoliday\ChinaHolidayHelper;

class DateInfo
{
    protected $adjustedWorkReason;
    protected $holidayName;
    protected $remark;
    protected $isHoliday;
    protected $isAdjustedWorkday;
    protected $isOffday;
    protected $isWorkday;
    protected $date;
    protected $timestamp;
    protected $type;

    public function __construct($date)
    {
        $this->date      = $date;
        $this->timestamp = strtotime($date);
        $this->init();
    }

    /**
     * 是否为法定节假日
     *
     * @return boolean
     */
    public function isHoliday()
    {
        return $this->isHoliday = is_null($this->isHoliday) ? ChinaHolidayHelper::isHoliday($this->date) : $this->isHoliday;
    }

    /**
     * 是否为调休工作日
     *
     * @return boolean
     */
    public function isAdjustedWorkday()
    {
        return $this->isAdjustedWorkday = is_null($this->isAdjustedWorkday) ? ChinaHolidayHelper::isAdjustedWorkday($this->date) : $this->isAdjustedWorkday;
    }

    /**
     * 是否为休息日
     *
     * @param boolean $includeHolidays 包含法定节假日
     * @return boolean
     */
    public function isOffday($includeHolidays = true)
    {
        return $this->isOffday = is_null($this->isOffday) ? ChinaHolidayHelper::isOffday($this->date, $includeHolidays) : $this->isOffday;
    }

    /**
     * 是否为工作日
     *
     * @param boolean $includeAdjustedWorkdays
     * @return boolean
     */
    public function isWorkday($includeAdjustedWorkdays = true)
    {
        return $this->isWorkday = is_null($this->isWorkday) ? ChinaHolidayHelper::isWorkday($this->date, $includeAdjustedWorkdays) : $this->isWorkday;
    }

    /**
     * 获取日期类型
     *
     * @return int
     */
    public function getType()
    {
        return $this->type = is_null($this->type) ? ChinaHolidayHelper::getType($this->date) : $this->type;
    }

    /**
     * 获取日期
     *
     * @param string $format 格式化
     * @return string
     */
    public function getDate($format = 'Y-m-d')
    {
        return date($format, $this->timestamp);
    }

    /**
     * 获取节假日名称
     *
     * @return string
     */
    public function getHolidayName()
    {
        if ($this->isHoliday() && is_null($this->isHoliday)) {
            $this->holidayName = ChinaHolidayHelper::getHolidayList()[$this->date]['holiday_name'];
        }

        return $this->holidayName;
    }

    /**
     * 获取调休理由
     *
     * @return string
     */
    public function getAdjustedWorkReason()
    {
        if ($this->isAdjustedWorkday() && is_null($this->adjustedWorkReason)) {
            $this->adjustedWorkReason = ChinaHolidayHelper::getAdjustedWorkdayList()[$this->date]['adjusted_work_reason'];
        }

        return $this->adjustedWorkReason;
    }

    /**
     * 获取备注
     *
     * @return string
     */
    public function getRemark()
    {
        if (is_null($this->remark)) {
            $remark = $this->getHolidayName() ?: $this->getAdjustedWorkReason();
            if (empty($remark)) {
                $remark = $this->isWorkday() ? '普通工作日' : '普通休息日';
            }
            $this->remark = $remark;
        }

        return $this->remark;

    }

    /**
     * 初始化
     *
     * @return void
     */
    private function init()
    {
        $this->isHoliday();
        $this->isAdjustedWorkday();
        $this->isOffday();
        $this->isWorkday();
        $this->getType();
        $this->getRemark();
    }

}
