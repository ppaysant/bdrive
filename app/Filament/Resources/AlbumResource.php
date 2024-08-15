<?php

namespace App\Filament\Resources;

use App\Filament\Forms\AlbumForm;
use App\Filament\Resources\AlbumResource\Pages;
use App\Models\Album;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AlbumResource extends Resource
{
    protected static ?string $model = Album::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(AlbumForm::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    ImageColumn::make('cover')
                        ->height(100)
                        ->grow(false)
                        ->defaultImageUrl(url('/storage/no-picture.png')),
                    Stack::make([
                        TextColumn::make('title')
                            ->searchable(['albums.title'])
                            ->sortable()
                            ->weight(FontWeight::Bold)
                            ->state(fn (Album|null $record): string => $record->albumTitle),
                        TextColumn::make('authors_list')
                            ->size(TextColumn\TextColumnSize::ExtraSmall),
                        TextColumn::make('serie_title')
                            ->sortable(query: function(Builder $query, string $direction): Builder {
                                // FIXME: leftJoin would be best but causes strange error (edit route has no record...)
                                return $query
                                    ->join('series', 'series.id', '=', 'albums.serie_id')
                                    ->orderBy('series.title', $direction);
                            })
                            ->searchable()
                            ->size(TextColumn\TextColumnSize::ExtraSmall)
                            ->state(fn (Album|null $record): string => (
                                !empty($record?->serie?->title) ?
                                    $record->serie->title . ', ' . (!empty($record->serie_issue) ? 'tome ' . $record->serie_issue : '')
                                    : ''
                            )),
                        TextColumn::make('pages')
                            ->sortable()
                            ->state(fn (Album|null $record): string => (!empty($record->pages) ? $record->pages . ' pages' : ''))
                            ->size(TextColumn\TextColumnSize::ExtraSmall),
                        TextColumn::make('complete_badge')
                            ->state(fn (Album|null $record): string => ($record->complete ? 'Complete' : ''))
                            ->badge(),
                    ]),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('authors', 'serie')->select('*', 'albums.title as albumTitle'))
            ->contentGrid([
                'sm' => 1,
                'lg' => 3,
            ])
            ->defaultSort('title', 'asc')
            ->filters([
                TernaryFilter::make('complete'),
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
            'index' => Pages\ListAlbums::route('/'),
            'create' => Pages\CreateAlbum::route('/create'),
            'edit' => Pages\EditAlbum::route('/{record}/edit'),
        ];
    }
}
