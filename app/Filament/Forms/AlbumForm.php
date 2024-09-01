<?php

namespace App\Filament\Forms;

use App\Models\Album;
use App\Services\AlbumInfos;
use App\Services\AlbumInfosBnfProvider;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;

class AlbumForm
{
    public static function getForm(): array
    {
        return [
            TextInput::make('title')
                ->required(),
            Toggle::make('read')
                ->required(),
            TextInput::make('isbn')
                ->suffixAction(
                    Action::make('getInfosFromBnf')
                        ->icon('heroicon-s-inbox-arrow-down')
                        ->mountUsing(function (Form $form, Album $record) {
                            $form->fill();

                            $album = new AlbumInfos();
                            $bnf = new AlbumInfosBnfProvider($record->isbn);
                            if (!$bnf->getDatas()) {
                                return;
                            }
                            $album = $bnf->hydrateAlbum($album);
                            $form->fill([
                                'title' => $album->title,
                                'resume' => $album->resume,
                                'serie' => $album->serie,
                                'serie_issue' => $album->serie_issue,
                                'publisher' => $album->publisher,
                            ]);
                        })
                        ->form([
                            Fieldset::make()->schema([
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
                                Checkbox::make('getSerie')
                                    ->hiddenLabel()
                                    ->columnSpan(1),
                                TextInput::make('serie')
                                    ->disabled(true)
                                    ->dehydrated()
                                    ->columnSpan(11),
                                Checkbox::make('getSerieIssue')
                                    ->hiddenLabel()
                                    ->columnSpan(1),
                                TextInput::make('serie_issue')
                                    ->disabled(true)
                                    ->dehydrated()
                                    ->columnSpan(11),
                                Checkbox::make('getPublisher')
                                    ->hiddenLabel()
                                    ->columnSpan(1),
                                TextInput::make('publisher')
                                    ->disabled(true)
                                    ->dehydrated()
                                    ->columnSpan(11),
                            ])->columns(12),
                        ])
                        ->action(function (array $data, Set $set) {
                            if ($data['getTitle'] == 1) {
                                $set('title', $data['title']);
                            }
                            if ($data['getResume'] == 1) {
                                $set('summary', $data['resume']);
                            }
                            if ($data['getSerieIssue'] == 1) {
                                $set('serie_issue', $data['serie_issue']);
                            }
                        })
                ),
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
