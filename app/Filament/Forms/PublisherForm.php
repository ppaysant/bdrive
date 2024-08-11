<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\TextInput;

class PublisherForm
{
    public static function getForm(): array
    {
        return [
            TextInput::make('name')
                ->required(),
            TextInput::make('url')
                ->prefixIcon('heroicon-o-globe-alt'),
            TextInput::make('search_album_url')
                ->prefixIcon('heroicon-o-globe-alt'),
            TextInput::make('search_author_url')
                ->prefixIcon('heroicon-o-globe-alt'),
        ];
    }
}
