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
        $journal->setIssn($expected['issn']);
        $journal->setTitle($expected['title']);
        $journal->setAbbr($expected['abbr']);

        $this->assertEquals($expected, $journal->toArray());
    }

    public function testSetAbbr()
    {
        $journal = new Journal();
        $journal->setAbbr('Ind. J. Cyt.');
        $this->assertEquals('Ind J Cyt', $journal->getAbbr());
    }
} 