<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 09/06/14
 * Time: 12:50 AM
 */

namespace ArticleTest\Entity;

use Article\Entity\Author;
use PHPUnit_Framework_TestCase;

class AuthorTest extends PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $author = new Author();
        $author->setForeName('Vikram Raj');
        $author->setLastName('Gopinathan');
        $author->setInitials('VR');

        $this->assertEquals('Gopinathan VR', $author->getName());
    }
} 