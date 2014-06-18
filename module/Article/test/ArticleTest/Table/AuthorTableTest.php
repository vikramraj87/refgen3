<?php
namespace ArticleTest\Table;

use ArticleTest\DbTestCase;
use Article\Table\AuthorTable;
use Article\Entity\Author;

class AuthorTableTest extends DbTestCase
{
    /** @var \Article\Table\AuthorTable */
    protected $table;

    protected function setUp()
    {
        $conn = $this->getConnection();
        $conn->getConnection()->query('set foreign_key_checks=0');
        $adapter = $this->getAdapter();
        $this->table = new AuthorTable();
        $this->table->setDbAdapter($adapter);
        parent::setUp();
        $conn->getConnection()->query('set foreign_key_checks=1');
    }

    public function testFetchByArticleId()
    {
        $expected = array();
        $data = array(
            array(
                'id'        => 1,
                'last_name' => 'Zhu',
                'fore_name' => 'Ming-Yue',
                'initials'  => 'MY'
            ),
            array(
                'id'        => 2,
                'last_name' => 'Chen',
                'fore_name' => 'Fan',
                'initials'  => 'F'
            ),
            array(
                'id'        => 3,
                'last_name' => 'Niyazi',
                'fore_name' => 'Mayinuer',
                'initials'  => 'M'
            ),
            array(
                'id'        => 4,
                'last_name' => 'Sui',
                'fore_name' => 'Shuang',
                'initials'  => 'S'
            ),
            array(
                'id'        => 5,
                'last_name' => 'Gao',
                'fore_name' => 'Dong-Mei',
                'initials'  => 'DM'
            )
        );
        $expected = $this->authorsFromArray($data);
        $this->assertEquals($expected, $this->table->fetchAuthorsByArticleId(1));

        $expected2 = array();
        $data2 = array(
            array(
                'id'        => 6,
                'last_name' => 'Mao',
                'fore_name' => 'Lu',
                'initials'  => 'L'
            ),
            array(
                'id'        => 7,
                'last_name' => 'Levin',
                'fore_name' => 'Simon',
                'initials'  => 'S'
            ),
            array(
                'id'        => 8,
                'last_name' => 'Faesen',
                'fore_name' => 'Mark',
                'initials'  => 'M'
            ),
            array(
                'id'        => 9,
                'last_name' => 'Lewis',
                'fore_name' => 'David A',
                'initials'  => 'DA'
            ),
            array(
                'id'        => 10,
                'last_name' => 'Goeieman',
                'fore_name' => 'Bridgette J',
                'initials'  => 'BJ'
            ),
            array(
                'id'        => 11,
                'last_name' => 'Swarts',
                'fore_name' => 'Avril J',
                'initials'  => 'AJ'
            ),
            array(
                'id'        => 12,
                'last_name' => 'Rakhombe',
                'fore_name' => 'Ntombiyenkosi',
                'initials'  => 'N'
            ),
            array(
                'id'        => 13,
                'last_name' => 'Michelow',
                'fore_name' => 'Pam M',
                'initials'  => 'PM'
            ),
            array(
                'id'        => 14,
                'last_name' => 'Williams',
                'fore_name' => 'Sophie',
                'initials'  => 'S'
            ),
            array(
                'id'        => 15,
                'last_name' => 'Smith',
                'fore_name' => 'Jennifer S',
                'initials'  => 'JS'
            ),
        );
        $expected2 = $this->authorsFromArray($data2);
        $this->assertEquals($expected2, $this->table->fetchAuthorsByArticleId(2));
    }

    public function testFetchByNonExistentArticleId()
    {
        $this->assertEquals(array(), $this->table->fetchAuthorsByArticleId(1001));
    }

    public function testCreateAuthor()
    {
        $author = new Author();
        $author->setForeName('Vikram Raj');
        $author->setLastName('Gopinathan');
        $author->setInitials('VR');

        $author2 = new Author();
        $author2->setForeName('Kirthika');
        $author2->setLastName('Vikram');
        $author2->setInitials('K');

        $authors = array($author, $author2);

        $result = $this->table->createAuthors($authors, 4);

        $this->assertEquals(true, $result);

        $authors = $this->table->fetchAuthorsByArticleId(4);
        // $a1 = $authors[0]; // already existing Thomas GM
        $a2 = $authors[1];
        $a3 = $authors[2];

        $author->setId(20);
        $author2->setId(21);

        $this->assertEquals(3, count($authors));
        $this->assertEquals($author, $a2);
        $this->assertEquals($author2, $a3);

    }

    public function testCreateAuthorWithEmptyArray()
    {
        $result = $this->table->createAuthors(array(), 4);
        $this->assertEquals(true, $result);

        $authors = $this->table->fetchAuthorsByArticleId(4);
        $this->assertEquals(1, count($authors));

        $this->assertEquals(19, $authors[0]->getId());
        $this->assertEquals('Gillian M', $authors[0]->getForeName());
    }

    public function testDeleteAuthors()
    {
        $result = $this->table->deleteAuthorsByArticleId(1);
        $this->assertEquals(true, $result);

        $authors = $this->table->fetchAuthorsByArticleId(1);
        $this->assertEquals(array(), $authors);
    }

    /**
     * @param array $data
     * @return \Article\Entity\Author[]
     */
    private function authorsFromArray(array $data = array())
    {
        /** @var \Article\Entity\Author[] $authors */
        $authors = array();
        foreach($data as $authorData) {
            $authors[] = Author::createFromArray($authorData);
        }
        return $authors;
    }
}