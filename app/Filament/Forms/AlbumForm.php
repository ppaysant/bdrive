<?php

namespace App\Filament\Forms;

use App\Filament\Forms\ProviderForm\BnfProviderForm;
use App\Models\Album;
use App\Models\Publisher;
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
    public static function getForm(string|null $operation = null): array
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
                        ->mountUsing(function (Form $form, $state) {
                            $form->fill();

                            $album = new AlbumInfos();
                            $bnf = new AlbumInfosBnfProvider($state);
                            if (!$bnf->getDatas()) {
                                return;
                            }
                            $album = $bnf->hydrateAlbum($album);
                            $form->fill([
                                'title' => $album->title,
                                'resume' => $album->resume,
                                'materialDescription' => $album->materialDescription,
                                'original_serie' => $album->serie,
                                'serie_issue' => $album->serie_issue,
                                'original_publisher' => $album->publisher,
                                'authors' => join(', ', $album->authors),
                            ]);
                        })
                        ->form(BnfProviderForm::getForm($operation))
                        ->action(function (array $data, Set $set) use ($operation) {
                            if ($data['getTitle'] == 1) {
                                $set('title', $data['title']);
                            }
                            if ($data['getResume'] == 1) {
                                $set('summary', $data['resume']);
                            }
                            if ($data['getPages'] == 1) {
                                $set('pages', $data['materialDescription']);
                            }
                            if ($data['getSerie'] == 1) {
                                $set('serie_id', $data['serie']);
                            }
                            if ($data['getSerieIssue'] == 1) {
                                $set('serie_issue', $data['serie_issue']);
                            }
                            // if ($data['getPublisher'] == 1) {
                            //     $set('publisher', $data['publisher']);
                            // }
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
