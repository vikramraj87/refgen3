<?php
namespace ArticleTest\Entity;

use Article\Entity\PubDate;
use PHPUnit_Framework_TestCase;

class PubDateTest extends PHPUnit_Framework_TestCase
{
    public function testToArray()
    {
        $expected = array(
            'year'  => '2014',
            'month' => 'Apr',
            'day'   => '27'
        );
        $pubdate = new PubDate();
        $pubdate->setDay('27');
        $pubdate->setMonth('Apr');
        $pubdate->setYear('2014');

        $this->assertSame($expected, $pubdate->toArray());
    }
} 