<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\TextInput;

class SerieForm
{
    public static function getForm(): array
    {
        return [
            TextInput::make('title')
                ->required(),
        ];
    }
}
