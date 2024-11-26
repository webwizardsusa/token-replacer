<?php

namespace Filapress\RichEditor\Plugins;

use Filapress\RichEditor\Contracts\PluginHasDialog;
use Filapress\RichEditor\FPRichEditor;
use DOMDocument;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Textarea;

class ViewSource extends AbstractPlugin implements PluginHasDialog
{

    public static function make(): static
    {
        return new static();
    }

    public function name(): string
    {
        return 'view_source';
    }

    public function dialog(): Action
    {
        return Action::make('view-source')
            ->fillForm(function ($arguments) {
                $source = \Arr::get($arguments, "source", "");
                if ($source) {
                    $dom = new DOMDocument();
                    $dom->preserveWhiteSpace = false; // Prevents extra spaces
                    $dom->formatOutput = true;       // Enables formatting
                    @$dom->loadHTML($source);          // Suppress warnings for invalid HTML
                    $body = $dom->getElementsByTagName('body')->item(0);
                    $source = '';
                    foreach ($body->childNodes as $node) {
                        $source .= $dom->saveHTML($node);
                    }

                }
                return ['source' => $source];
            })
            ->modalWidth('screen')
            ->modalHeading('View Source')
            ->form([
                TextArea::make('source')
                    ->label('HTML')
                    ->autosize()
                    ->extraAttributes(['class' => 'source_code_editor']),
            ])
            ->action(function (FPRichEditor $component, $data) {
                $content = $data['source'] ?? '<p></p>';

                $component->sendActionToEditor('setContent',[], ['content' => $content]);
            });
    }
}
