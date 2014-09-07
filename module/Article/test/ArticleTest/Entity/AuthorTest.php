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

    public function testCreateFromArray()
    {
        $data = array(
            'fore_name' => 'Vikram Raj',
            'last_name' => 'Gopinathan',
            'initials'  => 'VR'
        );

        $author = Author::createFromArray($data);
        $this->assertEquals(0, $author->getId());
        $this->assertEquals('Vikram Raj', $author->getForeName());
        $this->assertEquals('Gopinathan', $author->getLastName());
        $this->assertEquals('VR', $author->getInitials());
        $this->assertEquals('Gopinathan VR', $author->getName());

        $data['id'] = 3;

        $author = Author::createFromArray($data);
        $this->assertEquals(3, $author->getId());
    }

    public function testToArray()
    {
        $expected = array(
            'fore_name' => 'Vikram Raj',
            'last_name' => 'Gopinathan',
            'initials'  => 'VR'
        );

        $author = new Author();
        $author->setId(3);
        $author->setForeName('Vikram Raj');
        $author->setLastName("Gopinathan");
        $author->setInitials('VR');

        $this->assertEquals($expected, $author->toArray());
    }
} 