<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 26/06/14
 * Time: 11:48 PM
 */

namespace CollectionTest\CollectionTest\Entity;

use PHPUnit_Framework_TestCase;
use Article\Entity\Article;
use Collection\Entity\Collection;

class CollectionTest extends PHPUnit_Framework_TestCase
{
    private $sample1;

    private function sample1()
    {
        if(null === $this->sample1) {
            $ids = array(
                13,12,11,1,2,3,10,9,8,7
            );
            foreach($ids as $id) {
                $article = new Article();
                $article->setId($id);
                $this->sample1[] = $article;
            }
        }
        return $this->sample1;
    }

    public function testOrderedList()
    {
        $sample = $this->sample1();
        $expected = array();
        foreach($sample as $article) {
            $expected[$article->getId()] = $article;
        }
        $collection = new Collection();
        $collection->setArticles($sample);
        $this->assertEquals($expected, $collection->getArticles());

        $article = new Article();
        $article->setId(100);
        $collection->addArticle($article);
        $expected[$article->getId()] = $article;
        $this->assertEquals($expected, $collection->getArticles());

        $collection->removeArticle(11);
        unset($expected[11]);
        $this->assertEquals($expected, $collection->getArticles());
    }

    public function testSerialize()
    {
        $sample = $this->sample1();
        $expected = array(
            'id'         => 0,
            'name'       => '',
            'createdOn'  => null,
            'updatedOn'  => null,
            'articleIds' => array(
                13,12,11,1,2,3,10,9,8,7
            )
        );
        $collection = new Collection;
        $collection->setArticles($sample);
        $this->assertEquals($expected, $collection->serialize());

        $collection->removeArticle(11);
        $expected['articleIds'] = array(13,12,1,2,3,10,9,8,7);
        $this->assertEquals($expected, $collection->serialize());
    }

    /*
    public function testDataToXml()
    {
        $dbHandle = mysql_connect(
            'localhost',
            'root',
            'K1rth1k@s1n1'
        ) or die('could not connect to db');

        $selected = mysql_select_db('refgen3', $dbHandle)
        or die('could not select database');

        $tables = array(
            'journals',
            'articles',
            'article_authors',
            'article_abstract_paras'
        );
        $datasetXml = new \SimpleXMLElement('<dataset></dataset>');

        foreach($tables as $table) {
            $tableXml = $datasetXml->addChild('table');
            $tableXml->addAttribute('name', $table);

            $fields = mysql_query('SHOW FIELDS FROM ' . $table, $dbHandle);

            $cols = array();
            while($r = mysql_fetch_assoc($fields)) {
                $cols[] = $col = $r['Field'];
                $tableXml->addChild('column', $col);
            }

            $data = mysql_query('SELECT * FROM '. $table, $dbHandle);
            while($r = mysql_fetch_assoc($data)) {
                $rowXml = $tableXml->addChild('row');
                foreach($cols as $col) {
                    $value = $r[$col];
                    if(null === $value) {
                        $rowXml->addChild('null');
                    } else {
                        $value = str_replace('&', '&amp;amp;', $value);
                        $rowXml->addChild('value', $value);
                    }
                }
            }

        }
        $result = $datasetXml->saveXML(__DIR__ . '/fixture.xml');
    }
*/

} 