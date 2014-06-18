<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 16/06/14
 * Time: 8:08 PM
 */

namespace PubmedTest\Service;

use Pubmed\Entity\Adapter;
use PubmedTest\DbTestCase;
use Article\Table\ArticleTable;
use Pubmed\Service\PubmedService;
use Zend\EventManager\Event,
    Zend\Http\Client\Adapter\Exception\RuntimeException;

class PubmedServiceTest extends DbTestCase
{
    /** @var ArticleTable */
    private $table;

    /** @var PubmedService */
    private $service;

    protected function setUp()
    {
        $conn = $this->getConnection();
        $conn->getConnection()->query('set foreign_key_checks=0');
        $adapter = $this->getAdapter();
        $this->table = new ArticleTable();
        $this->table->setDbAdapter($adapter);
        $this->service = new PubmedService();
        $this->service->setTable($this->table);
        parent::setUp();
        $conn->getConnection()->query('set foreign_key_checks=1');
    }

    public function testFetchByIds()
    {
        /**
         * todo: Expecting an assert
         */
        $adapter = Adapter::getInstance();
        $adapterIds = array(
            'PMID12345678',
            'PMID21436587',
            'PMID23218765'
        );
        $adapter->events()->attach(Adapter::FETCH_IDS_EVENT, function(Event $e) use ($adapterIds) {
            $this->assertEquals($adapterIds, $e->getParam('ids'));
        });

        $ids = array(
            $adapterIds[0],
            'PMID24914887',
            'PMID24837888',
            $adapterIds[1],
            'PMID24914885',
            $adapterIds[2]
        );
        $incomplete = false;
        $result = $this->service->fetchArticlesByIndexerIds($ids, $incomplete);
        if($incomplete) {
            $this->assertEquals(3, count($result));
            $dbIds = array($ids[1], $ids[2], $ids[4]);
            $this->assertEquals($dbIds, array_keys($result));
            $this->assertEquals(4, $this->getConnection()->getRowCount('articles'));
        } else {
            $this->assertEquals(6, count($result));
            $this->assertEquals($ids, array_keys($result));
            $this->assertEquals(7, $this->getConnection()->getRowCount('articles'));
        }
        $msg = 'PubmedService tested in the absence of internet connection or ';
        $msg .= 'there is some communication error with the Pubmed server';
        $this->assertFalse($incomplete, $msg);
    }

    public function testFetchByIdsWithDuplicateIds()
    {

    }
} 