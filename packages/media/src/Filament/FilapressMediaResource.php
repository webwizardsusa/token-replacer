<?php

namespace Filapress\Media\Filament;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filapress\Media\Filament\FilapressMediaResource\Pages;
use Filapress\Media\MediaCollection;
use Filapress\Media\MediaCollections;
use Filapress\Media\MediaType;
use Filapress\Media\MediaTypes;
use Filapress\Media\Models\FilapressMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FilapressMediaResource extends Resource
{
    protected static ?string $model = FilapressMedia::class;

    protected static ?string $slug = 'filapress-medias';

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $label = 'Media';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public static function table(Table $table): Table
    {
        $types = collect(app(MediaTypes::class)->all())
            ->filter(fn(MediaType $type) => $type->userCan('list'))
            ->mapWithKeys(fn(MediaType $type) => [$type->name() => $type->label()])
            ->toArray();

        $collections = collect(app(MediaCollections::class)->all())
            ->filter(fn(MediaCollection $type) => $type->canList(\Auth::user()))
            ->mapWithKeys(fn(MediaCollection $type) => [$type->name() => $type->label()])
            ->toArray();

        return $table
            ->recordClasses('filapress-media-table-record')
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('thumbnail_path')->label('Thumbnail')
                    ->width(150)
                    ->height(150)
                    ->disk(fn(FilapressMedia $media) => $media->thumbnail_disk),
                TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('type')
                    ->sortable()
                    ->formatStateUsing(fn(FilapressMedia $record) => $record->getType()->label()),
                TextColumn::make('collection')
                    ->sortable()
                    ->formatStateUsing(fn(FilapressMedia $record) => $record->getCollection() ? $record->getCollection()->label() : '-'),
                TextColumn::make('width')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('height')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('filesize')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('usages_count')
                    ->counts('usages')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('user.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->visible(!empty($types))
                    ->multiple()
                    ->options($types),
                SelectFilter::make('collection')
                    ->visible(!empty($collections))
                    ->multiple()
                    ->options($collections),
                TrashedFilter::make()
                    ->visible()
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                    RestoreAction::make()
                        ->visible(),
                    ForceDeleteAction::make()
                        ->visible(),
                ])
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make()
                        ->visible(),
                    ForceDeleteBulkAction::make()
                        ->visible(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFilapressMedias::route('/'),
            'create' => Pages\CreateFilapressMedia::route('/create/{type}'),
            'edit' => Pages\EditFilapressMedia::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title'];
    }
}
