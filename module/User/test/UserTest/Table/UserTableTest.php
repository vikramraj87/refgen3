<?php
namespace UserTest\Table;

use User\table\SocialTable;
use User\table\UserEmailTable;
use UserTest\DbTestCase;
use User\Table\UserTable,
    User\Table\UserSocialTable,
    User\Table\RoleTable,
    User\Entity\User;

class UserTableTest extends DbTestCase
{
    /** @var \User\Table\UserTable */
    protected $table;

    protected function setUp()
    {
        $conn = $this->getConnection();
        $conn->getConnection()->query('set foreign_key_checks=0');
        parent::setUp();
        $conn->getConnection()->query('set foreign_key_checks=1');
    }

    public function testCheckWithExistingSocialId()
    {
        $userRowCount       = $this->getConnection()->getRowCount('users');
        $userSocialRowCount = $this->getConnection()->getRowCount('user_socials');
        $userEmailRowCount  = $this->getConnection()->getRowCount('user_emails');

        $input = new User();
        $input->setEmail('vikramraj87@gmail.com')
              ->setFirstName('Vikram Raj')
              ->setMiddleName('')
              ->setLastName('Gopinathan')
              ->setName('Vikram Raj Gopinathan')
              ->setPictureLink('http://google.co.in/picture?sz=50')
              ->setSocialId('109671001037346774242');
        /** @var User $result */
        $result = $this->table()->checkUser($input, 'Google');
        $this->assertEquals(1, $result->getId());
        $this->assertEquals('vikramraj87@gmail.com', $result->getEmail());
        $this->assertEquals('Vikram Raj', $result->getFirstName());
        $this->assertEquals('Gopinathan', $result->getLastName());
        $this->assertEquals('Vikram Raj Gopinathan', $result->getName());
        $this->assertEquals('http://google.co.in/picture?sz=50', $result->getPictureLink());
        $this->assertEquals('Admin', $result->getRole());
        $this->assertEquals('109671001037346774242', $result->getSocialId());

        $this->assertEquals($userRowCount,       $this->getConnection()->getRowCount('users'));
        $this->assertEquals($userSocialRowCount, $this->getConnection()->getRowCount('user_socials'));
        $this->assertEquals($userEmailRowCount,  $this->getConnection()->getRowCount('user_emails'));

        $input = new User();
        $input->setEmail('vikramraj87@gmail.com')
            ->setFirstName('Vikram Raj')
            ->setMiddleName('')
            ->setLastName('Gopinathan')
            ->setName('Vikram Raj Gopinathan')
            ->setPictureLink('http://fbn.co.in/picture?sz=50')
            ->setSocialId('702348216485397');
        /** @var User $result */
        $result = $this->table()->checkUser($input, 'Facebook');
        $this->assertEquals(1, $result->getId());
        $this->assertEquals('vikramraj87@gmail.com', $result->getEmail());
        $this->assertEquals('Vikram Raj', $result->getFirstName());
        $this->assertEquals('Gopinathan', $result->getLastName());
        $this->assertEquals('Vikram Raj Gopinathan', $result->getName());
        $this->assertEquals('http://fbn.co.in/picture?sz=50', $result->getPictureLink());
        $this->assertEquals('Admin', $result->getRole());
        $this->assertEquals('702348216485397', $result->getSocialId());

        $this->assertEquals($userRowCount,       $this->getConnection()->getRowCount('users'));
        $this->assertEquals($userSocialRowCount, $this->getConnection()->getRowCount('user_socials'));
        $this->assertEquals($userEmailRowCount,  $this->getConnection()->getRowCount('user_emails'));

        $input = new User();
        $input->setEmail('ishuips6@gmail.com')
              ->setFirstName('Ishwarya')
              ->setLastName('Viswanathan')
              ->setMiddleName('')
              ->setName('Ishwarya V')
              ->setPictureLink('http://google.co.in/picture_ishwarya?sz=50')
              ->setSocialId('109057272140035741568');
        /** @var User $result */
        $result = $this->table()->checkUser($input, 'Google');
        $this->assertEquals(4, $result->getId());
        $this->assertEquals('ishuips6@gmail.com', $result->getEmail());
        $this->assertEquals('Ishwarya', $result->getFirstName());
        $this->assertEquals('Viswanathan', $result->getLastName());
        $this->assertEquals('Ishwarya V', $result->getName());
        $this->assertEquals('http://google.co.in/picture_ishwarya?sz=50', $result->getPictureLink());
        $this->assertEquals('User', $result->getRole());
        $this->assertEquals('109057272140035741568', $result->getSocialId());

        $this->assertEquals($userRowCount,       $this->getConnection()->getRowCount('users'));
        $this->assertEquals($userSocialRowCount, $this->getConnection()->getRowCount('user_socials'));
        $this->assertEquals($userEmailRowCount,  $this->getConnection()->getRowCount('user_emails'));
    }

    public function testCheckingExistingSocialIdAndNonExistingEmail()
    {
        $userRowCount       = $this->getConnection()->getRowCount('users');
        $userSocialRowCount = $this->getConnection()->getRowCount('user_socials');
        $userEmailRowCount  = $this->getConnection()->getRowCount('user_emails');

        $input = new User();
        $input->setEmail('sumangalag@ymail.com')
              ->setFirstName('Sumangala')
              ->setLastName('Gopinathan')
              ->setMiddleName('')
              ->setName('Sumangala G')
              ->setPictureLink('http://google.co.in/picture_sumangala?sz=50')
              ->setSocialId('106904554194481876715');
        /** @var User $result */
        $result = $this->table()->checkUser($input, 'Google');
        $this->assertEquals(6, $result->getId());
        $this->assertEquals('sumangalag@ymail.com', $result->getEmail());
        $this->assertEquals('Sumangala', $result->getFirstName());
        $this->assertEquals('Gopinathan', $result->getLastName());
        $this->assertEquals('', $result->getMiddleName());
        $this->assertEquals('Sumangala G', $result->getName());
        $this->assertEquals('http://google.co.in/picture_sumangala?sz=50', $result->getPictureLink());
        $this->assertEquals('106904554194481876715', $result->getSocialId());

        $this->assertEquals($userRowCount,          $this->getConnection()->getRowCount('users'));
        $this->assertEquals($userSocialRowCount,    $this->getConnection()->getRowCount('user_socials'));
        $this->assertEquals($userEmailRowCount + 1, $this->getConnection()->getRowCount('user_emails'));

        $input = new User();
        $input->setEmail('dr.vikramraj@gmail.com')
            ->setFirstName('Vikram Raj')
            ->setMiddleName('')
            ->setLastName('Gopinathan')
            ->setName('Vikram Raj Gopinathan')
            ->setPictureLink('http://google.co.in/picture?sz=50')
            ->setSocialId('109671001037346774242');
        /** @var User $result */
        $result = $this->table()->checkUser($input, 'Google');
        $this->assertEquals(1, $result->getId());
        $this->assertEquals('dr.vikramraj@gmail.com', $result->getEmail());
        $this->assertEquals('Vikram Raj', $result->getFirstName());
        $this->assertEquals('Gopinathan', $result->getLastName());
        $this->assertEquals('Vikram Raj Gopinathan', $result->getName());
        $this->assertEquals('http://google.co.in/picture?sz=50', $result->getPictureLink());
        $this->assertEquals('Admin', $result->getRole());
        $this->assertEquals('109671001037346774242', $result->getSocialId());

        $this->assertEquals($userRowCount,          $this->getConnection()->getRowCount('users'));
        $this->assertEquals($userSocialRowCount,    $this->getConnection()->getRowCount('user_socials'));
        $this->assertEquals($userEmailRowCount + 2, $this->getConnection()->getRowCount('user_emails'));
    }

    public function testCheckingWithExistingEmailAndNonExistingSocial()
    {
        $userRowCount       = $this->getConnection()->getRowCount('users');
        $userSocialRowCount = $this->getConnection()->getRowCount('user_socials');
        $userEmailRowCount  = $this->getConnection()->getRowCount('user_emails');

        $input = new User();
        $input->setEmail('ishuips6@gmail.com')
            ->setFirstName('Ishwarya')
            ->setLastName('Viswanathan')
            ->setMiddleName('')
            ->setName('Ishwarya V')
            ->setPictureLink('http://fbn.co.in/picture_ishwarya?sz=50')
            ->setSocialId('765654543432321');
        /** @var User $result */
        $result = $this->table()->checkUser($input, 'Facebook');
        $this->assertEquals(4, $result->getId());
        $this->assertEquals('ishuips6@gmail.com', $result->getEmail());
        $this->assertEquals('Ishwarya', $result->getFirstName());
        $this->assertEquals('Viswanathan', $result->getLastName());
        $this->assertEquals('Ishwarya V', $result->getName());
        $this->assertEquals('http://fbn.co.in/picture_ishwarya?sz=50', $result->getPictureLink());
        $this->assertEquals('User', $result->getRole());
        $this->assertEquals('765654543432321', $result->getSocialId());

        $this->assertEquals($userRowCount,           $this->getConnection()->getRowCount('users'));
        $this->assertEquals($userSocialRowCount + 1, $this->getConnection()->getRowCount('user_socials'));
        $this->assertEquals($userEmailRowCount,      $this->getConnection()->getRowCount('user_emails'));

        $input = new User();
        $input->setEmail('sumangalagopinathan@gmail.com')
            ->setFirstName('Sumangala')
            ->setLastName('Gopinathan')
            ->setMiddleName('')
            ->setName('Sumangala G')
            ->setPictureLink('http://fbn.co.in/picture_sumangala?sz=50')
            ->setSocialId('8767656545543321');
        /** @var User $result */
        $result = $this->table()->checkUser($input, 'Facebook');
        $this->assertEquals(6, $result->getId());
        $this->assertEquals('sumangalagopinathan@gmail.com', $result->getEmail());
        $this->assertEquals('Sumangala', $result->getFirstName());
        $this->assertEquals('Gopinathan', $result->getLastName());
        $this->assertEquals('', $result->getMiddleName());
        $this->assertEquals('Sumangala G', $result->getName());
        $this->assertEquals('http://fbn.co.in/picture_sumangala?sz=50', $result->getPictureLink());
        $this->assertEquals('8767656545543321', $result->getSocialId());

        $this->assertEquals($userRowCount,           $this->getConnection()->getRowCount('users'));
        $this->assertEquals($userSocialRowCount + 2, $this->getConnection()->getRowCount('user_socials'));
        $this->assertEquals($userEmailRowCount, $this->getConnection()->getRowCount('user_emails'));
    }

    public function testCheckingWithNonExistingEmailAndNonExistingSocial()
    {
        $userRowCount       = $this->getConnection()->getRowCount('users');
        $userSocialRowCount = $this->getConnection()->getRowCount('user_socials');
        $userEmailRowCount  = $this->getConnection()->getRowCount('user_emails');

        $input = new User();
        $input->setEmail('test@ymail.com')
            ->setFirstName('Test')
            ->setLastName('Last')
            ->setMiddleName('')
            ->setName('Test L')
            ->setPictureLink('http://fbn.co.in/picture_test?sz=50')
            ->setSocialId('8757656545543321');
        /** @var User $result */
        $result = $this->table()->checkUser($input, 'Facebook');
        $this->assertEquals(7, $result->getId());
        $this->assertEquals('test@ymail.com', $result->getEmail());
        $this->assertEquals('Test', $result->getFirstName());
        $this->assertEquals('Last', $result->getLastName());
        $this->assertEquals('', $result->getMiddleName());
        $this->assertEquals('Test L', $result->getName());
        $this->assertEquals('http://fbn.co.in/picture_test?sz=50', $result->getPictureLink());
        $this->assertEquals('8757656545543321', $result->getSocialId());

        $this->assertEquals($userRowCount + 1,       $this->getConnection()->getRowCount('users'));
        $this->assertEquals($userSocialRowCount + 1, $this->getConnection()->getRowCount('user_socials'));
        $this->assertEquals($userEmailRowCount + 1,  $this->getConnection()->getRowCount('user_emails'));

        $input = new User();
        $input->setEmail('test123@ymail.com')
            ->setFirstName('Test123')
            ->setLastName('Last')
            ->setMiddleName('')
            ->setName('Test123 L')
            ->setPictureLink('http://fbn.co.in/picture_test123?sz=50')
            ->setSocialId('8757646545543321');
        /** @var User $result */
        $result = $this->table()->checkUser($input, 'Facebook');
        $this->assertEquals(8, $result->getId());
        $this->assertEquals('test123@ymail.com', $result->getEmail());
        $this->assertEquals('Test123', $result->getFirstName());
        $this->assertEquals('Last', $result->getLastName());
        $this->assertEquals('', $result->getMiddleName());
        $this->assertEquals('Test123 L', $result->getName());
        $this->assertEquals('http://fbn.co.in/picture_test123?sz=50', $result->getPictureLink());
        $this->assertEquals('8757646545543321', $result->getSocialId());

        $this->assertEquals($userRowCount + 2,       $this->getConnection()->getRowCount('users'));
        $this->assertEquals($userSocialRowCount + 2, $this->getConnection()->getRowCount('user_socials'));
        $this->assertEquals($userEmailRowCount + 2,  $this->getConnection()->getRowCount('user_emails'));
    }

    public function testFetchAllUsers()
    {
        $users = $this->table()->fetchAllUsers();
        $this->assertEquals($this->getConnection()->getRowCount('users'), count($users));


    }

    public function testFetchTotalCount()
    {
        $this->assertEquals($this->getConnection()->getRowCount('users'), $this->table()->getTotalCount());
    }

    private function table()
    {
        if(null === $this->table) {
            $adapter = $this->getAdapter();

            $socialTable = new SocialTable();
            $socialTable->setDbAdapter($adapter);

            $roleTable = new RoleTable();
            $roleTable->setDbAdapter($adapter);

            $userEmailTable = new UserEmailTable();
            $userEmailTable->setDbAdapter($adapter);

            $userSocialTable = new UserSocialTable();
            $userSocialTable->setSocialTable($socialTable);
            $userSocialTable->setDbAdapter($adapter);

            $this->table = new UserTable();
            $this->table->setDbAdapter($adapter);
            $this->table->setUserSocialTable($userSocialTable);
            $this->table->setRoleTable($roleTable);
            $this->table->setUserEmailTable($userEmailTable);
        }
        return $this->table;
    }
}