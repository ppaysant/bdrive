<?php

namespace App\Services;

class AlbumInfos
{
    // public $refBnf = '';
    // public $urlBnf = '';
    public $urlCover = '';
    public $title = '';
    public $publisher = '';
    public $authors = [];
    public $resume = '';
    public $serie = '';
    public $serie_issue = '';

    public function toArray(): array
    {
        return [
            'urlCover' => $this->urlCover,
            'title' => $this->title,
            'publisher' => $this->publisher,
            'authors' => $this->authors,
            'resume' => $this->resume,
            'serie' => $this->serie,
            'serie_issue' => $this->serie_issue,
        ];
    }
}
