<?php
namespace UserTest\Table;

use User\table\RoleTable;
use UserTest\DbTestCase;

class RoleTableTest extends DbTestCase
{
    /** @var \User\Table\RoleTable */
    protected $table;

    protected function setUp()
    {
        $conn = $this->getConnection();
        $conn->getConnection()->query('set foreign_key_checks=0');
        parent::setUp();
        $conn->getConnection()->query('set foreign_key_checks=1');
    }

    private function table()
    {
        if(null === $this->table) {
            $adapter = $this->getAdapter();

            $this->table = new RoleTable();
            $this->table->setDbAdapter($adapter);
        }
        return $this->table;
    }

    public function testFetchRoleByExistingId()
    {
        $this->assertEquals('User', $this->table()->fetchRoleById(1));
        $this->assertEquals('Moderator', $this->table()->fetchRoleById(2));
        $this->assertEquals('Admin', $this->table()->fetchRoleById(3));
    }

    public function testFetchRoleByNonExistingId()
    {
        $this->assertNull($this->table()->fetchRoleById(4));
    }

} 