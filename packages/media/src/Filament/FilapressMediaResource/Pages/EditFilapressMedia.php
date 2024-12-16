<?php

namespace Filapress\Media\Filament\FilapressMediaResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\EditRecord;
use Filapress\Media\Actions\UpdateMediaAction;
use Filapress\Media\Filament\FilapressMediaResource;
use Filapress\Media\MediaCollection;
use Filapress\Media\MediaCollections;
use Filapress\Media\MediaType;
use Filapress\Media\Models\FilapressMedia;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class EditFilapressMedia extends EditRecord
{
    protected static string $resource = FilapressMediaResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Edit '.$this->getType()->label();
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    public function getType(): MediaType
    {
        return $this->record->getType();
    }

    protected function getForms(): array
    {

        $form = $this->makeForm()
            ->operation('create')
            ->model($this->getModel())
            ->statePath($this->getFormStatePath())
            ->columns($this->hasInlineLabels() ? 1 : 2)
            ->inlineLabel($this->hasInlineLabels());

        $schema = $this->getType()->form($form, $this->getRecord());
        $collections = collect(app(MediaCollections::class)->all())
            ->filter(fn (MediaCollection $type) => $type->canCreate())
            ->mapWithKeys(fn (MediaCollection $type) => [$type->name() => $type->label()])
            ->toArray();
        if (! empty($collections)) {
            $schema[] = Select::make('collection')
                ->options($collections);
        }
        $form->schema($schema);

        return [
            'form' => $this->form($form),
        ];
    }

    protected function handleRecordUpdate(FilapressMedia|Model $record, array $data): Model
    {
        return UpdateMediaAction::run($record, $data);
    }
}
