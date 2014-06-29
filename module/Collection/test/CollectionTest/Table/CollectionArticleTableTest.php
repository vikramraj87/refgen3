<?php
namespace CollectionTest\CollectionTest\Table;

use Collection\Table\CollectionTable;
use CollectionTest\DbTestCase;

use Collection\Table\CollectionArticleTable;
use Article\Table\ArticleTable;

class CollectionArticleTableTest extends DbTestCase
{
    /** @var CollectionArticleTable */
    protected $table;

    /** @var ArticleTable */
    protected $articleTable;

    protected function setUp()
    {
        $conn = $this->getConnection();
        $conn->getConnection()->query('set foreign_key_checks=0');
        $adapter = $this->getAdapter();

        $this->articleTable = new ArticleTable();
        $this->articleTable->setDbAdapter($adapter);

        $this->table = new CollectionArticleTable();
        $this->table->setDbAdapter($adapter);

        parent::setUp();
        $conn->getConnection()->query('set foreign_key_checks=1');
    }

    public function testFetchArticlesByCollectionId()
    {
        $ids = array(
            1, 2, 4, 8, 16, 32
        );
        $expected = array();
        foreach($ids as $id) {
            $article = $this->articleTable->fetchArticleById($id);
            $expected[$article->getIndexerId()] = $article;
        }
        $this->assertEquals($expected, $this->table->fetchArticlesByCollectionId(1));

        $ids = array(
            2, 4, 6, 8, 10, 12, 14, 16, 18
        );
        $expected = array();
        foreach($ids as $id) {
            $article = $this->articleTable->fetchArticleById($id);
            $expected[$article->getIndexerId()] = $article;
        }
        $this->assertEquals($expected, $this->table->fetchArticlesByCollectionId(2));
        $ids = array(
            25, 27, 29, 31, 33, 35, 37, 39
        );
        $expected = array();
        foreach($ids as $id) {
            $article = $this->articleTable->fetchArticleById($id);
            $expected[$article->getIndexerId()] = $article;
        }
        $this->assertEquals($expected, $this->table->fetchArticlesByCollectionId(3));
    }

    public function testSaveArticles()
    {
        $ids = array(
            4, 8, 12, 16, 20, 24, 28, 32, 36
        );
        $articles = array();
        foreach($ids as $id) {
            $articles[$id] = $this->articleTable->fetchArticleById($id);
        }
        $this->table->saveArticles($articles, 2);
        $rowset = $this->table->select(array(
                'collection_id' => 2
            )
        );
        $pos = 0;
        foreach($rowset as $row) {
            $this->assertEquals($ids[$pos], $row['article_id']);
            $this->assertEquals($pos + 1, $row['position']);
            $pos++;
        }
    }
} 