<?php

namespace Webwizardsusa\TokenReplace\Transformers\Laravel;

use Webwizardsusa\TokenReplace\Contracts\Transformer;
use Webwizardsusa\TokenReplace\Exceptions\InvalidTransformerOptionsException;
use Webwizardsusa\TokenReplace\TokenReplacer;
use Illuminate\Http\UploadedFile;

class UploadedFileTransformer implements Transformer
{
    protected UploadedFile $uploadedFile;

    public function __construct(UploadedFile $uploadedFile)
    {
        $this->uploadedFile = $uploadedFile;
    }

    public function process(string $options, TokenReplacer $replacer): string
    {
        if (! $options) {
            throw new InvalidTransformerOptionsException('Upload transformer option required.');
        }

        return match ($options) {
            'basename' => (string) pathinfo($this->uploadedFile->getClientOriginalName(), PATHINFO_BASENAME),
            'extension' => (string) $this->uploadedFile->guessClientExtension(),
            default => '',
        };
    }
}
