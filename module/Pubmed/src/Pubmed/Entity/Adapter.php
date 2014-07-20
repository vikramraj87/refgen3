<?php

namespace Pubmed\Entity;

use Article\Entity\AbstractPara,
    Article\Entity\Article,
    Article\Entity\Author,
    Article\Entity\Journal,
    Article\Entity\JournalIssue,
    Article\Entity\PubDate;
use SimpleXMLElement;
use Zend\Http\Client,
    Zend\Http\Client\Exception\ExceptionInterface as HttpClientException,
    Zend\Http\Request,
    Zend\Http\Response,
    Zend\EventManager\EventManager;

class Adapter
{
    const SEARCH_URI = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=pubmed&retmode=xml&retmax=%s&retstart=%s&term=%s';
    const FETCH_URI  = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&retmode=xml&id=%s';

    // Constants to define events associated with the adapter
    const FETCH_IDS_EVENT         = 'adapter_fetch_ids';
    const FAILURE_RESPONSE_EVENT  = 'failure_response';
    const CONNECTION_FAILED_EVENT = 'connection_failed';

    /** @var \Zend\EventManager\EventManager */
    private $events;

    /** @var Client */
    private $client;

    /** @var int */
    private $lastSearchCount = 0;

    /** @var int */
    private $maxResultsPerPage = 10;

    /**
     * @return \Zend\EventManager\EventManager
     */
    public function events()
    {
        if($this->events == null) {
            $this->events = new EventManager(__CLASS__);
        }
        return $this->events;
    }

    /**
     * @param \Zend\Http\Client $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @return int
     */
    public function getLastSearchCount()
    {
        return $this->lastSearchCount;
    }

    /**
     * @param int $maxResultsPerPage
     */
    public function setMaxResultsPerPage($maxResultsPerPage = 10)
    {
        if($maxResultsPerPage > 0) {
            $this->maxResultsPerPage = $maxResultsPerPage;
        }
    }

    /**
     * @return int
     */
    public function getMaxResultsPerPage()
    {
        return $this->maxResultsPerPage;
    }

    /**
     * Searches articles by term and returns the id
     *
     * @param string $term
     * @param int $page
     * @return array|null
     */
    public function search($term = '', $page = 1)
    {
        $term = (string) $term;
        $term = urlencode($term);
        $start = 0;
        $page = (int) $page;
        if($page > 0) {
            $start = ($page - 1) * $this->maxResultsPerPage;
        }
        $uri = sprintf(self::SEARCH_URI, $this->maxResultsPerPage, $start, $term);
        $responseBody = $this->doRequest($uri);
        if($responseBody == null) {
            return null;
        }
        $xml = new SimpleXMLElement($responseBody);
        $this->lastSearchCount = (int) $xml->Count;

        $ids = array();
        if(isset($xml->IdList)) {
            $idList = $xml->IdList->children();
            foreach($idList as $id) {
                $ids[] = 'PMID' . (string) $id;
            }
        }
        return $ids;
    }

    /**
     * Fetches articles identified by ids provided
     *
     * @param array $ids
     * @return Article|Article[]|null
     */
    public function fetchByIds(array $ids = array())
    {
        $this->events()->trigger(
            self::FETCH_IDS_EVENT,
            $this,
            array(
                'ids' => $ids
            )
        );
        if(empty($ids)) {
            return null;
        }
        array_walk($ids, function(&$value, $key) {
            $prefix = substr($value, 0, 4);
            if($prefix == 'PMID') {
                $value = substr($value, 4);
            }
        });
        $uri = sprintf(self::FETCH_URI, implode(',', $ids));
        $responseBody = $this->doRequest($uri);
        if($responseBody == null) {
            return null;
        }
        $xml = new SimpleXMLElement($responseBody);
        $articles = $this->parse($xml);
        return $articles;
    }

    /**
     * Helper function for parsing xml to article object(s)
     * Returns
     *  Null if there is some communication with the pubmed server
     *  empty array() if there is no article found in the pubmed
     *  Article if only one article is found
     *  Article[] if many articles are found
     *
     * @param SimpleXMLElement $xml
     * @return Article|\Article\Entity\Article[]|null
     */
    private function parse(SimpleXMLElement $xml)
    {
        if(!isset($xml->PubmedArticle)) {
            return null;
        }
        $numArticles = $xml->count();
        if($numArticles == 1) {
            return $this->parseOneArticle($xml->PubmedArticle);
        }
        /** @var Article[] $articles */
        $articles = array();
        foreach($xml->PubmedArticle as $pubmedArticle) {
            /** @var SimpleXMLElement $pubmedArticle */

            $article = $this->parseOneArticle($pubmedArticle);
            $articles[$article->getIndexerId()] = $article;
        }
        return $articles;
    }

    /**
     * Helper function for parsing one article xml data to Article object
     *
     * @param SimpleXMLElement $pubmedArticle
     * @return Article
     */
    private function parseOneArticle(SimpleXMLElement $pubmedArticle)
    {
        /** @var AbstractPara[] $abstract */
        $abstract = array();
        if(isset($pubmedArticle->MedlineCitation->Article->Abstract)
            && !empty($pubmedArticle->MedlineCitation->Article->Abstract)) {
            $paras = $pubmedArticle->MedlineCitation->Article->Abstract->children();
            foreach($paras as $para) {
                /** @var SimpleXMLElement $para */
                if($para->getName() != 'CopyrightInformation') {
                    $attr = $para->attributes();
                    $heading = (isset($attr['Label'])) ? (string)$attr['Label'] : '';
                    $nlm     = (isset($attr['NlmCategory'])) ?
                        (string) $attr['NlmCategory'] : '';
                    $a = new AbstractPara();
                    $a->setHeading($heading);
                    $a->setNlmCategory($nlm);
                    $a->setPara((string) $para);
                    $abstract[] = $a;
                }
            }
        }

        /** @var Author[] $authors */
        $authors = array();
        if(isset($pubmedArticle->MedlineCitation->Article->AuthorList)) {
            $authorsData = $pubmedArticle->MedlineCitation->Article->AuthorList->children();
            foreach($authorsData as $authorData) {
                /** @var Author $author */
                $author = new Author();
                $author->setLastName((string) $authorData->LastName);
                $author->setForeName((string) $authorData->ForeName);
                $author->setInitials((string) $authorData->Initials);
                $authors[] = $author;
            }
        }

        $journal = new Journal;
        $journalData = $pubmedArticle->MedlineCitation->Article->Journal;
        $journal->setAbbr((string) $journalData->ISOAbbreviation);
        $journal->setIssn((string) $journalData->ISSN);
        $journal->setTitle((string) $journalData->Title);

        $pubDate = new PubDate();
        $journalIssueData = $journalData->JournalIssue;
        $pubDateData = $journalIssueData->PubDate;
        $y = (string) $pubDateData->Year;
        $m = (string) $pubDateData->Month;
        $d = (string) $pubDateData->Day;
        if(empty($y) && isset($pubDateData->MedlineDate)) {
            $mlDate = (string) $pubDateData->MedlineDate;
            $parts  = explode(' ', $mlDate);
            $y   = $parts[0];
            $m  = isset($parts[1]) ? $parts[1] : '';
        }
        $pubDate->setYear($y);
        $pubDate->setMonth($m);
        $pubDate->setDay($d);

        $journalIssue = new JournalIssue();
        $pubStatus = (string) $pubmedArticle->PubmedData->PublicationStatus;
        $pages = (string) $pubmedArticle->MedlineCitation->Article->Pagination->MedlinePgn;
        $journalIssue->setVolume((string) $journalIssueData->Volume);
        $journalIssue->setIssue((string) $journalIssueData->Issue);
        $journalIssue->setPubStatus($pubStatus != 'aheadofprint');
        $journalIssue->setPubDate($pubDate);
        $journalIssue->setPages($pages);

        $article = new Article();
        $indexerId = 'PMID' . $pubmedArticle->MedlineCitation->PMID;
        $title     = (string) $pubmedArticle->MedlineCitation->Article->ArticleTitle;
        $article->setIndexerId($indexerId);
        $article->setTitle($title);
        $article->setAbstract($abstract);
        $article->setAuthors($authors);
        $article->setJournal($journal);
        $article->setJournalIssue($journalIssue);
        return $article;
    }


    /**
     * @return \Zend\Http\Client
     */
    private function getClient()
    {
        if($this->client == null) {
            $this->client = new Client();
            $this->client->setMethod(Request::METHOD_GET);
        }
        return $this->client;
    }

    /**
     * Returns the body of the the request or null
     *
     * @param string $uri
     * @return null|string
     */
    private function doRequest($uri = '')
    {
        $client = $this->getClient();
        $client->setUri($uri);

        try {
            /** @var Response $response */
            $response = $client->send();
        } catch (HttpClientException $e) {
            $this->events()->trigger(
                self::CONNECTION_FAILED_EVENT,
                $this,
                array(
                    'exception' => $e
                )
            );
            return null;
        }

        if(!$response->isSuccess()) {
            $this->events()->trigger(
                self::FAILURE_RESPONSE_EVENT,
                $this,
                array(
                    'uri' => $uri
                )
            );
            return null;
        }
        return $response->getBody();
    }
} 