<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 13/07/14
 * Time: 1:23 PM
 */

namespace UserTest\Table;

use User\table\SocialTable;
use UserTest\DbTestCase;
use User\Table\UserSocialTable;
class UserSocialTableTest extends DbTestCase
{
    /** @var UserSocialTable */
    private $table;

    private function table()
    {
        if(null === $this->table) {
            $adapter = $this->getAdapter();
            $socialTable = new SocialTable();
            $socialTable->setDbAdapter($adapter);

            $this->table = new UserSocialTable();
            $this->table->setSocialTable($socialTable);
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

    public function testFetchBySocialAndSocialId()
    {
        $data = $this->table()->fetchBySocialAndSocialId('Facebook', '10201596577796234');
        $this->assertEquals(2, $data['userId']);

        $data = $this->table()->fetchBySocialAndSocialId('Google', '106904554194481876715');
        $this->assertEquals(6, $data['userId']);
    }

    public function testFetchBySocialAndNonExistingSocialId()
    {
        $this->assertNull(
            $this->table()->fetchBySocialAndSocialId('Google', '1234567887654321')
        );
    }

    public function testFetchByUserIdAndSocial()
    {
        $data = $this->table()->fetchByUserIdAndSocial(2,'Facebook');
        $this->assertEquals('10201596577796234', $data['socialId']);
    }

    public function testFetchByUserIdAndNonExistingSocial()
    {
        $this->assertNull($this->table()->fetchByUserIdAndSocial(3,'Facebook'));
    }

    public function testFetchByNonExistingUserAndSocial()
    {
        $this->assertNull($this->table()->fetchByUserIdAndSocial(8,0));
    }

    public function testCheckExistingUserIdAndSocial()
    {
        $rowCount = $this->getConnection()->getRowCount('user_socials');
        $result = $this->table()->checkUserIdAndSocial(
            4, 'Google', '109057272140035741568'
        );
        $this->assertEquals($rowCount, $this->getConnection()->getRowCount('user_socials'));
        $this->assertTrue($result);
    }

    public function testCheckNewUserIdAndSocial()
    {
        $rowCount = $this->getConnection()->getRowCount('user_socials');
        $result = $this->table()->checkUserIdAndSocial(4, 'Facebook', '1234567887654321');
        $this->assertEquals($rowCount + 1, $this->getConnection()->getRowCount('user_socials'));
        $this->assertTrue($result);
    }
}