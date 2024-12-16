<?php

namespace Filapress\Core;

use Illuminate\Support\Facades\Vite;

class Filapress
{
    protected static ?bool $runningHot = null;

    public static function viteRunningHot(): bool
    {
        if (static::$runningHot === null) {
            static::$runningHot = Vite::isRunningHot();
        }

        return static::$runningHot;
    }
}
