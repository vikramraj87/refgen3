<?php
namespace CollectionTest\CollectionTest\Service;

use Collection\Service\CollectionService,
    Collection\Entity\Collection,
    Collection\Table\CollectionTable;
use CollectionTest\DbTestCase;
use Article\Entity\Article,
    Article\Table\ArticleTable;

class CollectionServiceTest extends DbTestCase
{
    /** @var \Article\Table\ArticleTable */
    protected $articleTable;

    /** @var CollectionTable */
    protected $table;

    protected function setUp()
    {
        $conn = $this->getConnection();
        $conn->getConnection()->query('set foreign_key_checks=0');
        $adapter = $this->getAdapter();
        $this->articleTable = new ArticleTable();
        $this->articleTable->setDbAdapter($adapter);
        $this->table = new CollectionTable();
        $this->table->setDbAdapter($adapter);
        parent::setUp();
        $conn->getConnection()->query('set foreign_key_checks=1');
    }

    public function testDeserializeFromSession()
    {
        $container = $this->getMock(
            '\Zend\Session\Container',
            array('offsetGet', 'offsetExists')
        );
        $container->expects($this->any())
                  ->method('offsetExists')
                  ->with('data')
                  ->will($this->returnValue(true));
        $data = array(
            'id' => 0,
            'name' => '',
            'edited' => false,
            'articleIds' => array(
                2, 4, 8, 16, 32
            )
        );
        $container->expects($this->any())
                  ->method('offsetGet')
                  ->with('data')
                  ->will($this->returnValue($data));
        $service = new CollectionService();
        $service->setContainer($container);
        $service->setArticleTable($this->articleTable);
        $service->init();
        /** @var \Collection\Entity\Collection $collection */
        $collection = $service->getActiveCollection();

        $articles = array();
        foreach($data['articleIds'] as $articleId) {
            /** @var Article $article */
            $article = $this->articleTable->fetchArticleById($articleId);
            $articles[] = $article;
        }
        $expected = new Collection;
        $expected->setId(0);
        $expected->setName('');
        $expected->setUpdatedOn(null);
        $expected->setCreatedOn(null);
        $expected->setArticles($articles);
        $this->assertEquals($expected, $collection);
        $this->assertFalse($service->isEdited());
    }

    public function testDeserializeFromDb()
    {
        $container = $this->getMock(
            '\Zend\Session\Container',
            array('offsetGet', 'offsetExists')
        );
        $container->expects($this->any())
            ->method('offsetExists')
            ->with('data')
            ->will($this->returnValue(true));
        $data = array(
            'id' => 0,
            'name' => '',
            'edited' => false,
            'articleIds' => array(
                2, 4, 8, 16, 32
            )
        );
        $container->expects($this->any())
            ->method('offsetGet')
            ->with('data')
            ->will($this->returnValue($data));
        $service = new CollectionService();
        $service->setContainer($container);
        $service->setArticleTable($this->articleTable);
        $service->setTable($this->table);
        $service->init();
        $this->assertFalse($service->isEdited());
    }

    public function testAddArticle()
    {
        $container = $this->getMock(
            '\Zend\Session\Container',
            array('offsetGet', 'offsetExists', 'offsetSet')
        );
        $container->expects($this->any())
            ->method('offsetExists')
            ->with('data')
            ->will($this->returnValue(true));
        $data = array(
            'id' => 0,
            'name' => '',
            'edited' => false,
            'articleIds' => array(
                2, 4, 8, 16, 32
            )
        );
        $container->expects($this->any())
            ->method('offsetGet')
            ->with('data')
            ->will($this->returnValue($data));

        $expectedData = array(
            'id' => 0,
            'name' => '',
            'edited' => true,
            'articleIds' => array(
                2, 4, 8, 16, 32, 1
            )
        );
        $container->expects($this->once())
                  ->method('offsetSet')
                  ->with('data', $expectedData);

        $service = new CollectionService();
        $service->setContainer($container);
        $service->setArticleTable($this->articleTable);
        $service->setTable($this->table);
        $service->init();

        $service->addArticle(1);
        $this->assertTrue($service->isEdited());
    }

    public function testAddExistingArticle()
    {
        $container = $this->getMock(
            '\Zend\Session\Container',
            array('offsetGet', 'offsetExists', 'offsetSet')
        );
        $container->expects($this->any())
            ->method('offsetExists')
            ->with('data')
            ->will($this->returnValue(true));
        $data = array(
            'id' => 0,
            'name' => '',
            'edited' => false,
            'articleIds' => array(
                2, 4, 8, 16, 32
            )
        );
        $container->expects($this->any())
            ->method('offsetGet')
            ->with('data')
            ->will($this->returnValue($data));

        $expectedData = array(
            'id' => 0,
            'name' => '',
            'edited' => false,
            'articleIds' => array(
                2, 4, 8, 16, 32
            )
        );
        $container->expects($this->once())
            ->method('offsetSet')
            ->with('data', $expectedData);

        $service = new CollectionService();
        $service->setContainer($container);
        $service->setArticleTable($this->articleTable);
        $service->setTable($this->table);
        $service->init();

        $service->addArticle(4);
        $this->assertFalse($service->isEdited());
    }

    public function testRemoveArticle()
    {
        $container = $this->getMock(
            '\Zend\Session\Container',
            array('offsetGet', 'offsetExists', 'offsetSet')
        );
        $container->expects($this->any())
            ->method('offsetExists')
            ->with('data')
            ->will($this->returnValue(true));
        $data = array(
            'id' => 0,
            'name' => '',
            'edited' => false,
            'articleIds' => array(
                2, 4, 8, 16, 32
            )
        );
        $container->expects($this->any())
            ->method('offsetGet')
            ->with('data')
            ->will($this->returnValue($data));

        $expectedData = array(
            'id' => 0,
            'name' => '',
            'edited' => true,
            'articleIds' => array(
                2, 4, 16, 32
            )
        );
        $container->expects($this->once())
            ->method('offsetSet')
            ->with('data', $expectedData);

        $service = new CollectionService();
        $service->setContainer($container);
        $service->setArticleTable($this->articleTable);
        $service->setTable($this->table);
        $service->init();

        $service->removeArticle(8);
        $this->assertTrue($service->isEdited());
    }

    public function testRemoveNonExistingArticle()
    {
        $container = $this->getMock(
            '\Zend\Session\Container',
            array('offsetGet', 'offsetExists', 'offsetSet')
        );
        $container->expects($this->any())
            ->method('offsetExists')
            ->with('data')
            ->will($this->returnValue(true));
        $data = array(
            'id' => 0,
            'name' => '',
            'edited' => false,
            'articleIds' => array(
                2, 4, 8, 16, 32
            )
        );
        $container->expects($this->any())
            ->method('offsetGet')
            ->with('data')
            ->will($this->returnValue($data));

        $expectedData = array(
            'id' => 0,
            'name' => '',
            'edited' => false,
            'articleIds' => array(
                2, 4, 8, 16, 32
            )
        );
        $container->expects($this->once())
            ->method('offsetSet')
            ->with('data', $expectedData);

        $service = new CollectionService();
        $service->setContainer($container);
        $service->setArticleTable($this->articleTable);
        $service->setTable($this->table);
        $service->init();

        $service->removeArticle(9);
        $this->assertFalse($service->isEdited());
    }

    public function testSave()
    {

    }

    public function testDelete()
    {

    }
} 