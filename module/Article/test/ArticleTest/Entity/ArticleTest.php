<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 09/06/14
 * Time: 12:24 AM
 */

namespace ArticleTest\Entity;

use Article\Entity\AbstractText,
    Article\Entity\Article,
    Article\Entity\Author,
    Article\Entity\Journal,
    Article\Entity\JournalIssue,
    Article\Entity\PubDate,
    Article\Entity\Keyword;

use PHPUnit_Framework_TestCase;

class ArticleTest extends PHPUnit_Framework_TestCase
{
    public function testCreateFromArray()
    {
        $data = [
            'indexer_id' => 'PMID12345678',
            'volume'     => '9',
            'issue'      => '7',
            'pages'      => '237-241',
            'year'       => '2013',
            'month'      => 'Sep',
            'day'        => '10',
            'title'      => 'How to test the article object?',
            'pub_status' => 1
        ];

        $article = Article::createFromArray($data);

        $this->assertEquals(0, $article->getId());
        $this->assertEquals('PMID12345678', $article->getIndexerId());
        $this->assertEquals('9', $article->getJournalIssue()->getVolume());
        $this->assertEquals('7', $article->getJournalIssue()->getIssue());
        $this->assertEquals('237-41', $article->getJournalIssue()->getPages());
        $this->assertEquals(true, $article->getJournalIssue()->getPubStatus());
        $this->assertEquals('2013', $article->getJournalIssue()->getPubDate()->getYear());
        $this->assertEquals('Sep', $article->getJournalIssue()->getPubDate()->getMonth());
        $this->assertEquals('10', $article->getJournalIssue()->getPubDate()->getDay());
        $this->assertEquals('How to test the article object?', $article->getTitle());

        $this->assertEquals([], $article->getAuthors());
        $this->assertEquals([], $article->getAbstract());
        $this->assertEquals(null, $article->getJournal());

        $data = [
            'id'         => 13,
            'indexer_id' => 'PMID12345678',
            'volume'     => '',
            'issue'      => '',
            'pages'      => '',
            'year'       => '2013',
            'month'      => 'Sep',
            'day'        => '10',
            'title'      => 'How to test the article object?',
            'pub_status' => 0
        ];

        $article = Article::createFromArray($data);

        $this->assertEquals(13, $article->getId());
        $this->assertEquals('PMID12345678', $article->getIndexerId());
        $this->assertEquals('', $article->getJournalIssue()->getVolume());
        $this->assertEquals('', $article->getJournalIssue()->getIssue());
        $this->assertEquals('', $article->getJournalIssue()->getPages());
        $this->assertEquals(false, $article->getJournalIssue()->getPubStatus());
        $this->assertEquals('2013', $article->getJournalIssue()->getPubDate()->getYear());
        $this->assertEquals('Sep', $article->getJournalIssue()->getPubDate()->getMonth());
        $this->assertEquals('10', $article->getJournalIssue()->getPubDate()->getDay());
        $this->assertEquals('How to test the article object?', $article->getTitle());

        $this->assertEquals([], $article->getAuthors());
        $this->assertEquals([], $article->getAbstract());
        $this->assertEquals(null, $article->getJournal());

    }

    public function testToArray()
    {

    }
}