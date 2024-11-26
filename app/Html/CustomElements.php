<?php

namespace App\Html;

class CustomElements
{

    public function __construct(array $elements = []) {
        $this->register($elements);
    }

    public function register(...$elements): static
    {
        foreach($elements as $element) {
            if (is_array($element)) {
                return $this->register(...$element);
            }
            if ($element instanceof CustomElement) {

            } else {
                $element = app($element);
            }

        }

        return $this;
    }

    protected function extract(string $tag, string $html): array {
        $tag = preg_quote($tag);
        $pattern = '/<' . $tag . '\b([^>]*?)\/?>.*?<\/' . $tag . '>|<' . $tag . '\b([^>]*?)\/>/';
        $results = [];
        if (preg_match_all($pattern, $html, $matches)) {

            foreach($matches[0] as $key=>$search){
                $attrString = trim($matches[1][$key] ?: $matches[2][$key]);
                $attributes = [];
                if (preg_match_all('/(\w+)=["\']([^"\']+)["\']/', $attrString, $attrMatches, PREG_SET_ORDER)) {
                    foreach($attrMatches as $item) {
                        $attributes[$item[1]] = $item[2];
                    }
                }

                $results[] = [
                    'raw' => $search,
                    'attributes' => $attributes
                ];
            }
        }
        return $results;
    }
}
