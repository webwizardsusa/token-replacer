<?php

namespace Webwizardsusa\OEmbed\Exceptions;

use Throwable;

class OembedNotFoundException extends OEmbedException
{
    public function __construct(string $message = 'The provider could not determine data for that oembed', int $code = 404, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
