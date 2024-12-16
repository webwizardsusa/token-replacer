<?php

namespace Filapress\Core\Support;

class DateTimeUtils
{
    public static function parseIntervalFromString(string $interval)
    {
        return \DateInterval::createFromDateString($interval);
    }
}
