<?php
namespace CollectionTest\CollectionTest\Service;

use Zend\Session\Container;
use DateTime;
use Collection\Service\CollectionService,
    Collection\Entity\Collection;
use Article\Entity\Article,
    Article\Table\ArticleTable;

class CollectionServiceTest extends \PHPUnit_Framework_TestCase // extends DbTestCase
{
    /** @var CollectionService */
    private $service = null;

    /** @var ArticleTable */
    private $articleTable = null;

    /** @var Container */
    private $container = null;

    protected function setUp()
    {
        /** @var Container $container */
        $container = $this->getMock('\Zend\Session\Container', array('offsetExists', 'offsetGet'));
        $container->expects($this->once())
            ->method('offsetExists')
            ->with($this->equalTo('data'))
            ->will($this->returnValue(true));
        $data = [
            'id'        => 13,
            'name'      => 'Pancytopenia in HIV',
            'createdOn' => new DateTime('2014-09-01 10:00:00'),
            'updatedOn' => null,
            'itemIds'   => '3,6,9,12,24,36',
            'edited'    => true
        ];
        $container->expects($this->once())
            ->method('offsetGet')
            ->with($this->equalTo('data'))
            ->will($this->returnValue($data));
        /** $var ArticleTable $articleTable */
        $articleTable = $this->getMock('\Article\Table\ArticleTable', array('fetchArticlesByIds'));
        $articleTable->expects($this->once())
            ->method('fetchArticlesByIds')
            ->with($this->equalTo([3,6,9,12,24,36]))
            ->will($this->returnCallback(array($this, 'fetchArticles')));

        $this->service = new CollectionService($articleTable, $container);
        $this->articleTable = $articleTable;
        $this->container = $container;
    }

    public function testDeserialize()
    {
        $service = $this->service;

        $this->assertEquals(13, $service->getId());
        $this->assertEquals('Pancytopenia in HIV', $service->getName());
        $this->assertEquals(new DateTime('2014-09-01 10:00:00'), $service->getCreatedOn());
        $this->assertNull($service->getUpdatedOn());
        $this->assertTrue($service->isEdited());
        $this->assertCount(6, $service);

        $expected = [3,6,9,12,24,36];
        foreach($service->getItems() as $item) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $item->getId());
            next($expected);
        }
    }

    public function testAddItem()
    {
        /** @var Container $container */
        $container = $this->getMock('\Zend\Session\Container', array('offsetExists', 'offsetGet', 'offsetSet'));
        $container->expects($this->once())
            ->method('offsetExists')
            ->with($this->equalTo('data'))
            ->will($this->returnValue(true));
        $data = [
            'id'        => 13,
            'name'      => 'Pancytopenia in HIV',
            'createdOn' => new DateTime('2014-09-01 10:00:00'),
            'updatedOn' => null,
            'itemIds'   => '3,6,9,12,24,36',
            'edited'    => false
        ];
        $container->expects($this->once())
            ->method('offsetGet')
            ->with($this->equalTo('data'))
            ->will($this->returnValue($data));
        /** $var ArticleTable $articleTable */
        $articleTable = $this->getMock('\Article\Table\ArticleTable', array('fetchArticlesByIds', 'fetchArticleById'));
        $articleTable->expects($this->once())
            ->method('fetchArticlesByIds')
            ->with($this->equalTo([3,6,9,12,24,36]))
            ->will($this->returnCallback(array($this, 'fetchArticles')));

        $service = new CollectionService($articleTable, $container);
        $this->assertFalse($service->isEdited());

        $data['itemIds'] = '3,6,9,12,24,36,30';
        $data['edited'] = true;

        $article = new Article();
        $article->setId(30);
        $articleTable->expects($this->once())
                     ->method('fetchArticleById')
                     ->with($this->equalTo(30))
                     ->will($this->returnValue($article));

        $container->expects($this->once())
                  ->method('offsetSet')
                  ->with($this->equalTo('data'), $this->equalTo($data));
        $service->addArticle(30);
    }

    public function testRemoveItem()
    {
        /** @var Container $container */
        $container = $this->getMock('\Zend\Session\Container', array('offsetExists', 'offsetGet', 'offsetSet'));
        $container->expects($this->once())
            ->method('offsetExists')
            ->with($this->equalTo('data'))
            ->will($this->returnValue(true));
        $data = [
            'id'        => 13,
            'name'      => 'Pancytopenia in HIV',
            'createdOn' => new DateTime('2014-09-01 10:00:00'),
            'updatedOn' => null,
            'itemIds'   => '3,6,9,12,24,36',
            'edited'    => false
        ];
        $container->expects($this->once())
            ->method('offsetGet')
            ->with($this->equalTo('data'))
            ->will($this->returnValue($data));
        /** $var ArticleTable $articleTable */
        $articleTable = $this->getMock('\Article\Table\ArticleTable', array('fetchArticlesByIds', 'fetchArticleById'));
        $articleTable->expects($this->once())
            ->method('fetchArticlesByIds')
            ->with($this->equalTo([3,6,9,12,24,36]))
            ->will($this->returnCallback(array($this, 'fetchArticles')));

        $service = new CollectionService($articleTable, $container);
        $this->assertFalse($service->isEdited());

        $data['itemIds'] = '3,9,12,24,36';
        $data['edited'] = true;

        $container->expects($this->once())
            ->method('offsetSet')
            ->with($this->equalTo('data'), $this->equalTo($data));
        $service->removeArticle(6);
    }

    public function testRemoveItems()
    {
        /** @var Container $container */
        $container = $this->getMock('\Zend\Session\Container', array('offsetExists', 'offsetGet', 'offsetSet'));
        $container->expects($this->once())
            ->method('offsetExists')
            ->with($this->equalTo('data'))
            ->will($this->returnValue(true));
        $data = [
            'id'        => 13,
            'name'      => 'Pancytopenia in HIV',
            'createdOn' => new DateTime('2014-09-01 10:00:00'),
            'updatedOn' => null,
            'itemIds'   => '3,6,9,12,24,36',
            'edited'    => false
        ];
        $container->expects($this->once())
            ->method('offsetGet')
            ->with($this->equalTo('data'))
            ->will($this->returnValue($data));
        /** $var ArticleTable $articleTable */
        $articleTable = $this->getMock('\Article\Table\ArticleTable', array('fetchArticlesByIds', 'fetchArticleById'));
        $articleTable->expects($this->once())
            ->method('fetchArticlesByIds')
            ->with($this->equalTo([3,6,9,12,24,36]))
            ->will($this->returnCallback(array($this, 'fetchArticles')));

        $service = new CollectionService($articleTable, $container);
        $this->assertFalse($service->isEdited());

        $data['itemIds'] = '3,24,36';
        $data['edited'] = true;

        $container->expects($this->once())
            ->method('offsetSet')
            ->with($this->equalTo('data'), $this->equalTo($data));
        $service->removeArticles([6,9,12]);
    }

    public function testHasItems()
    {
        $service = $this->service;

        $this->assertTrue($service->hasItem(3));
        $this->assertTrue($service->hasItem(6));
        $this->assertTrue($service->hasItem(36));

        $this->assertFalse($service->hasItem(2));
        $this->assertFalse($service->hasItem(4));
        $this->assertFalse($service->hasItem(8));
    }

    public function testMoveUpItems()
    {
        /** @var Container $container */
        $container = $this->getMock('\Zend\Session\Container', array('offsetExists', 'offsetGet', 'offsetSet'));
        $container->expects($this->once())
            ->method('offsetExists')
            ->with($this->equalTo('data'))
            ->will($this->returnValue(true));
        $data = [
            'id'        => 13,
            'name'      => 'Pancytopenia in HIV',
            'createdOn' => new DateTime('2014-09-01 10:00:00'),
            'updatedOn' => null,
            'itemIds'   => '3,6,9,12,24,36',
            'edited'    => false
        ];
        $container->expects($this->once())
            ->method('offsetGet')
            ->with($this->equalTo('data'))
            ->will($this->returnValue($data));
        /** $var ArticleTable $articleTable */
        $articleTable = $this->getMock('\Article\Table\ArticleTable', array('fetchArticlesByIds', 'fetchArticleById'));
        $articleTable->expects($this->once())
            ->method('fetchArticlesByIds')
            ->with($this->equalTo([3,6,9,12,24,36]))
            ->will($this->returnCallback(array($this, 'fetchArticles')));

        $service = new CollectionService($articleTable, $container);
        $this->assertFalse($service->isEdited());

        $data['itemIds'] = '3,6,9,24,36,12';
        $data['edited'] = true;

        $container->expects($this->once())
            ->method('offsetSet')
            ->with($this->equalTo('data'), $this->equalTo($data));
        $service->moveUpItems([3,6,24,36]);
    }

    public function testMoveDownItems()
    {
        /** @var Container $container */
        $container = $this->getMock('\Zend\Session\Container', array('offsetExists', 'offsetGet', 'offsetSet'));
        $container->expects($this->once())
            ->method('offsetExists')
            ->with($this->equalTo('data'))
            ->will($this->returnValue(true));
        $data = [
            'id'        => 13,
            'name'      => 'Pancytopenia in HIV',
            'createdOn' => new DateTime('2014-09-01 10:00:00'),
            'updatedOn' => null,
            'itemIds'   => '3,6,9,12,24,36',
            'edited'    => false
        ];
        $container->expects($this->once())
            ->method('offsetGet')
            ->with($this->equalTo('data'))
            ->will($this->returnValue($data));
        /** $var ArticleTable $articleTable */
        $articleTable = $this->getMock('\Article\Table\ArticleTable', array('fetchArticlesByIds', 'fetchArticleById'));
        $articleTable->expects($this->once())
            ->method('fetchArticlesByIds')
            ->with($this->equalTo([3,6,9,12,24,36]))
            ->will($this->returnCallback(array($this, 'fetchArticles')));

        $service = new CollectionService($articleTable, $container);
        $this->assertFalse($service->isEdited());

        $data['itemIds'] = '9,3,6,12,24,36';
        $data['edited'] = true;

        $container->expects($this->once())
            ->method('offsetSet')
            ->with($this->equalTo('data'), $this->equalTo($data));
        $service->moveDownItems([3,6,24,36]);
    }

    public function testSortItems()
    {
        /** @var Container $container */
        $container = $this->getMock('\Zend\Session\Container', array('offsetExists', 'offsetGet', 'offsetSet'));
        $container->expects($this->once())
            ->method('offsetExists')
            ->with($this->equalTo('data'))
            ->will($this->returnValue(true));
        $data = [
            'id'        => 13,
            'name'      => 'Pancytopenia in HIV',
            'createdOn' => new DateTime('2014-09-01 10:00:00'),
            'updatedOn' => null,
            'itemIds'   => '3,6,9,12,24,36',
            'edited'    => false
        ];
        $container->expects($this->once())
            ->method('offsetGet')
            ->with($this->equalTo('data'))
            ->will($this->returnValue($data));
        /** $var ArticleTable $articleTable */
        $articleTable = $this->getMock('\Article\Table\ArticleTable', array('fetchArticlesByIds', 'fetchArticleById'));
        $articleTable->expects($this->once())
            ->method('fetchArticlesByIds')
            ->with($this->equalTo([3,6,9,12,24,36]))
            ->will($this->returnCallback(array($this, 'fetchArticles')));

        $service = new CollectionService($articleTable, $container);
        $this->assertFalse($service->isEdited());

        $data['itemIds'] = '36,24,12,9,6,3';
        $data['edited'] = true;

        $container->expects($this->once())
            ->method('offsetSet')
            ->with($this->equalTo('data'), $this->equalTo($data));
        $service->sortItems([36,24,12,9,6,3]);
    }

    public function testSetCollectionWithNull()
    {
        $container = $this->getMock('\Zend\Session\Container', array('offsetExists', 'offsetGet', 'offsetSet'));
        $container->expects($this->once())
            ->method('offsetExists')
            ->with($this->equalTo('data'))
            ->will($this->returnValue(true));
        $data = [
            'id'        => 13,
            'name'      => 'Pancytopenia in HIV',
            'createdOn' => new DateTime('2014-09-01 10:00:00'),
            'updatedOn' => null,
            'itemIds'   => '3,6,9,12,24,36',
            'edited'    => false
        ];
        $container->expects($this->once())
            ->method('offsetGet')
            ->with($this->equalTo('data'))
            ->will($this->returnValue($data));
        /** $var ArticleTable $articleTable */
        $articleTable = $this->getMock('\Article\Table\ArticleTable', array('fetchArticlesByIds', 'fetchArticleById'));
        $articleTable->expects($this->once())
            ->method('fetchArticlesByIds')
            ->with($this->equalTo([3,6,9,12,24,36]))
            ->will($this->returnCallback(array($this, 'fetchArticles')));

        $service = new CollectionService($articleTable, $container);
        $this->assertFalse($service->isEdited());

        $data = [
            'id' => 0,
            'name' => '',
            'createdOn' => null,
            'updatedOn' => null,
            'itemIds' => '',
            'edited' => false
        ];

        $container->expects($this->once())
            ->method('offsetSet')
            ->with($this->equalTo('data'), $this->equalTo($data));

        $service->setCollection(null);
    }

    public function testSetCollection()
    {
        $container = $this->getMock('\Zend\Session\Container', array('offsetExists', 'offsetGet', 'offsetSet'));
        $container->expects($this->once())
            ->method('offsetExists')
            ->with($this->equalTo('data'))
            ->will($this->returnValue(true));
        $data = [
            'id'        => 13,
            'name'      => 'Pancytopenia in HIV',
            'createdOn' => new DateTime('2014-09-01 10:00:00'),
            'updatedOn' => null,
            'itemIds'   => '3,6,9,12,24,36',
            'edited'    => false
        ];
        $container->expects($this->once())
            ->method('offsetGet')
            ->with($this->equalTo('data'))
            ->will($this->returnValue($data));
        /** $var ArticleTable $articleTable */
        $articleTable = $this->getMock('\Article\Table\ArticleTable', array('fetchArticlesByIds'));
        $articleTable->expects($this->once())
            ->method('fetchArticlesByIds')
            ->will($this->returnCallback(array($this, 'fetchArticles')));

        $service = new CollectionService($articleTable, $container);
        $this->assertFalse($service->isEdited());

        $data = [
            'id' => 32,
            'name' => 'Gaucher disease',
            'createdOn' => new DateTime('2014-09-03 10:00:00'),
            'updatedOn' => new DateTime('2014-09-04 12:00:00'),
            'itemIds' => '2,4,6,8,10,12',
            'edited' => false
        ];

        $container->expects($this->once())
            ->method('offsetSet')
            ->with($this->equalTo('data'), $this->equalTo($data));

        $collection = new Collection(
            $this->fetchArticles([2,4,6,8,10,12]),
            32,
            'Gaucher disease',
            $data['createdOn'],
            $data['updatedOn']
        );

        $service->setCollection($collection);
    }

    public function fetchArticles($ids = array())
    {
        $articles = [];
        $ids = array_unique($ids);
        foreach($ids as $id) {
            $article = new Article();
            $article->setId($id);
            $articles[] = $article;
        }
        return $articles;
    }



} 