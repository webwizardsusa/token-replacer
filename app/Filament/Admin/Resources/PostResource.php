<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PostResource\Pages;
use App\Forms\Components\Editor\Plugins\OEmbed\Oembed;
use App\Html\PostDefinition;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filapress\Media\Editor\MediaPlugin;
use Filapress\Media\Filament\Components\Form\MediaBrowserField;
use Filapress\RichEditor\FPRichEditor;
use Filapress\RichEditor\Plugins\Blockquote;
use Filapress\RichEditor\Plugins\Blocks;
use Filapress\RichEditor\Plugins\Bold;
use Filapress\RichEditor\Plugins\Italic;
use Filapress\RichEditor\Plugins\Link;
use Filapress\RichEditor\Plugins\OrderedList;
use Filapress\RichEditor\Plugins\Strike;
use Filapress\RichEditor\Plugins\Underline;
use Filapress\RichEditor\Plugins\UnorderedList;
use Filapress\RichEditor\Plugins\ViewSource;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Forms\Components\TextInput::make('title')->required(),
                MediaBrowserField::make('image_id')
                    ->types(['image'])
                    ->collection('content')
                    ->label('Image'),
                TextArea::make('description')
                    ->autosize()
                    ->helperText('A short description of the post. This is text only and supports only a single paragraph')
                    ->required(),
                FPRichEditor::make('body')
                    ->plugins([
                        Bold::make(),
                        Italic::make(),
                        Underline::make(),
                        Strike::make(),
                        Link::make(),
                        Blockquote::make(),
                        OrderedList::make(),
                        UnorderedList::make(),
                        Blocks::make()
                            ->addBlock('Heading 1', 'toggleHeading', ['level' => 3], 'heading')
                            ->addBlock('Heading 2', 'toggleHeading', ['level' => 4], 'heading')
                            ->addBlock('Heading 3', 'toggleHeading', ['level' => 5], 'heading'),
                        ViewSource::make(),
                        Oembed::make(),
                        MediaPlugin::make()
                            ->collection('content'),
                    ])
                    ->afterStateHydrated(function (FPRichEditor $component, $state) {
                        if ($state) {
                            $component->state(PostDefinition::for($state)->context('editor')->parse());

                        }
                    })
                    ->buttons([
                        'blocks', 'bold', 'italic', 'underline', 'strike', '|', 'link', 'blockquote', 'media', 'oembed', 'orderedList', 'unorderedList', '|', 'view_source',
                    ]),

                Forms\Components\Section::make('Settings')
                    ->columns(1)
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                        ->label('Author'),

                        Toggle::make('published')->label('Published')
                            ->live(),
                        DateTimePicker::make('published_at')->label('Publish Date')
                            ->visible(fn(Forms\Get $get): bool => $get('published'))
                            ->helperText('You can set the published date, or leave blank to set the published date to the time when the post is saved.')
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->sortable()->dateTime(),
                Tables\Columns\TextColumn::make('created_at')->sortable()->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
