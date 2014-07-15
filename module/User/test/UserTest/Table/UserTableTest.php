<?php
namespace UserTest\Table;

use UserTest\DbTestCase;
use User\Table\UserTable,
    User\Table\UserSocialTable,
    User\Entity\User;

class UserTableTest extends DbTestCase
{
    /** @var \User\Table\UserTable */
    protected $table;

    /** @var \User\Table\UserSocialTable */
    protected $userSocialTable;

    protected function setUp()
    {
        $conn = $this->getConnection();
        $conn->getConnection()->query('set foreign_key_checks=0');
        $adapter = $this->getAdapter();
        $this->table = new UserTable();
        $this->userSocialTable = new UserSocialTable();
        $this->table->setDbAdapter($adapter);
        $this->userSocialTable->setDbAdapter($adapter);
        parent::setUp();
        $conn->getConnection()->query('set foreign_key_checks=1');
    }
    /*
    public function testFetchBySocialIdAndSocial()
    {
        $expected = new User();
        $expected->setId(1)
                 ->setEmail('dr.vikramraj87@gmail.com')
                 ->setFirstName('Vikram Raj')
                 ->setMiddleName('')
                 ->setLastName('Gopinathan')
                 ->setName('Vikram Raj Gopinathan')
                 ->setPictureLink('https://lh6.googleusercontent.com/-huEFicSGyKU/AAAAAAAAAAI/AAAAAAAAA4I/m0PFsWD8QFg/photo.jpg')
                 ->setSocialId('109671001037346774242');

        $user = $this->table->fetchUserByIdAndSocial(1,2);
        $this->assertEquals($expected, $user);
    }

    public function testFetchByEmailAndExistingSocialData()
    {
        $expected = new User();
        $expected->setId(1)
                 ->setEmail('dr.vikramraj87@gmail.com')
                 ->setFirstName('Vikram Raj')
                 ->setMiddleName('')
                 ->setLastName('Gopinathan')
                 ->setName('Vikram Raj Gopinathan')
                 ->setPictureLink('https://lh6.googleusercontent.com/-huEFicSGyKU/AAAAAAAAAAI/AAAAAAAAA4I/m0PFsWD8QFg/photo.jpg')
                 ->setSocialId('109671001037346774242');
        $user = $this->table->fetchUserByEmailAndSocial(
            'dr.vikramraj87@gmail.com',
            2,
            '109671001037346774242',
            'https://lh6.googleusercontent.com/-huEFicSGyKU/AAAAAAAAAAI/AAAAAAAAA4I/m0PFsWD8QFg/photo.jpg'
        );
        $this->assertEquals($expected, $user);
    }

    public function testFetchByEmailAndNonExistingSocialData()
    {
        $expected = new User();
        $expected->setId(1)
            ->setEmail('dr.vikramraj87@gmail.com')
            ->setFirstName('Vikram Raj')
            ->setMiddleName('')
            ->setLastName('Gopinathan')
            ->setName('Vikram Raj Gopinathan')
            ->setPictureLink('https://fbcdn-profile-a.akamaihd.net/hprofile-ak-xpa1/t1.0-1/c64.5.114.114/s50x50/1891165_635032089883677_31263367_n.jpg')
            ->setSocialId('684982804888605');
        $user = $this->table->fetchUserByEmailAndSocial(
            'dr.vikramraj87@gmail.com',
            1,
            '684982804888605',
            'https://fbcdn-profile-a.akamaihd.net/hprofile-ak-xpa1/t1.0-1/c64.5.114.114/s50x50/1891165_635032089883677_31263367_n.jpg'
        );
        $this->assertEquals($expected, $user);
    }
    */
    public function testCheckUserWithNonExistingSocialData()
    {
        $expected = new User();
        $expected->setId(1)
                 ->setEmail('dr.vikramraj87@gmail.com')
                 ->setFirstName('Vikram Raj')
                 ->setMiddleName('')
                 ->setLastName('Gopinathan')
                 ->setName('Vikram Raj Gopinathan')
                 ->setPictureLink('https://fbcdn-profile-a.akamaihd.net/hprofile-ak-xpa1/t1.0-1/c64.5.114.114/s50x50/1891165_635032089883677_31263367_n.jpg')
                 ->setSocialId('684982804888605');
        $user = new User();
        $user->setEmail('dr.vikramraj87@gmail.com')
             ->setFirstName('Vikram Raj')
             ->setMiddleName('')
             ->setLastName('Gopinathan')
             ->setName('Vikram Raj Gopinathan')
             ->setPictureLink('https://fbcdn-profile-a.akamaihd.net/hprofile-ak-xpa1/t1.0-1/c64.5.114.114/s50x50/1891165_635032089883677_31263367_n.jpg')
             ->setSocialId('684982804888605');
        $this->assertEquals($expected, $this->table->checkUser($user, 1));
    }

    public function testCheckUserWithExistingSocialData()
    {
        $expected = new User();
        $expected->setId(1)
            ->setEmail('dr.vikramraj87@gmail.com')
            ->setFirstName('Vikram Raj')
            ->setMiddleName('')
            ->setLastName('Gopinathan')
            ->setName('Vikram Raj Gopinathan')
            ->setPictureLink('https://lh6.googleusercontent.com/-huEFicSGyKU/AAAAAAAAAAI/AAAAAAAAA4I/m0PFsWD8QFg/photo.jpg')
            ->setSocialId('109671001037346774242');
        $user = new User();
        $user->setEmail('dr.vikramraj87@gmail.com')
            ->setFirstName('Vikram Raj')
            ->setMiddleName('')
            ->setLastName('Gopinathan')
            ->setName('Vikram Raj Gopinathan')
            ->setPictureLink('https://lh6.googleusercontent.com/-huEFicSGyKU/AAAAAAAAAAI/AAAAAAAAA4I/m0PFsWD8QFg/photo.jpg')
            ->setSocialId('109671001037346774242');
        $this->assertEquals($expected, $this->table->checkUser($user, 2));
    }

    public function testCheckNonExistingUser()
    {
        $expected = new User();
        $expected->setId(3)
            ->setEmail('kirthika2203@gmail.com')
            ->setFirstName('kirthika')
            ->setMiddleName('')
            ->setLastName('viswanathan')
            ->setName('kirthika viswanathan')
            ->setPictureLink('https://lh3.googleusercontent.com/-R-VrIwNVp6E/AAAAAAAAAAI/AAAAAAAAAB0/1nm_k5aGDh4/photo.jpg')
            ->setSocialId('116662090606954236795');
        $user = new User();
        $user->setEmail('kirthika2203@gmail.com')
            ->setFirstName('kirthika')
            ->setMiddleName('')
            ->setLastName('viswanathan')
            ->setName('kirthika viswanathan')
            ->setPictureLink('https://lh3.googleusercontent.com/-R-VrIwNVp6E/AAAAAAAAAAI/AAAAAAAAAB0/1nm_k5aGDh4/photo.jpg')
            ->setSocialId('116662090606954236795');
        $this->assertEquals($expected, $this->table->checkUser($user, 2));

        $socialData = $this->userSocialTable->fetchByUserIdAndSocial(3,2);
        $this->assertEquals(3, $socialData['userId']);
        $this->assertEquals('https://lh3.googleusercontent.com/-R-VrIwNVp6E/AAAAAAAAAAI/AAAAAAAAAB0/1nm_k5aGDh4/photo.jpg', $socialData['picture']);
        $this->assertEquals('116662090606954236795', $socialData['socialId']);

    }
}