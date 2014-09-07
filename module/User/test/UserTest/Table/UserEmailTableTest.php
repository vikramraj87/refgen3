<?php
namespace UserTest\Table;

use UserTest\DbTestCase;
use User\Table\UserEmailTable;

class UserEmailTableTest extends DbTestCase
{
    /** @var UserEmailTable */
    private $table;

    private function table()
    {
        if(null === $this->table) {
            $adapter = $this->getAdapter();
            $this->table = new UserEmailTable();
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

    public function testCheckExistingEmail()
    {
        $rowCount = $this->getConnection()->getRowCount('user_emails');
        $result = $this->table()->checkEmail('dr.vikramraj87@gmail.com', 1);
        $this->assertEquals(true, $result);
        $this->assertEquals($rowCount, $this->getConnection()->getRowCount('user_emails'));
    }

    public function testCheckNonExistingEmail()
    {
        $rowCount = $this->getConnection()->getRowCount('user_emails');
        $result = $this->table()->checkEmail('dr.vikramraj@gmail.com', 1);
        $this->assertEquals(true, $result);
        $emails = $this->table()->fetchEmailsByUserId(1);
        $this->assertEquals(3, count($emails));
        $this->assertTrue(in_array('vikramraj87@gmail.com', $emails));
        $this->assertTrue(in_array('dr.vikramraj87@gmail.com', $emails));
        $this->assertTrue(in_array('dr.vikramraj@gmail.com', $emails));
        $this->assertEquals($rowCount + 1, $this->getConnection()->getRowCount('user_emails'));
    }

    public function testFetchEmailsByUserId()
    {
        $emails = $this->table()->fetchEmailsByUserId(1);
        $this->assertEquals(2, count($emails));
        $this->assertTrue(in_array('vikramraj87@gmail.com', $emails));
        $this->assertTrue(in_array('dr.vikramraj87@gmail.com', $emails));

        $emails = $this->table()->fetchEmailsByUserId(2);
        $this->assertEquals(2, count($emails));
        $this->assertTrue(in_array('kirthiviswanath@gmail.com', $emails));
        $this->assertTrue(in_array('kvartandcrafts@gmail.com', $emails));

        $emails = $this->table()->fetchEmailsByUserId(4);
        $this->assertEquals(1, count($emails));
        $this->assertEquals('ishuips6@gmail.com', $emails[0]);
    }

    public function testFetchEmailsByNonExistingUserId()
    {
        $this->assertNull($this->testFetchEmailsByUserId(10));
        $this->assertNull($this->testFetchEmailsByUserId(101));
    }
} 