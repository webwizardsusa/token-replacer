<?php

namespace Webwizardsusa\HtmlRefiner\Filters;

use Webwizardsusa\HtmlRefiner\RefinerDefinition;

class AutoParagraph extends AbstractFilter
{
    protected array $blocks = ['table',
        'thead',
        'tfoot',
        'caption',
        'col',
        'colgroup',
        'tbody',
        'tr',
        'td',
        'th',
        'div',
        'dl',
        'dd',
        'dt',
        'ul',
        'ol',
        'li',
        'pre',
        'select',
        'option',
        'form',
        'map',
        'area',
        'blockquote',
        'address',
        'math',
        'input',
        'p',
        'h[1-6]',
        'fieldset',
        'legend',
        'hr',
        'article',
        'aside',
        'details',
        'figcaption',
        'figure',
        'footer',
        'header',
        'hgroup',
        'menu',
        'nav',
        'section',
        'summary'
    ];
    protected array $ignore = [
        'pre',
        'script',
        'style',
        'object',
        'iframe',
        'drupal-media',
        'svg',
        '!--'
    ];


    public function block(string $block): static
    {
        if (!in_array($block, $this->blocks)) {
            $this->blocks[] = $block;
        }
        return $this;

    }

    public function removeBlock(string $block): static
    {
        if (in_array($block, $this->blocks)) {
            $this->blocks = array_diff($this->blocks, [$block]);
        }
        return $this;
    }


    public function ignore(string $ignore): static
    {
        if (!in_array($ignore, $this->ignore)) {
            $this->ignore[] = $ignore;
        }
        return $this;
    }

    public function removeIgnore(string $ignore): static
    {
        if (in_array($ignore, $this->ignore)) {
            $this->ignore = array_diff($this->ignore, [$ignore]);
        }
        return $this;
    }


    /**
     * Based off of Drupal's _filter_autop. See https://www.drupal.org/project/drupal
     *
     * @param string $html
     * @param RefinerDefinition $definition
     * @return string
     */
    public function process(string $html, RefinerDefinition $definition): string
    {
        foreach ($definition->getCustomElements() as $element) {
            if (!$element->isInline()) {
                $this->block($element->tag());
            }
        }
        $block = '(?:' . implode('|', $this->blocks) .')';

        // Split at opening and closing PRE, SCRIPT, STYLE, OBJECT, IFRAME tags
        // and comments. We don't apply any processing to the contents of these tags
        // to avoid messing up code. We look for matched pairs and allow basic
        // nesting. For example:
        // "processed <pre> ignored <script> ignored </script> ignored </pre> processed"
        $chunks = preg_split('@(<!--.*?-->|</?(?:' . implode('|' , $this->ignore) . ')[^>]*>)@i', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
        // Note: PHP ensures the array consists of alternating delimiters and literals
        // and begins and ends with a literal (inserting NULL as required).
        $ignore = FALSE;
        $ignore_tag = '';
        $output = '';
        foreach ($chunks as $i => $chunk) {
            if ($i % 2) {
                if (str_starts_with($chunk, '<!--')) {
                    // Nothing to do, this is a comment.
                    $output .= $chunk;
                    continue;
                }
                // Opening or closing tag?
                $open = ($chunk[1] != '/');
                [$tag] = preg_split('/[ >]/', substr($chunk, 2 - $open), 2);
                if (!$ignore) {
                    if ($open) {
                        $ignore = TRUE;
                        $ignore_tag = $tag;
                    }
                }
                // Only allow a matching tag to close it.
                elseif (!$open && $ignore_tag == $tag) {
                    $ignore = FALSE;
                    $ignore_tag = '';
                }
            }
            elseif (!$ignore) {
                // Skip if the next chunk starts with Twig theme debug.
                // @see twig_render_template()
                if (isset($chunks[$i + 1]) && $chunks[$i + 1] === '<!-- THEME DEBUG -->') {
                    $chunk = rtrim($chunk, "\n");
                    $output .= $chunk;
                    continue;
                }

                // Skip if the preceding chunk was the end of a Twig theme debug.
                // @see twig_render_template()
                if (isset($chunks[$i - 1])) {
                    if (
                        str_starts_with($chunks[$i - 1], '<!-- BEGIN OUTPUT from ')
                        || str_starts_with($chunks[$i - 1], '<!-- 💡 BEGIN CUSTOM TEMPLATE OUTPUT from ')
                    ) {
                        $chunk = ltrim($chunk, "\n");
                        $output .= $chunk;
                        continue;
                    }
                }

                // Just to make things a little easier, pad the end
                $chunk = preg_replace('|\n*$|', '', $chunk) . "\n\n";
                $chunk = preg_replace('|<br />\s*<br />|', "\n\n", $chunk);
                // Space things out a little
                $chunk = preg_replace('!(<' . $block . '[^>]*>)!', "\n$1", $chunk);
                // Space things out a little
                $chunk = preg_replace('!(</' . $block . '>)!', "$1\n\n", $chunk);
                // Take care of duplicates
                $chunk = preg_replace("/\n\n+/", "\n\n", $chunk);
                $chunk = preg_replace('/^\n|\n\s*\n$/', '', $chunk);
                // Make paragraphs, including one at the end
                $chunk = '<p>' . preg_replace('/\n\s*\n\n?(.)/', "</p>\n<p>$1", $chunk) . "</p>\n";
                // Problem with nested lists
                $chunk = preg_replace("|<p>(<li.+?)</p>|", "$1", $chunk);
                $chunk = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $chunk);
                $chunk = str_replace('</blockquote></p>', '</p></blockquote>', $chunk);
                // Under certain strange conditions it could create a P of entirely whitespace
                $chunk = preg_replace('|<p>\s*</p>\n?|', '', $chunk);
                $chunk = preg_replace('!<p>\s*(</?' . $block . '[^>]*>)!', "$1", $chunk);
                $chunk = preg_replace('!(</?' . $block . '[^>]*>)\s*</p>!', "$1", $chunk);
                // Make line breaks
                $chunk = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $chunk);
                $chunk = preg_replace('!(</?' . $block . '[^>]*>)\s*<br />!', "$1", $chunk);
                $chunk = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)>)!', '$1', $chunk);
                $chunk = preg_replace('/&([^#])(?![A-Za-z0-9]{1,8};)/', '&amp;$1', $chunk);
            }
            $output .= $chunk;
        }
        return $output;
    }
}
