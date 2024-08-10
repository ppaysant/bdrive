<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlbumResource\Pages;
use App\Filament\Resources\AlbumResource\RelationManagers;
use App\Models\Album;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AlbumResource extends Resource
{
    protected static ?string $model = Album::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required(),
                Forms\Components\Textarea::make('summary')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('pages'),
                Forms\Components\Textarea::make('cover')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('isbn'),
                Forms\Components\Textarea::make('comment')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('read')
                    ->required(),
                Forms\Components\Toggle::make('complete')
                    ->required(),
                Forms\Components\Select::make('serie_id')
                    ->relationship('serie', 'title'),
                Forms\Components\TextInput::make('serie_issue')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pages')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('isbn')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\IconColumn::make('read')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('complete')
                    ->boolean(),
                Tables\Columns\TextColumn::make('serie.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('serie_issue')
                    ->numeric(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('title', 'asc')
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
            'index' => Pages\ListAlbums::route('/'),
            'create' => Pages\CreateAlbum::route('/create'),
            'edit' => Pages\EditAlbum::route('/{record}/edit'),
        ];
    }
}
