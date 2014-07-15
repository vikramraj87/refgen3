<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 13/07/14
 * Time: 1:23 PM
 */

namespace UserTest\Table;

use UserTest\DbTestCase;
use User\Table\UserSocialTable;
class UserSocialTableTest extends DbTestCase
{
    /** @var UserSocialTable */
    protected $table;

    protected function setUp()
    {
        $conn = $this->getConnection();
        $conn->getConnection()->query('set foreign_key_checks=0');
        $adapter = $this->getAdapter();
        $this->table = new UserSocialTable();
        $this->table->setDbAdapter($adapter);
        parent::setUp();
        $conn->getConnection()->query('set foreign_key_checks=1');
    }

    public function testFetchBySocialAndSocialId()
    {
        $expected = array(
            'userId'  => 1,
            'socialId' => '109671001037346774242',
            'picture' => 'https://lh6.googleusercontent.com/-huEFicSGyKU/AAAAAAAAAAI/AAAAAAAAA4I/m0PFsWD8QFg/photo.jpg'
        );
        $data = $this->table->fetchBySocialAndSocialId(2, '109671001037346774242');
        $this->assertEquals($expected, $data);
    }

    public function testFetchBySocialAndInvalidSocialId()
    {
        $data = $this->table->fetchBySocialAndSocialId(2, '1000100010001000');
        $this->assertNull($data);
    }

    public function testFetchByUserIdAndSocial()
    {
        $expected = array(
            'userId'    => '1',
            'socialId'  => '109671001037346774242',
            'picture' => 'https://lh6.googleusercontent.com/-huEFicSGyKU/AAAAAAAAAAI/AAAAAAAAA4I/m0PFsWD8QFg/photo.jpg'
        );
        $data = $this->table->fetchByUserIdAndSocial(1,2);
        $this->assertEquals($expected, $data);
    }

    public function testFetchByNonExistingUserIdOrSocial()
    {
        $this->assertNull($this->table->fetchByUserIdAndSocial(3,2));
        $this->assertNull($this->table->fetchByUserIdAndSocial(1,1));
    }

    public function testInsertSocialAndSocialId()
    {
        $data = array(
            'user_id'   => 1,
            'social_id' => '684982804888605',
            'social'    => 1,
            'picture'   => 'https://fbcdn-profile-a.akamaihd.net/hprofile-ak-xpa1/t1.0-1/c64.5.114.114/s50x50/1891165_635032089883677_31263367_n.jpg'
        );

        $rowsAffected = $this->table->insert($data);
        $this->assertEquals(1, $rowsAffected);

        $expected = array(
            'userId' => 1,
            'socialId' => '684982804888605',
            'picture' => 'https://fbcdn-profile-a.akamaihd.net/hprofile-ak-xpa1/t1.0-1/c64.5.114.114/s50x50/1891165_635032089883677_31263367_n.jpg'
        );
        $observed = $this->table->fetchBySocialAndSocialId(1, '684982804888605');
        $this->assertEquals($expected, $observed);
    }
} 