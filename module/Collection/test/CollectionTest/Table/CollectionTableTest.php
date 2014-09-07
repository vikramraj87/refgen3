<?php
namespace CollectionTest\CollectionTest\Table;

use DateTime;
use CollectionTest\DbTestCase;
use Collection\Table\CollectionTable,
    Collection\Table\CollectionArticleTable;
use Collection\Entity\Collection;
use Article\Table\ArticleTable,
    Article\Entity\Article;
class CollectionTableTest extends DbTestCase
{
    /** @var CollectionTable */
    protected $table;

    /** @var ArticleTable */
    protected $articleTable;

    /** @var CollectionArticleTable */
    protected $collectionArticleTable;

    protected function setUp()
    {
        $conn = $this->getConnection();
        $conn->getConnection()->query('set foreign_key_checks=0');
        $adapter = $this->getAdapter();

        $collectionArticleTable = $this->getMock('\Collection\Table\CollectionArticleTable', array('fetchArticlesByCollectionId'));
        $collectionArticleTable->expects($this->any())
                               ->method('fetchArticlesByCollectionId')
                               ->will($this->returnCallback(array($this, 'fetchArticlesForCollection')));

        $this->table = new CollectionTable();
        $this->table->setDbAdapter($adapter);
        $this->table->setCollectionArticleTable($collectionArticleTable);

        parent::setUp();
        $conn->getConnection()->query('set foreign_key_checks=1');
    }

    public function testFetchCollectionById()
    {
        $collection = $this->table->fetchCollectionByIdAndUserId(26,1);
        $this->assertSame(26, $collection->getId());
        $this->assertSame('Pancytopenia in septicemia', $collection->getName());
        $this->assertEquals(new DateTime('2014-07-24 01:00:37'), $collection->getCreatedOn());
        $this->assertEquals(new DateTime('2014-07-24 01:07:57'), $collection->getUpdatedOn());
        $this->assertEquals(false, $collection->isEdited());
        $this->assertEquals(1, $collection->getUserId());
        $expectedIds = [243,246,248];
        foreach($collection->getItems() as $item) {
            $id = current($expectedIds);
            $this->assertSame($id, $item->getId());
            next($expectedIds);
        }
    }

    public function testFetchCollectionByNonExistingId()
    {
        // Non existing collection
        $collection = $this->table->fetchCollectionByIdAndUserId(100,1);
        $this->assertNull($collection);

        // Invalid user id provided
        $collection = $this->table->fetchCollectionByIdAndUserId(26,2);
        $this->assertNull($collection);
    }

    public function testFetchRecentByUserId()
    {
        $expected = [
            [
                'id' => 26,
                'name' => 'Pancytopenia in septicemia'
            ],
            [
                'id' => 50,
                'name' => 'New Collection'
            ],
            [
                'id' => 53,
                'name' => 'New Collection 2'
            ],
            [
                'id' => 153,
                'name' => 'New Collection 3'
            ],
            [
                'id' => 155,
                'name' => 'New Collection 4'
            ],
        ];
        $collections = $this->table->fetchRecentByUserId(1);
        $this->assertSame($expected, $collections);
    }

    public function testFetchRecentByUserIdWithCurrent()
    {
        $expected = [
            [
                'id' => 26,
                'name' => 'Pancytopenia in septicemia'
            ],
            [
                'id' => 50,
                'name' => 'New Collection'
            ],
            [
                'id' => 53,
                'name' => 'New Collection 2'
            ],
            [
                'id' => 155,
                'name' => 'New Collection 4'
            ],
        ];
        $collections = $this->table->fetchRecentByUserId(1,153);
        $this->assertSame($expected, $collections);
    }

    public function testFetchRecentByNonExistingUserId()
    {
        $this->assertSame([], $this->table->fetchRecentByUserId(100));
    }

    public function testFetchEntireByUserId()
    {
        $expected = [
            [
                'id' => 26,
                'name' => 'Pancytopenia in septicemia'
            ],
            [
                'id' => 50,
                'name' => 'New Collection'
            ],
            [
                'id' => 53,
                'name' => 'New Collection 2'
            ],
            [
                'id' => 153,
                'name' => 'New Collection 3'
            ],
            [
                'id' => 155,
                'name' => 'New Collection 4'
            ],
        ];
        $collections = $this->table->fetchEntireByUserId(1);
        $this->assertSame($expected, $collections);
    }

    public function testFetchEntireByNonExistingUserId()
    {
        $this->assertSame([], $this->table->fetchRecentByUserId(100));
    }

    public function fetchArticlesForCollection($collectionId = 0)
    {
        $data = [
            16 => [1],
            26 => [243,246,248],
            28 => [306,307,308,309]
        ];
        $articleIds = array_key_exists($collectionId, $data) ?
                        $data[$collectionId] : [];
        $articles = [];
        foreach($articleIds as $articleId) {
            $article = new Article();
            $article->setId($articleId);
            $articles[] = $article;
        }
        return $articles;
    }

    public function testSaveCollectionWithNewCollection()
    {
        $items = [];
        $ids = [3,6,9,12,15,18,21,24];

        foreach($ids as $id) {
            $article = new Article();
            $article->setId($id);
            $items[$id] = $article;
        }

        $collection = new Collection(
            $items, 0, 'Multiples of 3', null, null, true, 1
        );

        $collectionArticleTable = $this->getMock('\Collection\Table\CollectionArticleTable', array('saveArticles'));
        $collectionArticleTable->expects($this->once())
                               ->method('saveArticles')
                               ->with($this->equalTo($items), $this->equalTo(156))
                               ->will($this->returnValue(true));
        $this->table->setCollectionArticleTable($collectionArticleTable);
        $result = $this->table->saveCollection($collection);

        $this->assertTrue($result);
        $this->assertEquals(156, $collection->getId());
        $this->assertRegExp('/^' . date('Y-m-d H') . ':\d{2}:\d{2}$/', $collection->getCreatedOn()->format('Y-m-d H:i:s'));
        $this->assertFalse($collection->isEdited());

        $pdo = $this->getConnection()->getConnection();
        $st = $pdo->prepare('SELECT * FROM collections WHERE id = :id');
        $st->execute(array('id' => 156));
        $row = $st->fetchAll(\PDO::FETCH_ASSOC)[0];

        $this->assertEquals('156', $row['id']);
        $this->assertEquals('1', $row['user_id']);
        $this->assertEquals('Multiples of 3', $row['name']);
        $this->assertRegExp('/^' . date('Y-m-d H') . ':\d{2}:\d{2}$/', $row['created_on']);
        $this->assertNull($row['updated_on']);
    }

    public function testSaveCollectionWithNewEmptyCollection()
    {
        $collection = new Collection(
            [], 0, 'Empty Collection', null, null, true, 1
        );

        $collectionArticleTable = $this->getMock('\Collection\Table\CollectionArticleTable', array('saveArticles'));
        $collectionArticleTable->expects($this->once())
            ->method('saveArticles')
            ->with($this->equalTo([]), $this->equalTo(156))
            ->will($this->returnValue(true));
        $this->table->setCollectionArticleTable($collectionArticleTable);

        $result = $this->table->saveCollection($collection, 1);

        $this->assertTrue($result);
        $this->assertEquals(156, $collection->getId());
        $this->assertRegExp('/^' . date('Y-m-d H') . ':\d{2}:\d{2}$/', $collection->getCreatedOn()->format('Y-m-d H:i:s'));
        $this->assertFalse($collection->isEdited());

        $pdo = $this->getConnection()->getConnection();
        $st = $pdo->prepare('SELECT * FROM collections WHERE id = :id');
        $st->execute(array('id' => 156));
        $row = $st->fetchAll(\PDO::FETCH_ASSOC)[0];
        $this->assertEquals('156', $row['id']);
        $this->assertEquals('1', $row['user_id']);
        $this->assertEquals('Empty Collection', $row['name']);
        $this->assertRegExp('/^' . date('Y-m-d H') . ':\d{2}:\d{2}$/', $row['created_on']);
        $this->assertNull($row['updated_on']);
    }

    public function testSaveCollectionWithExistingCollection()
    {
        $collection = $this->table->fetchCollectionByIdAndUserId(26,1);

        $ids = [1,300,306];
        $items = [];
        foreach($ids as $id) {
            $article = new Article();
            $article->setId($id);
            $items[$id] = $article;
        }
        $oldCreatedOn = $collection->getCreatedOn();
        $collection = new Collection(
            $items,
            $collection->getId(),
            'Uterus leiomyoma',
            $collection->getCreatedOn(),
            $collection->getUpdatedOn(),
            true,
            $collection->getUserId()
        );

        $collectionArticleTable = $this->getMock('\Collection\Table\CollectionArticleTable', array('saveArticles'));
        $collectionArticleTable->expects($this->once())
            ->method('saveArticles')
            ->with($this->equalTo($items), $this->equalTo(26))
            ->will($this->returnValue(true));
        $this->table->setCollectionArticleTable($collectionArticleTable);
        $this->table->saveCollection($collection);

        $this->assertEquals(26, $collection->getId());
        $this->assertEquals('Uterus leiomyoma', $collection->getName());
        $this->assertEquals($oldCreatedOn, $collection->getCreatedOn());
        $this->assertRegExp('/^' . date('Y-m-d H') . ':\d{2}:\d{2}$/', $collection->getUpdatedOn()->format('Y-m-d H:i:s'));
        $this->assertFalse($collection->isEdited());

        $pdo = $this->getConnection()->getConnection();
        $st = $pdo->prepare('SELECT * FROM collections WHERE id = :id');
        $st->execute(array('id' => 26));
        $row = $st->fetchAll(\PDO::FETCH_ASSOC)[0];
        $this->assertEquals('26', $row['id']);
        $this->assertEquals('1', $row['user_id']);
        $this->assertEquals('Uterus leiomyoma', $row['name']);
        $this->assertRegExp('/^' . date('Y-m-d H') . ':\d{2}:\d{2}$/', $row['updated_on']);
        $this->assertEquals('2014-07-24 01:00:37',$row['created_on']);
    }


//    public function testSaveCollection()
//    {
//        $ids = array(
//            3, 6, 9, 12, 15, 18, 21
//        );
//        $articles = $this->articlesFromIds($ids);
//
//        $collection = new Collection();
//        $collection->setId(0);
//        $collection->setName('MultiplesOf3');
//        $collection->setArticles($articles);
//
//        $result = $this->table->saveCollection($collection, 1);
//        $date = new \DateTime();
//        $expectedDate = $date->format('Y-m-d H');
//
//        $this->assertTrue($result);
//        $rowset = $this->table->select(array(
//                'id' => $this->table->getLastInsertValue()
//            )
//        );
//        $data = $rowset->current();
//        $this->assertEquals(5, $data['id']);
//        $this->assertEquals('MultiplesOf3', $data['name']);
//        $this->assertEquals(1, $data['user_id']);
//        $this->assertEquals(substr($data['created_on'], 0, 13), $expectedDate);
//        $this->assertNull($data['updated_on']);
//
//        $rowset = $this->collectionArticleTable->select(array(
//                'collection_id' => 5,
//            )
//        );
//        $pos = 0;
//        $this->assertEquals(7, count($rowset));
//        foreach($rowset as $row) {
//            $this->assertEquals($pos+1, $row['position']);
//            $this->assertEquals($ids[$pos], $row['article_id']);
//            $pos++;
//        }
//    }
//
//    public function testSaveEmptyCollection()
//    {
//        $collection = new Collection();
//        $collection->setId(0);
//        $collection->setName('MultiplesOf3');
//        $collection->setArticles(array());
//
//        $result = $this->table->saveCollection($collection, 1);
//        $this->assertTrue($result);
//
//        $rowset = $this->table->select(array(
//                'id' => $this->table->getLastInsertValue()
//            )
//        );
//        $data = $rowset->current();
//        $this->assertEquals(5, $data['id']);
//        $this->assertEquals('MultiplesOf3', $data['name']);
//        $this->assertEquals(1, $data['user_id']);
//        $this->assertEquals(
//            (new \DateTime())->format('Y-m-d H'),
//            substr($data['created_on'], 0, 13)
//        );
//        $this->assertNull($data['updated_on']);
//
//        $rowset = $this->collectionArticleTable->select(array(
//                'collection_id' => 5,
//            )
//        );
//        $this->assertEquals(0, count($rowset));
//    }
//
//    public function testUpdatingCollection()
//    {
//        $ids = array(
//            3, 6, 9, 12, 15, 18, 21
//        );
//        $articles = $this->articlesFromIds($ids);
//
//        $collection = new Collection();
//        $collection->setId(3);
//        $collection->setName('MultiplesOf3');
//        $collection->setArticles($articles);
//
//        $result = $this->table->saveCollection($collection, 1);
//        $this->assertTrue($result);
//        $rowset = $this->table->select(array(
//                'id' => 3
//            )
//        );
//        $data = $rowset->current();
//        $this->assertEquals(3, $data['id']);
//        $this->assertEquals('MultiplesOf3', $data['name']);
//        $this->assertEquals(2, $data['user_id']);
//        $this->assertEquals('2014-06-21 15:12:40', $data['created_on']);
//        $this->assertEquals(
//            (new \DateTime())->format('Y-m-d H'),
//            substr($data['updated_on'], 0, 13)
//        );
//
//        $rowset = $this->collectionArticleTable->select(array(
//                'collection_id' => 3,
//            )
//        );
//        $pos = 0;
//        $this->assertEquals(7, count($rowset));
//        foreach($rowset as $row) {
//            $this->assertEquals($pos+1, $row['position']);
//            $this->assertEquals($ids[$pos], $row['article_id']);
//            $pos++;
//        }
//    }
//
//    public function testUpdatingEmptyCollection()
//    {
//        $collection = new Collection();
//        $collection->setId(3);
//        $collection->setName('MultiplesOf3');
//
//        $result = $this->table->saveCollection($collection);
//
//        $this->assertTrue($result);
//        $rowset = $this->table->select(array(
//                'id' => 3
//            )
//        );
//        $data = $rowset->current();
//        $this->assertEquals(3, $data['id']);
//        $this->assertEquals('MultiplesOf3', $data['name']);
//        $this->assertEquals(2, $data['user_id']);
//        $this->assertEquals('2014-06-21 15:12:40', $data['created_on']);
//        $this->assertEquals(
//            (new \DateTime())->format('Y-m-d H'),
//            substr($data['updated_on'], 0, 13)
//        );
//
//        $rowset = $this->collectionArticleTable->select(array(
//                'collection_id' => 3,
//            )
//        );
//        $this->assertEquals(0, count($rowset));
//    }
//
//    public function testCount()
//    {
//        $count = $this->table->getTotalCount();
//        var_dump($count);
//    }
//
//    private function articlesFromIds($ids = array())
//    {
//        $articles = array();
//        foreach($ids as $id) {
//            /** @var Article $article */
//            $article = $this->articleTable->fetchArticleById($id);
//            $articles[] = $article;
//        }
//        return $articles;
//    }
}