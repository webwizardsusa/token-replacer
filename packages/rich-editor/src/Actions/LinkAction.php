<?php

namespace Filapress\RichEditor\Actions;

use Filapress\RichEditor\FPRichEditor;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class LinkAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'link-dialog';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->modalWidth('lg')
            ->arguments([
                'href' => '',
                'id' => '',
                'hreflang' => '',
                'target' => '',
                'rel' => '',
                'referrerpolicy' => '',
                'as_button' => false,
                'button_theme' => '',
            ])->mountUsing(function (ComponentContainer $form, array $arguments) {
                $arguments = array_merge([
                    'href' => '',
                    'id' => '',
                    'hreflang' => '',
                    'target' => '',
                    'rel' => '',
                    'referrerpolicy' => '',
                    'as_button' => false,
                    'button_theme' => '',
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
                $component->sendActionToEditor(!$remove ? 'setLink' : 'unsetLink',$arguments, $data);
            })->extraModalFooterActions(function (Action $action): array {

                if (\Arr::get($action->getArguments(), 'href', '') !== '') {
                    return [
                        $action->makeModalSubmitAction('remove_link', ['remove' => true])
                            ->color('danger')
                            ->extraAttributes(function () use ($action) {
                                return [
                                    'style' => 'margin-inline-start: auto;',
                                ];
                            }),
                    ];
                }

                return [];
            });
    }


}
