<?php
namespace CollectionTest\CollectionTest\Table;

use CollectionTest\DbTestCase;

use Collection\Table\CollectionArticleTable;
use Article\Entity\Article;

class CollectionArticleTableTest extends DbTestCase
{
    /** @var CollectionArticleTable */
    protected $table;

    protected function setUp()
    {
        $conn = $this->getConnection();
        $conn->getConnection()->query('set foreign_key_checks=0');
        $adapter = $this->getAdapter();
        $articleTable = $this->getMock('\Article\Table\ArticleTable', array('fetchArticlesByIds'));
        $articleTable->expects($this->any())
                     ->method('fetchArticlesByIds')
                     ->will($this->returnCallback(array($this, 'fetchArticles')));
        $this->table = new CollectionArticleTable();
        $this->table->setDbAdapter($adapter);
        $this->table->setArticleTable($articleTable);
        parent::setUp();
        $conn->getConnection()->query('set foreign_key_checks=1');
    }

    public function testFetchArticlesByCollectionId()
    {
        $articles = $this->table->fetchArticlesByCollectionId(26);
        $expected = [243,246,248];
        foreach($articles as $id => $article)
        {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $article->getId());
            next($expected);
        }
    }

    public function testSaveArticles()
    {
        $articles = $this->fetchArticles([
            306,307,308,309,300
        ]);
        $this->table->saveArticles($articles, 50);
        $pdo = $this->getConnection()->getConnection();
        $st = $pdo->prepare('SELECT * FROM collection_articles WHERE `collection_id` = :id');
        $st->execute(array('id' => 50));
        $rows = $st->fetchAll(\PDO::FETCH_ASSOC);
        $expected = [
            [
                'collection_id' => '50',
                'article_id'    => '306',
                'position'      => '1'
            ],
            [
                'collection_id' => '50',
                'article_id'    => '307',
                'position'      => '2'
            ],
            [
                'collection_id' => '50',
                'article_id'    => '308',
                'position'      => '3'
            ],
            [
                'collection_id' => '50',
                'article_id'    => '309',
                'position'      => '4'
            ],
            [
                'collection_id' => '50',
                'article_id'    => '300',
                'position'      => '5'
            ],
        ];
        $this->assertSame($expected, $rows);
    }

    public function testSaveZeroArticles()
    {
        $this->table->saveArticles([], 50);
        $pdo = $this->getConnection()->getConnection();
        $st = $pdo->prepare('SELECT * FROM collection_articles WHERE `collection_id` = :id');
        $st->execute(array('id' => 50));
        $rows = $st->fetchAll(\PDO::FETCH_ASSOC);
        $expected = [];
        $this->assertSame($expected, $rows);
    }

    public function testUpdatingArticles()
    {
        $pdo = $this->getConnection()->getConnection();
        $st = $pdo->prepare('SELECT * FROM collection_articles WHERE `collection_id` = :id');
        $st->execute(array('id' => 28));
        $rows = $st->fetchAll(\PDO::FETCH_ASSOC);
        $expected = [
            [
                'collection_id' => '28',
                'article_id'    => '306',
                'position'      => '1'
            ],
            [
                'collection_id' => '28',
                'article_id'    => '307',
                'position'      => '2'
            ],
            [
                'collection_id' => '28',
                'article_id'    => '308',
                'position'      => '3'
            ],
            [
                'collection_id' => '28',
                'article_id'    => '309',
                'position'      => '4'
            ],
        ];
        $this->assertSame($expected, $rows);

        $articles = $this->fetchArticles([
            306,307,308,309,300
        ]);
        $this->table->saveArticles($articles, 28);
        $expected = [
            [
                'collection_id' => '28',
                'article_id'    => '306',
                'position'      => '1'
            ],
            [
                'collection_id' => '28',
                'article_id'    => '307',
                'position'      => '2'
            ],
            [
                'collection_id' => '28',
                'article_id'    => '308',
                'position'      => '3'
            ],
            [
                'collection_id' => '28',
                'article_id'    => '309',
                'position'      => '4'
            ],
            [
                'collection_id' => '28',
                'article_id'    => '300',
                'position'      => '5'
            ],
        ];
        $st->execute(array('id' => 28));
        $rows = $st->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertSame($expected, $rows);
    }

    public function testUpdatingZeroArticles()
    {
        $pdo = $this->getConnection()->getConnection();
        $st = $pdo->prepare('SELECT * FROM collection_articles WHERE `collection_id` = :id');
        $st->execute(array('id' => 28));
        $rows = $st->fetchAll(\PDO::FETCH_ASSOC);
        $expected = [
            [
                'collection_id' => '28',
                'article_id'    => '306',
                'position'      => '1'
            ],
            [
                'collection_id' => '28',
                'article_id'    => '307',
                'position'      => '2'
            ],
            [
                'collection_id' => '28',
                'article_id'    => '308',
                'position'      => '3'
            ],
            [
                'collection_id' => '28',
                'article_id'    => '309',
                'position'      => '4'
            ],
        ];
        $this->assertSame($expected, $rows);

        $this->table->saveArticles([], 28);
        $expected = [];
        $st->execute(array('id' => 28));
        $rows = $st->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertSame($expected, $rows);
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