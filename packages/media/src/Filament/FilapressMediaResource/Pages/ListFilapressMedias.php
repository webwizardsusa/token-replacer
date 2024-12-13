<?php

namespace Filapress\Media\Filament\FilapressMediaResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\ActionSize;
use Filapress\Media\Filament\FilapressMediaResource;
use Filapress\Media\MediaTypes;

class ListFilapressMedias extends ListRecords
{
    protected static string $resource = FilapressMediaResource::class;

    protected function getHeaderActions(): array
    {
        $types = [];
        foreach (app(MediaTypes::class)->all() as $type) {
            if ($type->userCan('create')) {
                $types[] = Action::make($type->label())
                    ->url(FilapressMediaResource::getUrl('create', ['type' => $type->name()]));
            }
        }

        if (empty($types)) {
            return [];
        }

        return [
            ActionGroup::make($types)
                ->icon('heroicon-o-plus')
                ->size(ActionSize::Medium)
                ->label('Create New')
                ->color('primary')
                ->button(),
        ];

    }
}
