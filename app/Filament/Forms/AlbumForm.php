<?php

namespace App\Filament\Forms;

use App\Models\Album;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;

class AlbumForm
{
    public static function getForm(): array
    {
        return [
            TextInput::make('title')
                ->required(),
            Toggle::make('read')
                ->required(),
            TextInput::make('isbn'),
            Toggle::make('complete')
                ->required(),
            Select::make('serie_id')
                ->searchable()
                ->preload()
                ->relationship('serie', 'title')
                ->live()
                ->editOptionForm(SerieForm::getForm())
                ->createOptionForm(SerieForm::getForm()),
            TextInput::make('serie_issue')
                ->numeric()
                ->placeholder(fn(Get $get): string => Album::where('serie_id', $get('serie_id'))?->max('serie_issue') + 1),
            TextInput::make('pages'),
            MarkdownEditor::make('summary')
                ->columnSpanFull(),
            FileUpload::make('cover')
                ->image()
                ->imageEditor()
                ->directory('covers'),
            MarkdownEditor::make('comment')
                ->columnSpanFull(),
        ];
    }
}
