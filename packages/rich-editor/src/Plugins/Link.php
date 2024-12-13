<?php

namespace Filapress\RichEditor\Plugins;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filapress\RichEditor\Contracts\PluginHasDialog;
use Filapress\RichEditor\FPRichEditor;
use Filapress\RichEditor\Support\HelpView;

class Link extends AbstractPlugin implements PluginHasDialog
{
    public static function make(): static
    {
        return new static;
    }

    public function dialog(): Action
    {
        return Action::make('link-dialog')
            ->modalWidth('lg')
            ->arguments([
                'href' => '',
                'target' => '',
            ])->mountUsing(function (ComponentContainer $form, array $arguments) {
                $arguments = array_merge([
                    'href' => '',
                    'target' => '',
                ], $arguments);
                $form->fill($arguments);
            })->modalHeading(function (array $arguments) {
                return \Arr::has($arguments, 'href') ? 'Update Link' : 'Insert Link';
            })->form([
                TextInput::make('href')
                    ->label('URL')
                    ->columnSpan('full')
                    ->required()
                    ->validationAttribute('URL'),
                Select::make('target')
                    ->selectablePlaceholder(false)
                    ->options([
                        '' => 'Current tab',
                        '_blank' => 'New tab',
                    ]),

            ])->action(function (FPRichEditor $component, $data, $arguments) {
                $remove = \Arr::get($arguments, 'remove', false);
                $data['inclusive'] = false;
                $component->sendActionToEditor(! $remove ? 'setLink' : 'unsetLink', $arguments, $data);
            })->extraModalFooterActions(function (Action $action): array {

                if (\Arr::get($action->getArguments(), 'href', '') !== '') {
                    return [
                        $action->makeModalSubmitAction('remove_link', ['remove' => true])
                            ->color('danger')
                            ->extraAttributes(function () {
                                return [
                                    'style' => 'margin-inline-start: auto;',
                                ];
                            }),
                    ];
                }

                return [];
            });
    }

    public function name(): string
    {

        return 'link';
    }

    public function getHelp(): mixed
    {
        return HelpView::make('Link')
            ->text('Create a hyperlink to another page or resource. You may specify if it should open in the current tab/window or a new tab/window.');

    }
}
