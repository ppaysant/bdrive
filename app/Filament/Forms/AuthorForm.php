<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\TextInput;

class AuthorForm
{
    public static function getForm(): array
    {
        return [
            TextInput::make('firstname'),
            TextInput::make('lastname')
                ->required(),
        ];
    }
}
