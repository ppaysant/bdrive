<?php

namespace App\Services;

/**
 * sru (Search/Retrieval via Url) quickly : https://www.bnf.fr/sites/default/files/2019-04/service_sru_bnf.pdf
 * gallica https://gallica.bnf.fr/services/engine/search/sru?operation=searchRetrieve&version=1.2&query=%28gallica%20all%20%229791091146418%22%29&lang=fr&suggest=0
 * bnf listing https://catalogue.bnf.fr/ark:/12148/cb412255468
 * search bnf : https://catalogue.bnf.fr/api/SRU?version=1.2&operation=searchRetrieve&query=bib.isbn%20all%20%22%279782864973270%22
 * unimark doc : https://www.transition-bibliographique.fr/unimarc/manuel-unimarc-format-bibliographique/
 * cover api https://github.com/hackathonBnF/hackathon2016/wiki/API-Couverture-Service
 * cover example : https://catalogue.bnf.fr/couverture?&appName=NE&idArk=ark:/12148/cb412255468&couverture=1
 * Some infos here too https://github.com/hackathonBnF/hackathon2016/wiki
 */

class AlbumInfosBnfProvider implements AlbumInfosProvider
{
    public $isbn  = '';
    public $recordIdentifier = '';
    public $url = '';
    public $xmlstr = '';
    public $xmlDatas;

    public function __construct($isbn)
    {
        $this->isbn = $isbn;

        $this->url = "https://catalogue.bnf.fr/api/SRU?version=1.2&operation=searchRetrieve&query=bib.isbn%20all%20%22";
        $this->url .= $isbn . "%22";
    }

    public function getDatas(): bool
    {
        $this->getXml();

        $sxe = new \SimpleXMLElement($this->xmlstr);
        $ns = $sxe->getNamespaces(true);
        $child = $sxe->children($ns['srw']);

        if ($child->numberOfRecords == 0) {
            $this->xmlDatas = null;
            return false;
        }

        $this->recordIdentifier = $child->records->record->recordIdentifier;

        $data = $child->records->record->recordData;
        $dataNs = $data->getNamespaces(true);

        // BNF is not exempt of bugs (or is it feature I don't understand ?)
        if (empty($dataNs['mxc'])) {
            $this->xmlDatas = null;
            return false;
        }

        $child = $data->children($dataNs['mxc']);

        $this->xmlDatas = $child;
        return true;
    }

    public function hydrateAlbum(AlbumInfos $album): AlbumInfos
    {
        $refBnf = $this->recordIdentifier;
        $album->urlCover = 'https://catalogue.bnf.fr/couverture?&appName=NE&idArk=' . $refBnf . '&couverture=1';
        foreach ($this->xmlDatas->record->datafield as $field) {
            $tag = $field->attributes()['tag'];
            $ind2 = $field->attributes()['ind2'];
            switch ((string)$tag) {
                case '200':
                    $album->title = (string)$this->getCode($field, 'a');
                    if (!empty($this->getCode($field, 'e'))) {
                        $album->title .= ' / ' . (string)$this->getCode($field, 'e');
                    }
                    $album->authors[] = $this->getCode($field, 'f');
                    $album->authors = array_merge($album->authors, $this->getRepetableCodes($field, 'g'));
                    break;
                case '210':
                    $album->publisher = (string)$this->getCode($field, 'c');
                    break;
                case '214':
                    if ($ind2 == '0') {
                        $album->publisher = (string)$this->getCode($field, 'c');
                    }
                    break;
                case '330':
                    $album->resume = (string)$this->getCode($field, 'a');
                    break;
                case '410': // "collection"
                    $album->serie = (string)$this->getCode($field, 't');
                    $album->serie_issue = (string)$this->getCode($field, 'v');
                    break;
                case '461': // "ensemble"
                    if ($album->serie == '') $album->serie = (string)$this->getCode($field, 't');
                    if ($album->serie_issue == '') $album->serie_issue = (string)$this->getCode($field, 'v');
                    break;
            }
        }

        return $album;
    }

    protected function getXml()
    {
        $this->xmlstr = join('', file($this->url));
    }

    protected function getCode($field, $code)
    {
        foreach ($field->subfield as $subfield) {
            if ($subfield->attributes()['code'] == $code) {
                return $subfield;
            }
        }
        return '';
    }

    protected function getRepetableCodes($field, $code)
    {
        $results = [];
        foreach ($field->subfield as $subfield) {
            if ($subfield->attributes()['code'] == $code) {
                $results[] = $subfield;
            }
        }
        return $results;
    }
}
