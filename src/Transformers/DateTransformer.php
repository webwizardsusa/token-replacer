<?php

namespace Webwizardsusa\TokenReplace\Transformers;

use DateTime;
use Webwizardsusa\TokenReplace\Contracts\Transformer;
use Webwizardsusa\TokenReplace\Exceptions\InvalidTransformerOptionsException;
use Webwizardsusa\TokenReplace\TokenReplacer;

class DateTransformer implements Transformer
{
    /**
     * @var DateTime
     */
    protected DateTime $date;

    public function __construct($date = null)
    {
        if (is_string($date)) {
            $date = (new DateTime())->setTimestamp(strtotime($date));
        }

        if (! $date) {
            $date = new DateTime();
        }
        $this->date = $date;
    }

    public function process(string $options, TokenReplacer $replacer): string
    {
        if (! $options) {
            throw new InvalidTransformerOptionsException('Date transformer option required');
        }

        return $this->date->format($options);
    }
}
