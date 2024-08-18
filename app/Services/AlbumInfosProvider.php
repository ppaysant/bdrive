<?php

namespace App\Services;

interface AlbumInfosProvider
{
    public function __construct(string $isbn);

    public function getDatas(): bool;

    public function hydrateAlbum(AlbumInfos $album): AlbumInfos;
}
