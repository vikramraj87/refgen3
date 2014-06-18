<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 09/06/14
 * Time: 1:38 AM
 */

namespace ArticleTest\Entity;

use Article\Entity\JournalIssue;
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
} 