<?php

namespace App\Filament\Forms\ProviderForm;

use App\Models\Publisher;
use App\Models\Serie;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class BnfProviderForm
{
    public static function getForm(string|null $operation = null): array
    {
        $formSchema = [];

        array_push($formSchema,
            Checkbox::make('getTitle')
                ->hiddenLabel()
                ->columnSpan(1),
            TextInput::make('title')
                ->disabled(true)
                ->dehydrated()
                ->columnSpan(11),
            Checkbox::make('getResume')
                ->hiddenLabel()
                ->columnSpan(1),
            Textarea::make('resume')
                ->rows(10)
                ->disabled(true)
                ->dehydrated()
                ->columnSpan(11),
            Checkbox::make('getPages')
                ->hiddenLabel()
                ->columnSpan(1),
            TextInput::make('materialDescription')
                // ->disabled(true)
                ->dehydrated()
                ->columnSpan(11),
            Checkbox::make('getSerie')
                ->hiddenLabel()
                ->columnSpan(1),
            TextInput::make('original_serie')
                ->disabled(true)
                ->dehydrated()
                ->columnSpan(11),
            Select::make('serie')
                ->searchable()
                ->getSearchResultsUsing(fn(string $search): array => Serie::where('title', 'like', "%{$search}%")->limit(50)->pluck('title', 'id')->toArray())
                ->columnStart(2)
                ->columnSpan(11),
            Checkbox::make('getSerieIssue')
                ->hiddenLabel()
                ->columnSpan(1),
            TextInput::make('serie_issue')
                // ->disabled(true)
                ->dehydrated()
                ->columnSpan(11),

        );

        if ($operation === 'edit') {
            array_push($formSchema,
                Checkbox::make('getPublisher')
                    ->hiddenLabel()
                    ->columnSpan(1),
                TextInput::make('original_publisher')
                    ->disabled(true)
                    ->dehydrated()
                    ->columnSpan(11),
                Select::make('publisher')
                    ->searchable()
                    ->getSearchResultsUsing(fn(string $search): array => Publisher::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id')->toArray())
                    ->columnStart(2)
                    ->columnSpan(11),
                Checkbox::make('getAuthors')
                    ->hiddenLabel()
                    ->columnSpan(1),
                TextInput::make('authors')
                    ->disabled(true)
                    ->dehydrated()
                    ->columnSpan(11)
            );
        }

        $form = [
            Fieldset::make()->schema(
                $formSchema
            )->columns(12),
        ];

        return $form;
    }
}
