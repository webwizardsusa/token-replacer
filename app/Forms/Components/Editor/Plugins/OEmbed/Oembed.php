<?php

namespace App\Forms\Components\Editor\Plugins\OEmbed;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Support\Enums\MaxWidth;
use Filapress\RichEditor\Assets\ViteCss;
use Filapress\RichEditor\Assets\ViteScript;
use Filapress\RichEditor\Contracts\PluginHasDialog;
use Filapress\RichEditor\Contracts\PluginAsExternalAssets;
use Filapress\RichEditor\FPRichEditor;
use Filapress\RichEditor\Plugins\AbstractPlugin;
use Filapress\RichEditor\Support\HelpView;
use Filapress\RichEditor\Support\ServerRequestEvent;

class Oembed extends AbstractPlugin implements PluginHasDialog, PluginAsExternalAssets
{

    public function setup(FPRichEditor $editor): void
    {
       $this->onServerRequest('parseOembedLinks', function(ServerRequestEvent $event) {
           $urls = $event->arg('urls', []);
           $results = [];
           foreach($urls as $url) {
               $oembed = $this->oembedService()->fromUrl($url);
               $results[$url] = $oembed?->toArray();
           }
           return ['results' => $results ];
       });
    }

    public static function make(): static
    {
        return new static();
    }

    public function name(): string
    {
        return 'oembed';
    }

    public function oembedService():\Webwizardsusa\OEmbed\OEmbed
    {
        return app(\Webwizardsusa\OEmbed\OEmbed::class);
    }
    public function dialog(): Action
    {
        return Action::make('oembed-dialog')
            ->modalWidth(MaxWidth::FiveExtraLarge)
            ->arguments([
                'src' => null,
                'title' => null,
                'provider' => null,
                'invalid' => null,
            ])->mountUsing(function (ComponentContainer $form, array $arguments) {

                $arguments = array_merge([
                    'src' => null,
                    'preview' => null,
                    'title' => '',
                    'provider' => '',
                    'invalid' => false,
                ], $arguments);
                if ($arguments['src']) {
                    $arguments['initial_url'] = $arguments['src'];
                }

                if ($arguments['src']) {
                    $oembed = app(\Webwizardsusa\OEmbed\OEmbed::class)
                        ->fromUrl($arguments['src']);
                    if ($oembed) {
                        $arguments['preview'] = json_encode($oembed->toArray());
                        $arguments['title'] = $oembed->getTitle();
                        $arguments['provider'] = $oembed->getProvider();
                    }
                }
                $form->fill($arguments);
            })->modalHeading(function (array $arguments) {
                return \Arr::get($arguments, 'src') ? 'Update OEmbed' : 'Insert Oembed';
            })->form(function ($arguments) {
                $src = \Arr::get($arguments, 'src');
                return [
                    TextInput::make('src')
                        ->label('URL')
                        ->columnSpan('full')
                        ->required()
                        ->live()
                        ->afterStateUpdated(function($state, Set $set) {
                            if (!$state) {
                                return;
                            }
                            $oembed = app(\Webwizardsusa\OEmbed\OEmbed::class)
                                ->fromUrl($state);

                            $data = null;
                            if ($oembed) {
                                $data = json_encode($oembed->toArray());
                            }
                            $set('preview', $data);
                        })
                        ->validationAttribute('URL'),
                        OembedPreview::make('preview')
                    ->hidden(function ($state) { return !$state;})

                ];
            })
            ->action(function (FPRichEditor $component, $data, $arguments) {

                $component->sendActionToEditor('insertOembed', $arguments, $data);
            })
            ->extraModalFooterActions(function (Action $action): array {
return [];

            });
    }


    public function externalAssets(): array
    {
        return [
            ViteScript::make('resources/js/Editor/Image/image.js'),
        ];

    }


    public function getHelp(): mixed
    {
        $items = [];
        foreach($this->oembedService()->all() as $provider) {
            $items[] = \Str::title($provider->name());
        }
        return HelpView::make('OEmbed')

            ->html('Embed an item from another site via it\'s URL only. You can either paste a line in on a new line in the editor, or click the OEmbed button. <br /> <br />We support the following sites for embedding: ' . implode(', ', $items));

    }
}
