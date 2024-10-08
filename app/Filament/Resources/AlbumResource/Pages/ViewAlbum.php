<?php

namespace App\Filament\Resources\AlbumResource\Pages;

use App\Filament\Resources\AlbumResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAlbum extends ViewRecord
{
    protected static string $resource = AlbumResource::class;

    // Avoid displaying the relationManagers (authors and publishers)
    public function getRelationManagers(): array
    {
        return [];
    }
}
