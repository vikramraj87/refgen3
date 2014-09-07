<?php
namespace UserTest\Table;

use UserTest\DbTestCase;
use User\Table\SocialTable;

class SocialTableTest extends DbTestCase
{
    /** @var SocialTable */
    private $table;

    private function table()
    {
        if(null === $this->table) {
            $adapter = $this->getAdapter();
            $this->table = new SocialTable();
            $this->table->setDbAdapter($adapter);
        }
        return $this->table;
    }

    protected function setUp()
    {
        $conn = $this->getConnection();
        $conn->getConnection()->query('set foreign_key_checks=0');
        parent::setUp();
        $conn->getConnection()->query('set foreign_key_checks=1');
    }

    public function testFetchIdByExistingName()
    {
        $id = $this->table()->fetchIdByName('Google');
        $this->assertEquals(2, $id);
    }

    public function testFetchIdByNonExistingName()
    {
        $this->assertNull($this->table()->fetchIdByName('Twitter'));
    }

    public function testFetchNameByExistingId()
    {
        $name = $this->table()->fetchNameById(1);
        $this->assertEquals('Facebook', $name);
    }

    public function testFetchNameByNonExistingId()
    {
        $this->assertNull($this->table()->fetchNameById(3));
    }
} 