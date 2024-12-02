<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PostResource\Pages;
use App\Forms\Components\Editor\Plugins\Image\Image;
use App\Forms\Components\Editor\Plugins\OEmbed\Oembed;
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
use Filapress\RichEditor\FPRichEditor;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

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
                        Image::make(),
                        Blocks::make()
                            ->addBlock('Heading 1', 'toggleHeading', ['level' => 3], 'heading')
                            ->addBlock('Heading 2', 'toggleHeading', ['level' => 4], 'heading')
                            ->addBlock('Heading 3', 'toggleHeading', ['level' => 5], 'heading'),
                        ViewSource::make(),
                        Oembed::make(),
                    ])
                    ->buttons([
                        'blocks', 'bold', 'italic', 'underline', 'strike', '|', 'link', 'blockquote', 'image', 'oembed', 'orderedList','unorderedList',  '|', 'view_source'
                    ]),


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
