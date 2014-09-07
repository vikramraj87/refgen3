<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 09/06/14
 * Time: 1:04 AM
 */

namespace ArticleTest\Entity;

use Article\Entity\Journal;
use PHPUnit_Framework_TestCase;

class JournalTest extends PHPUnit_Framework_TestCase
{
    public function testToArray()
    {
        $expected = array(
            'issn' => '1234-5678',
            'title' => 'Indian Journal of Cytology',
            'abbr' => 'Ind J Cyt'
        );

        $journal = new Journal();
        $journal->setIssn('1234-5678');
        $journal->setTitle('Indian Journal of Cytology');
        $journal->setAbbr('Ind. J. Cyt.');

        $this->assertEquals($expected, $journal->toArray());
    }

    public function testSetAbbr()
    {
        $journal = new Journal();
        $journal->setAbbr('Ind. J. Cyt.');
        $this->assertEquals('Ind J Cyt', $journal->getAbbr());
    }

    public function testCreateFromArray()
    {
        $data = [
            'issn'  => '1234-5678',
            'title' => 'Indian Journal of Cytology',
            'abbr'  => 'Ind. J. Cyt.'
        ];

        $journal = Journal::createFromArray($data);
        $this->assertSame(0, $journal->getId());
        $this->assertEquals('1234-5678', $journal->getIssn());
        $this->assertEquals('Indian Journal of Cytology', $journal->getTitle());
        $this->assertEquals('Ind J Cyt', $journal->getAbbr());

        $data['id'] = 10;

        $journal = Journal::createFromArray($data);
        $this->assertSame(10, $journal->getId());
        $this->assertEquals('1234-5678', $journal->getIssn());
        $this->assertEquals('Indian Journal of Cytology', $journal->getTitle());
        $this->assertEquals('Ind J Cyt', $journal->getAbbr());
    }
} 