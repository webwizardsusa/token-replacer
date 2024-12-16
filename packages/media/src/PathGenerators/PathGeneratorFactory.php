<?php

namespace Filapress\Media\PathGenerators;

use Filapress\Media\Contracts\PathGeneratorContract;
use Illuminate\Support\Arr;

class PathGeneratorFactory
{
    public static function make(array|string $class): PathGeneratorContract
    {
        if (is_array($class)) {
            if (! isset($class['class'])) {
                throw new \Exception('Path must define a class');
            }

            return app($class['class'], ['configuration' => Arr::except($class, ['class'])]);
        } elseif (is_string($class)) {
            return app($class);
        }
    }
}
