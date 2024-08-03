<?php

namespace App\Services;

use App\Models\Album;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\Serie;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use PDO;

class MyLibraryService
{
    /**
     * Import from My Library sqlite database
     */
    public function import(string $mlpath, Command $cmd = null)
    {
        // verify path exists
        if (!file_exists($mlpath)) {
            return [];
        }

        $ml = new PDO('sqlite:' . $mlpath);

        // import authors
        $cmd->info('Importing authors...');
        $this->importAuthor($ml);

        // import publishers
        $cmd->info('Importing publishers...');
        $this->importPublishers($ml);

        // import series
        $cmd->info('Importing series...');
        $this->importSeries($ml);

        // import albums
        $cmd->info('Importing albums...');
        $this->importAlbums($ml);

        return [];
    }

    /**
     * Import authors from My Library sqlite database
     *
     * @param mixed $ml PDO object
     * @return void
     */
    public function importAuthor($ml)
    {
        $results = $ml->query('SELECT id, firstname, lastname FROM AUTHOR', PDO::FETCH_ASSOC);
        $results = $results->fetchAll();
        foreach ($results as $result) {
            $author = Author::where('firstname', $result['FIRSTNAME'])->where('lastname', $result['LASTNAME'])->first();
            if (empty($author)) {
                $author = new Author();
                $author->firstname = $result['FIRSTNAME'];
                $author->lastname = $result['LASTNAME'];
                $author->save();
            }
        }
    }

    /**
     * Import publishers from My Library sqlite database
     *
     * @param mixed $ml PDO object
     * @return void
     */
    public function importPublishers($ml)
    {
        $results = $ml->query('SELECT PUBLISHER FROM COMIC', PDO::FETCH_ASSOC);
        $results = $results->fetchAll();

        $publishers = [];
        foreach ($results as $result) {
            $title = Str::ucfirst(Str::lower($result['PUBLISHER']));
            if (!isset($publishers[$title])) {
                $publishers[$title] = true;
            }
        }

        foreach (array_keys($publishers) as $publisherName) {
            $publisher = Publisher::where('name', $publisherName)->first();
            if (empty($publisher)) {
                $publisher = new Publisher();
                $publisher->name = $publisherName;
                $publisher->save();
            }
        }
    }

    /**
     * Import Series from My Library sqlite database
     *
     * @param mixed $ml PDO object
     * @return void
     */
    public function importSeries($ml)
    {
        $results = $ml->query('SELECT ID, SERIES FROM COMIC', PDO::FETCH_ASSOC);
        $results = $results->fetchAll();

        $series = [];
        foreach ($results as $result) {
            if (empty($result['SERIES'])) {
                continue;
            }
            $series[$result['SERIES']] = true;
        }

        foreach (array_keys($series) as $serie) {
            $decodedSerie = json_decode($serie)[0];
            if (!isset($decodedSerie->title) or empty($decodedSerie->title)) {
                echo 'vide';
                continue;
            }

            $serie = Serie::where('title', $decodedSerie->title)->first();
            if (empty($serie)) {
                $serie = new Serie();
                $serie->title = $decodedSerie->title;
                $serie->save();
            }
        }
    }

    /**
     * Import albums from My Library sqlite database
     *
     * @param mixed $ml PDO object
     * @return void
     */
    public function importAlbums($ml)
    {
        $sth = $ml->prepare('SELECT COMIC.*, AUTHOR.FIRSTNAME AS AUTHOR_FIRSTNAME, AUTHOR.LASTNAME AS AUTHOR_LASTNAME FROM COMIC JOIN AUTHOR ON COMIC.AUTHOR = AUTHOR.ID');
        $sth->execute();
        $albumsML = $sth->fetchAll(PDO::FETCH_ASSOC);

        foreach ($albumsML as $albumML) {
            if (empty($albumML['TITLE'])) {
                continue;
            }

            $album = new Album();
            $album->title = $albumML['TITLE'];
            $album->summary = $albumML['SUMMARY'];
            $album->pages = $albumML['PAGES'];
            $album->isbn = $albumML['ISBN'];
            $album->save();

            // author
            if (!empty($albumML['AUTHOR'])) {
                $author = Author::where('firstname', $albumML['AUTHOR_FIRSTNAME'])->where('lastname', $albumML['AUTHOR_LASTNAME'])->first();
                if (!empty($author)) {
                    $album->authors()->attach($author);
                }
            }

            // additional_authors
            if (!empty($albumML['ADDITIONAL_AUTHORS'])) {
                $authorIds = json_decode($albumML['ADDITIONAL_AUTHORS']);
                $sth2 = $ml->prepare('SELECT * FROM AUTHOR WHERE ID = :authorId');
                foreach ($authorIds as $authorId) {
                    $sth2->bindParam('authorId', $authorId);
                    $sth2->execute();
                    $authorML = $sth2->fetch(PDO::FETCH_ASSOC);
                    if (!empty($authorML)) {
                        $author = Author::where('firstname', $authorML['FIRSTNAME'])->where('lastname', $authorML['LASTNAME'])->first();
                        if (!empty($author)) {
                            $album->authors()->attach($author);
                        }
                    }
                }
            }

            // serie
            if (!empty($albumML['SERIES'])) {
                $decodedSerie = json_decode($albumML['SERIES'])[0];
                if (isset($decodedSerie->title) and !empty($decodedSerie->title)) {
                    $serieTitleML = $decodedSerie->title;
                    $serie = Serie::where('title', $serieTitleML)->first();
                    if (!empty($serie)) {
                        $album->serie()->associate($serie);

                        if (isset($decodedSerie->volume) and !empty($decodedSerie->volume)) {
                            $album->serie_issue = (int)$decodedSerie->volume;
                        }
                    }
                }
            }

            // publisher
            if (!empty($albumML['PUBLISHER'])) {
                $publisher = Publisher::where('name', $albumML['PUBLISHER'])->first();
                if (!empty($publisher)) {
                    $album->publishers()->attach($publisher, ['published_date' => $albumML['PUBLISHED_DATE']]);
                }
            }

            $album->save();
        }
    }
}
