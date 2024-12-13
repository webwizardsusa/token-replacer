<?php

namespace Filapress\Media\PathGenerators;

use Filapress\Media\Contracts\PathGeneratorContract;
use Filapress\Media\Support\FileUtils;

class DateBasedPathGenerator implements PathGeneratorContract
{
    protected array $configuration;

    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    public static function configure(string $basePath, string $pattern = 'Y/m'): array
    {
        return [
            'class' => static::class,
            'basePath' => $basePath,
            'pattern' => $pattern,
        ];
    }

    public function generate(): string
    {
        return FileUtils::resolvePath($this->configuration['basePath'], date($this->configuration['pattern']));
    }
}
