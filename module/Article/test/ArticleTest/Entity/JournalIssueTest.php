<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 09/06/14
 * Time: 1:38 AM
 */

namespace ArticleTest\Entity;

use Article\Entity\JournalIssue;
use Article\Entity\PubDate;
use PHPUnit_Framework_TestCase;

class JournalIssueTest extends PHPUnit_Framework_TestCase
{
    public function testSetPages()
    {
        /** @var JournalIssue $journalIssue */
        $journalIssue = new JournalIssue();

        $pages = array(
            '1024-1028' => '1024-8',
            '11-1001'   => '11-1001',
            '1-7'       => '1-7',
            '101-03'    => '101-3',
            '175-9'     => '175-9'
        );

        foreach($pages as $provided => $expected) {
            $journalIssue->setPages($provided);
            $this->assertEquals($expected, $journalIssue->getPages());
        }
    }

    public function testToArray()
    {
        $pubDate = new PubDate();
        $pubDate->setDay('10');
        $pubDate->setMonth('Sep');
        $pubDate->setYear('2014');

        $journalIssue = new JournalIssue();
        $journalIssue->setIssue('7');
        $journalIssue->setVolume('9');
        $journalIssue->setPages('123-127');
        $journalIssue->setPubDate($pubDate);
        $journalIssue->setPubStatus(true);

        $expected = [
            'volume' => '9',
            'issue'  => '7',
            'pages'  => '123-7',
            'pub_status' => 1,
            'day' => '10',
            'month' => 'Sep',
            'year' => '2014'
        ];

        $this->assertEquals($expected, $journalIssue->toArray());

        $expected['pub_status'] = 0;
        $journalIssue->setPubStatus(false);
        $this->assertEquals($expected, $journalIssue->toArray());
    }
} 