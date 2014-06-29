<?php
namespace UserTest\Table;

use UserTest\DbTestCase;
use User\Table\UserTable,
    User\Entity\User;

class UserTableTest extends DbTestCase
{
    /** @var \User\Table\UserTable */
    protected $table;

    protected function setUp()
    {
        $conn = $this->getConnection();
        $conn->getConnection()->query('set foreign_key_checks=0');
        $adapter = $this->getAdapter();
        $this->table = new UserTable();
        $this->table->setDbAdapter($adapter);
        parent::setUp();
        $conn->getConnection()->query('set foreign_key_checks=1');
    }

    public function testCheckUserWithFacebookId()
    {
        $expected = new User();
        $expected->setId(1);
        $expected->setEmail('dr.vikramraj87@gmail.com');
        $expected->setFacebookId('688970634489822');
        $expected->setName('Vikram Raj Gopinathan');
        $expected->setCreatedOn(new \DateTime('2014-06-15 10:15:00'));
        $expected->setLastLoggedIn(new \DateTime('2014-06-17 02:35:00'));

        $user = new User();
        $user->setFacebookId('688970634489822');
        $user->setEmail('dr.vikramraj87@gmail.com');
        $user->setName('Vikram Raj Gopinathan');

        $result = $this->table->checkUser($user);
        $this->assertEquals(1, $result->getId());
        $this->assertEquals('dr.vikramraj87@gmail.com', $result->getEmail());
        $this->assertEquals('688970634489822', $result->getFacebookId());
        $this->assertEquals('Vikram Raj Gopinathan', $result->getName());
        $this->assertEquals(new \DateTime('2014-06-15 10:15:00'), $result->getCreatedOn());
        $this->assertTrue($result->getLastLoggedIn() instanceof \DateTime);
    }

    public function testCheckUserWithEmail()
    {
        $user = new User();
        $user->setEmail('kirthiviswanath@gmail.com');
        $user->setName('Kirthika Vikram');
        $user->setFacebookId('');

        $result = $this->table->checkUser($user);
        $this->assertEquals(2, $result->getId());
        $this->assertEquals('kirthiviswanath@gmail.com', $result->getEmail());
        $this->assertEquals('Kirthika Vikram', $result->getName());
        $this->assertNull($result->getFacebookId());
        $this->assertTrue($result->getCreatedOn() instanceof \DateTime);
        $this->assertTrue($result->getLastLoggedIn() instanceof \DateTime);
    }

    public function testCheckUserWithFacebookDataPreviouslyRegisteredViaGmail()
    {
        $user = new User();
        $user->setEmail('kirthiviswanath@gmail.com');
        $user->setName('Kirthika Vikram');
        $user->setFacebookId('0123456789');

        $result = $this->table->checkUser($user);
        $this->assertEquals(2, $result->getId());
        $this->assertEquals('kirthiviswanath@gmail.com', $result->getEmail());
        $this->assertEquals('Kirthika Vikram', $result->getName());
        $this->assertEquals('0123456789', $result->getFacebookId());
        $this->assertTrue($result->getCreatedOn() instanceof \DateTime);
        $this->assertTrue($result->getLastLoggedIn() instanceof \DateTime);
    }

    public function testCheckNewUser()
    {
        $user = new User();
        $user->setEmail('nirmalraj78@gmail.com');
        $user->setName('Nirmal Raj Gopinathan');
        $user->setFacebookId('9876543210');

        $result = $this->table->checkUser($user);
        $this->assertEquals(3, $result->getId());
        $this->assertEquals('nirmalraj78@gmail.com', $result->getEmail());
        $this->assertEquals('Nirmal Raj Gopinathan', $result->getName());
        $this->assertEquals('9876543210', $result->getFacebookId());
        $this->assertTrue($result->getLastLoggedIn() instanceof \DateTime);
        $this->assertTrue($result->getCreatedOn() instanceof \DateTime);

        $user = new User();
        $user->setEmail('naishadhanirmal@gmail.com');
        $user->setName('Naishadha Nirmal');

        $result = $this->table->checkUser($user);
        $this->assertEquals(4, $result->getId());
        $this->assertEquals('naishadhanirmal@gmail.com', $result->getEmail());
        $this->assertEquals('Naishadha Nirmal', $result->getName());
        $this->assertNull($result->getFacebookId());
        $this->assertTrue($result->getLastLoggedIn() instanceof \DateTime);
        $this->assertTrue($result->getCreatedOn() instanceof \DateTime);

        $user->setFacebookId('4321098765');
        $result = $this->table->checkUser($user);
        $this->assertEquals(4, $result->getId());
        $this->assertEquals('naishadhanirmal@gmail.com', $result->getEmail());
        $this->assertEquals('Naishadha Nirmal', $result->getName());
        $this->assertEquals('4321098765', $result->getFacebookId());
        $this->assertTrue($result->getLastLoggedIn() instanceof \DateTime);
        $this->assertTrue($result->getCreatedOn() instanceof \DateTime);
    }
}