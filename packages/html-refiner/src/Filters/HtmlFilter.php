<?php

namespace Webwizardsusa\HtmlRefiner\Filters;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Webwizardsusa\HtmlRefiner\RefinerDefinition;

/**
 * @mixin HtmlSanitizerConfig;
 */
class HtmlFilter extends AbstractFilter
{
    protected AbstractFilter|HtmlSanitizerConfig $configuration;

    public function __construct()
    {
        $this->configuration = $this->makeConfiguration();
        parent::__construct();
    }

    protected function makeConfiguration(): HtmlSanitizerConfig
    {
        $config = new HtmlSanitizerConfig;

        return $config->allowElement('p')
            ->allowElement('br')
            ->allowElement('a', ['href']);
    }

    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->configuration, $name)) {
            $this->configuration = $this->configuration->{$name}(...$arguments);

            return $this;
        }
        throw new \BadMethodCallException("Method $name does not exist");
    }

    public function process(string $html, RefinerDefinition $definition): string
    {
        $configuration = clone $this->configuration;
        foreach ($definition->getCustomElements() as $element) {
            $configuration = $configuration->allowElement($element->tag(), $element->getAttributes());
        }

        $sanitizer = new HtmlSanitizer($configuration);

        return $sanitizer->sanitize($html);
    }
}
