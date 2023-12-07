<?php

namespace src\Util;

class Financial2
{
    private const MONTH = 12;
    private const SMALL_NUMBER = 0.0000001;

    /**
     * @brief PV 현재가치를 계산하는 함수
     *
     * @param $rate float, 년 이자율
     * @param $nper int, 년 기간
     * @param $pmt int, 납입액
     * @return float|int 계산에 따라 소수점자리까지 계산이 간다.
     */
    public static function PV($rate, $nper, $pmt) {
        $ret = 0;

        $month_rate = $rate / self::MONTH;
        $month_nper = $nper * self::MONTH;
        for($i = 1; $i <= $month_nper; $i++)
            $ret -= $pmt / pow((1 + $month_rate), $i);

        return $ret;
    }

    /**
     * @brief FV 미래가치를 계산하는 함수
     *
     * @param $rate float, 년 이자율
     * @param $nper int, 년 기간
     * @param $pmt int, 납입액
     * @param $pv int, 현재가치
     * @return float
     */
    public static function FV($rate, $nper, $pmt, $pv) {
        if($pmt == 0)
            $fv = ($pv * -1) * pow((1 + $rate), $nper);
        else if (abs($rate) < self::SMALL_NUMBER)
            $fv = $pmt * $nper;
        else
            $fv = $pmt * (pow((1 + $rate), $nper) - 1) / $rate;

        return $fv * -1;
    }

    /**
     * @brief PMT 일정 금액을 정기적으로 납입하고 일정한 이자율이 적용되는 대출 상환금
     *
     * @param $rate float, 년 이자율
     * @param $nper int, 년 기간
     * @param $pv int, 현재가치
     * @param $fv int, 미래가치
     * @param $t int, 납입시점 0 or 1
     * @return float
     */
    public static function PMT($rate, $nper, $pv, $fv, $t) {
        $month_nper = $nper * self::MONTH;
        if (abs($rate) < self::SMALL_NUMBER) {
            if ($month_nper == 0) {
                return null;
            }
            $pmt = (-$pv - $fv) / $month_nper;
        } else {
            $month_rate = $rate / self::MONTH;
            if ($month_rate == -1) {
                return null;
            }
            $up = (-$fv * $month_rate - $pv * $month_rate * pow(1 + $month_rate, $month_nper));
            $down = ((1 + $month_rate * $t) * (pow(1 + $month_rate, $month_nper) - 1));
            $pmt = $up / $down;
        }

        return $pmt;
    }
}