<?php
namespace CollectionTest\CollectionTest\Table;

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

        $this->articleTable = new ArticleTable();
        $this->articleTable->setDbAdapter($adapter);

        $this->collectionArticleTable = new CollectionArticleTable();
        $this->collectionArticleTable->setDbAdapter($adapter);

        $this->table = new CollectionTable();
        $this->table->setDbAdapter($adapter);

        parent::setUp();
        $conn->getConnection()->query('set foreign_key_checks=1');
    }

    public function testFetchCollectionById()
    {
        $articles = $this->articlesFromIds(array(
                1, 2, 4, 8, 16, 32
            )
        );

        $expected = new Collection();
        $expected->setId(1);
        $expected->setName('PowerOfTwo');
        $expected->setCreatedOn(new \DateTime('2014-06-20 14:01:03'));
        $expected->setUpdatedOn(null);
        $expected->setArticles($articles);

        $this->assertEquals($expected, $this->table->fetchCollectionById(1));
    }

    public function testFetchCollectionByNonExistingId()
    {
        $this->assertEquals(null, $this->table->fetchCollectionById(131));
    }

    public function testFetchEmptyCollection()
    {
        $expected = new Collection();
        $expected->setId(4);
        $expected->setName('Empty');
        $expected->setCreatedOn(new \DateTime('2014-06-25 15:12:40'));
        $expected->setUpdatedOn(null);

        $this->assertEquals($expected, $this->table->fetchCollectionById(4));
    }


    public function testSaveCollection()
    {
        $ids = array(
            3, 6, 9, 12, 15, 18, 21
        );
        $articles = $this->articlesFromIds($ids);

        $collection = new Collection();
        $collection->setId(0);
        $collection->setName('MultiplesOf3');
        $collection->setArticles($articles);

        $result = $this->table->saveCollection($collection, 1);
        $date = new \DateTime();
        $expectedDate = $date->format('Y-m-d H');

        $this->assertTrue($result);
        $rowset = $this->table->select(array(
                'id' => $this->table->getLastInsertValue()
            )
        );
        $data = $rowset->current();
        $this->assertEquals(5, $data['id']);
        $this->assertEquals('MultiplesOf3', $data['name']);
        $this->assertEquals(1, $data['user_id']);
        $this->assertEquals(substr($data['created_on'], 0, 13), $expectedDate);
        $this->assertNull($data['updated_on']);

        $rowset = $this->collectionArticleTable->select(array(
                'collection_id' => 5,
            )
        );
        $pos = 0;
        $this->assertEquals(7, count($rowset));
        foreach($rowset as $row) {
            $this->assertEquals($pos+1, $row['position']);
            $this->assertEquals($ids[$pos], $row['article_id']);
            $pos++;
        }
    }

    public function testSaveEmptyCollection()
    {
        $collection = new Collection();
        $collection->setId(0);
        $collection->setName('MultiplesOf3');
        $collection->setArticles(array());

        $result = $this->table->saveCollection($collection, 1);
        $this->assertTrue($result);

        $rowset = $this->table->select(array(
                'id' => $this->table->getLastInsertValue()
            )
        );
        $data = $rowset->current();
        $this->assertEquals(5, $data['id']);
        $this->assertEquals('MultiplesOf3', $data['name']);
        $this->assertEquals(1, $data['user_id']);
        $this->assertEquals(
            (new \DateTime())->format('Y-m-d H'),
            substr($data['created_on'], 0, 13)
        );
        $this->assertNull($data['updated_on']);

        $rowset = $this->collectionArticleTable->select(array(
                'collection_id' => 5,
            )
        );
        $this->assertEquals(0, count($rowset));
    }

    public function testUpdatingCollection()
    {
        $ids = array(
            3, 6, 9, 12, 15, 18, 21
        );
        $articles = $this->articlesFromIds($ids);

        $collection = new Collection();
        $collection->setId(3);
        $collection->setName('MultiplesOf3');
        $collection->setArticles($articles);

        $result = $this->table->saveCollection($collection, 1);
        $this->assertTrue($result);
        $rowset = $this->table->select(array(
                'id' => 3
            )
        );
        $data = $rowset->current();
        $this->assertEquals(3, $data['id']);
        $this->assertEquals('MultiplesOf3', $data['name']);
        $this->assertEquals(2, $data['user_id']);
        $this->assertEquals('2014-06-21 15:12:40', $data['created_on']);
        $this->assertEquals(
            (new \DateTime())->format('Y-m-d H'),
            substr($data['updated_on'], 0, 13)
        );

        $rowset = $this->collectionArticleTable->select(array(
                'collection_id' => 3,
            )
        );
        $pos = 0;
        $this->assertEquals(7, count($rowset));
        foreach($rowset as $row) {
            $this->assertEquals($pos+1, $row['position']);
            $this->assertEquals($ids[$pos], $row['article_id']);
            $pos++;
        }
    }

    public function testUpdatingEmptyCollection()
    {
        $collection = new Collection();
        $collection->setId(3);
        $collection->setName('MultiplesOf3');

        $result = $this->table->saveCollection($collection);

        $this->assertTrue($result);
        $rowset = $this->table->select(array(
                'id' => 3
            )
        );
        $data = $rowset->current();
        $this->assertEquals(3, $data['id']);
        $this->assertEquals('MultiplesOf3', $data['name']);
        $this->assertEquals(2, $data['user_id']);
        $this->assertEquals('2014-06-21 15:12:40', $data['created_on']);
        $this->assertEquals(
            (new \DateTime())->format('Y-m-d H'),
            substr($data['updated_on'], 0, 13)
        );

        $rowset = $this->collectionArticleTable->select(array(
                'collection_id' => 3,
            )
        );
        $this->assertEquals(0, count($rowset));
    }

    public function testCount()
    {
        $count = $this->table->getTotalCount();
        var_dump($count);
    }

    private function articlesFromIds($ids = array())
    {
        $articles = array();
        foreach($ids as $id) {
            /** @var Article $article */
            $article = $this->articleTable->fetchArticleById($id);
            $articles[] = $article;
        }
        return $articles;
    }
}