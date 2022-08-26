<?php

namespace SinMing\ChinaHoliday;

use PHPUnit\Framework\TestCase;
use SinMing\ChinaHoliday\ChinaHolidayHelper;

class ChinaHolidayTest extends TestCase
{
    public function testGetHolidays()
    {
        $days = ChinaHolidayHelper::getHolidays();
        $this->assertIsArray($days);
    }

    public function testGetAdjustedWorkdays()
    {
        $days = ChinaHolidayHelper::getAdjustedWorkdays();
        $this->assertIsArray($days);
    }

    public function testGetOffdays()
    {
        $days = ChinaHolidayHelper::getOffdays();
        $this->assertIsArray($days);
    }

    public function testGetWorkdays()
    {
        $days = ChinaHolidayHelper::getWorkdays();
        $this->assertIsArray($days);
    }

    public function testIsHoliday()
    {
        $days = ChinaHolidayHelper::getHolidays();
        foreach ($days as $day) {
            $bool = ChinaHolidayHelper::isHoliday($day);
            $this->assertTrue($bool);
        }
    }

    public function testIsAdjustedWorkday()
    {
        $days = ChinaHolidayHelper::getAdjustedWorkdays();
        foreach ($days as $day) {
            $bool = ChinaHolidayHelper::isAdjustedWorkday($day);
            $this->assertTrue($bool);
        }
    }

    public function testIsOffday()
    {
        $days = ChinaHolidayHelper::getOffdays();
        foreach ($days as $day) {
            $bool = ChinaHolidayHelper::isOffday($day);
            $this->assertTrue($bool);
        }
    }

    public function testIsWorkday()
    {
        $days = ChinaHolidayHelper::getWorkdays();
        foreach ($days as $day) {
            $bool = ChinaHolidayHelper::isWorkday($day);
            $this->assertTrue($bool);
        }
    }
}
