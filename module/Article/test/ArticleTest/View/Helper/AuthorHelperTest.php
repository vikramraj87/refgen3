<?php
namespace ArticleTest\View\Helper;

use Article\View\Helper\AuthorHelper;
use Article\Entity\Author;
class AuthorHelperTest extends \PHPUnit_Framework_TestCase
{
    /** @var AuthorHelper */
    private $helper;

    public function testHelperWithArray()
    {
        $author1 = new Author();
        $author1->setLastName('Gopinathan');
        $author1->setForeName('Vikram Raj');
        $author1->setInitials('VR');

        $author2 = new Author();
        $author2->setLastName('Vikram');
        $author2->setForeName('Kirthika');
        $author2->setInitials('K');

        $author3 = new Author();
        $author3->setLastName('Gopinathan');
        $author3->setForeName('Nirmal Raj');
        $author3->setInitials('NR');

        $authors = array($author1, $author2, $author3);
        $helper = $this->getHelper();
        $this->assertEquals('Gopinathan VR, Vikram K, Gopinathan NR', $helper($authors)->render());
    }

    public function testHelperWithSingleAuthor()
    {
        $author = new Author();
        $author->setLastName('Gopinathan');
        $author->setForeName('Vikram Raj');
        $author->setInitials('VR');

        $authors = array($author);
        $helper = $this->getHelper();
        $this->assertEquals('Gopinathan VR', $helper($authors)->render());
    }

    public function testHelperWithEmptyArray()
    {
        $authors = array();
        $helper = $this->getHelper();
        $this->assertEquals('[No authors listed]', $helper($authors)->render());
    }

    public function testHelperWithInvalidArgument()
    {
        $authors = array(
            'Gopinathan VR',
            'Gopinathan NR'
        );
        $helper = $this->getHelper();
        try {
            $helper($authors)->render();
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \InvalidArgumentException);
        }
    }

    private function getHelper()
    {
        if(null === $this->helper) {
            $this->helper = new AuthorHelper();
        }
        return $this->helper;
    }
}
 