<?php

namespace src\Util;

class Financial
{
    public static function PV($rate, $nper, $pmt) {
        $pv = 0;
        $rate /= 12;
        $nper *= 12;
        for($i = 1; $i <= $nper; $i++)
            $pv -= $pmt / pow((1 + $rate), $i);

        return round($pv);
    }

    public static function FV($rate, $nper, $pmt, $pv) {
        if($pmt == 0)
            $fv = ($pv * -1) * pow((1 + $rate), $nper);
        else
            $fv = $pmt * (pow((1 + $rate), $nper) - 1) / $rate;
        return round($fv) * -1;
    }

    public static function PMT($r, $nper, $pv, $fv, $t) {
        if ($r == 0) {
            $pmt = (-$pv - $fv) / $nper;
        } else {
            $r /= 12;
            $nper *= 12;
            $pmt = (-$fv * $r - $pv * $r * pow(1 + $r, $nper)) / ((1 + $r * $t) *(pow(1 + $r, $nper) - 1));
        }

        return round($pmt);
    }
}
