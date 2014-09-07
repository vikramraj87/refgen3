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

    public function testFetchByExistingArticleId()
    {
        $authors = $this->table->fetchAuthorsByArticleId(243);
        $expected = [
            [
                'id' => 1576,
                'lastName' => 'Bhatnagar',
                'foreName' => 'Shishir Kumar',
                'initials' => 'SK'
            ], [
                'id' => 1577,
                'lastName' => 'Chandra',
                'foreName' => 'Jagdish',
                'initials' => 'J'
            ], [
                'id' => 1578,
                'lastName' => 'Narayan',
                'foreName' => 'Shashi',
                'initials' => 'S'
            ], [
                'id' => 1579,
                'lastName' => 'Sharma',
                'foreName' => 'Sunita',
                'initials' => 'S'
            ], [
                'id' => 1580,
                'lastName' => 'Singh',
                'foreName' => 'Varinder',
                'initials' => 'V'
            ], [
                'id' => 1581,
                'lastName' => 'Dutta',
                'foreName' => 'Ashok Kumar',
                'initials' => 'AK'
            ]
        ];
        $this->assertEquals(6, count($authors));
        foreach($authors as $author) {
            $data = current($expected);
            $this->assertEquals($data['id'], $author->getId());
            $this->assertEquals($data['lastName'], $author->getLastName());
            $this->assertEquals($data['foreName'], $author->getForeName());
            $this->assertEquals($data['initials'], $author->getInitials());
            next($expected);
        }
    }

    public function testFetchByNonExistingArticleId()
    {
        $authors = $this->table->fetchAuthorsByArticleId(2400);
        $this->assertEmpty($authors);
    }

    public function testFetchByExistingArticleIds()
    {
        $authors = $this->table->fetchAuthorsByArticleIds([243, 246, 248]);
        $expected = [
            243 => [
                [
                    'id' => 1576,
                    'lastName' => 'Bhatnagar',
                    'foreName' => 'Shishir Kumar',
                    'initials' => 'SK'
                ], [
                    'id' => 1577,
                    'lastName' => 'Chandra',
                    'foreName' => 'Jagdish',
                    'initials' => 'J'
                ], [
                    'id' => 1578,
                    'lastName' => 'Narayan',
                    'foreName' => 'Shashi',
                    'initials' => 'S'
                ], [
                    'id' => 1579,
                    'lastName' => 'Sharma',
                    'foreName' => 'Sunita',
                    'initials' => 'S'
                ], [
                    'id' => 1580,
                    'lastName' => 'Singh',
                    'foreName' => 'Varinder',
                    'initials' => 'V'
                ], [
                    'id' => 1581,
                    'lastName' => 'Dutta',
                    'foreName' => 'Ashok Kumar',
                    'initials' => 'AK'
                ]
            ],
            246 => [
                [
                    'id' => 1593,
                    'lastName' => 'Memon',
                    'foreName' => 'Shazia',
                    'initials' => 'S'
                ], [
                    'id' => 1594,
                    'lastName' => 'Shaikh',
                    'foreName' => 'Salma',
                    'initials' => 'S'
                ], [
                    'id' => 1595,
                    'lastName' => 'Nizamani',
                    'foreName' => 'M Akbar A',
                    'initials' => 'MA'
                ]
            ],
            248 => [
                [
                    'id' => 1601,
                    'lastName' => 'Garewal',
                    'foreName' => 'G',
                    'initials' => 'G'
                ], [
                    'id' => 1602,
                    'lastName' => 'Marwaha',
                    'foreName' => 'N',
                    'initials' => 'N'
                ], [
                    'id' => 1603,
                    'lastName' => 'Marwaha',
                    'foreName' => 'R K',
                    'initials' => 'RK'
                ], [
                    'id' => 1604,
                    'lastName' => 'Das',
                    'foreName' => 'K C',
                    'initials' => 'KC'
                ]
            ]
        ];
        foreach($authors as $articleId => $a) {
            $expectedId = key($expected);
            $expectedAuthors = current($expected);
            $this->assertEquals($expectedId, $articleId);
            foreach($a as $author) {
                $expectedAuthor = current($expectedAuthors);
                $this->assertEquals($expectedAuthor['id'], $author->getId());
                $this->assertEquals($expectedAuthor['lastName'], $author->getLastName());
                $this->assertEquals($expectedAuthor['foreName'], $author->getForeName());
                $this->assertEquals($expectedAuthor['initials'], $author->getInitials());
                next($expectedAuthors);
            }
            next($expected);
        }
    }

    public function testFetchByExistingAndNonExistingArticleIds()
    {
        $authors = $this->table->fetchAuthorsByArticleIds([243, 246, 247, 248, 250]);
        $expected = [
            243 => [
                [
                    'id' => 1576,
                    'lastName' => 'Bhatnagar',
                    'foreName' => 'Shishir Kumar',
                    'initials' => 'SK'
                ], [
                    'id' => 1577,
                    'lastName' => 'Chandra',
                    'foreName' => 'Jagdish',
                    'initials' => 'J'
                ], [
                    'id' => 1578,
                    'lastName' => 'Narayan',
                    'foreName' => 'Shashi',
                    'initials' => 'S'
                ], [
                    'id' => 1579,
                    'lastName' => 'Sharma',
                    'foreName' => 'Sunita',
                    'initials' => 'S'
                ], [
                    'id' => 1580,
                    'lastName' => 'Singh',
                    'foreName' => 'Varinder',
                    'initials' => 'V'
                ], [
                    'id' => 1581,
                    'lastName' => 'Dutta',
                    'foreName' => 'Ashok Kumar',
                    'initials' => 'AK'
                ]
            ],
            246 => [
                [
                    'id' => 1593,
                    'lastName' => 'Memon',
                    'foreName' => 'Shazia',
                    'initials' => 'S'
                ], [
                    'id' => 1594,
                    'lastName' => 'Shaikh',
                    'foreName' => 'Salma',
                    'initials' => 'S'
                ], [
                    'id' => 1595,
                    'lastName' => 'Nizamani',
                    'foreName' => 'M Akbar A',
                    'initials' => 'MA'
                ]
            ],
            247 => [],
            248 => [
                [
                    'id' => 1601,
                    'lastName' => 'Garewal',
                    'foreName' => 'G',
                    'initials' => 'G'
                ], [
                    'id' => 1602,
                    'lastName' => 'Marwaha',
                    'foreName' => 'N',
                    'initials' => 'N'
                ], [
                    'id' => 1603,
                    'lastName' => 'Marwaha',
                    'foreName' => 'R K',
                    'initials' => 'RK'
                ], [
                    'id' => 1604,
                    'lastName' => 'Das',
                    'foreName' => 'K C',
                    'initials' => 'KC'
                ]
            ],
            250 => []
        ];
        foreach($authors as $articleId => $a) {
            $expectedId = key($expected);
            $expectedAuthors = current($expected);
            $this->assertEquals($expectedId, $articleId);
            if(empty($a)) {
                $this->assertEquals($expectedAuthors, $a);
            } else {
                foreach($a as $author) {
                    $expectedAuthor = current($expectedAuthors);
                    $this->assertEquals($expectedAuthor['id'], $author->getId());
                    $this->assertEquals($expectedAuthor['lastName'], $author->getLastName());
                    $this->assertEquals($expectedAuthor['foreName'], $author->getForeName());
                    $this->assertEquals($expectedAuthor['initials'], $author->getInitials());
                    next($expectedAuthors);
                }
            }
            next($expected);
        }
    }

    public function testFetchByNonExistingArticleIds()
    {
        $authors = $this->table->fetchAuthorsByArticleIds([2, 3, 4]);
        $expected = [
            2 => [],
            3 => [],
            4 => []
        ];
        $this->assertEquals($expected, $authors);
    }

    public function testCreateAuthors()
    {
        $length = $this->getConnection()->getRowCount('article_authors');
        $authorData = [
            [
                'lastName' => 'Gopinathan',
                'firstName' => 'Vikram Raj',
                'initials' => 'VR'
            ], [
                'lastName' => 'Vikram',
                'firstName' => 'Kirthika',
                'initials' => 'K'
            ], [
                'lastName' => 'Gopinathan',
                'firstName' => 'Nirmal Raj',
                'initials' => 'NR'
            ]
        ];
        $authors = [];
        foreach($authorData as $data) {
            $author = new Author();
            $author->setForeName($data['firstName']);
            $author->setLastName($data['lastName']);
            $author->setInitials($data['initials']);
            $authors[] = $author;
        }

        $this->table->createAuthors($authors, 300);
        $this->assertEquals($length + 3, $this->getConnection()->getRowCount('article_authors'));

        $conn = $this->getConnection();
        $statement = $conn->getConnection()->prepare('SELECT * FROM article_authors WHERE article_id = :id');
        $statement->execute(array(':id' => 300));
        $authors = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertCount(3, $authors);

        $i = 0;
        foreach($authors as $author) {
            $expected = $authorData[$i];
            $this->assertEquals($i + 1, $author['position']);
            $this->assertEquals($expected['lastName'], $author['last_name']);
            $this->assertEquals($expected['firstName'], $author['fore_name']);
            $this->assertEquals($expected['initials'], $author['initials']);
            $this->assertTrue($author['id'] > 0);
            ++$i;
        }
    }

    public function testCreatingAuthorsWithEmptyArray()
    {
        $length = $this->getConnection()->getRowCount('article_authors');
        $authors = [];
        $this->table->createAuthors($authors, 300);
        $this->assertEquals($length, $this->getConnection()->getRowCount('article_authors'));
        $actual = $this->table->fetchAuthorsByArticleId(300);
        $this->assertEmpty($actual);
    }

    /**
     * @expectedException \Zend\Db\Adapter\Exception\InvalidQueryException
     */
    public function testCreatingAuthorsWithInvalidConstraint()
    {
        $length = $this->getConnection()->getRowCount('article_authors');
        $authorData = [
            [
                'lastName' => 'Gopinathan',
                'firstName' => 'Vikram Raj',
                'initials' => 'VR'
            ], [
                'lastName' => 'Vikram',
                'firstName' => 'Kirthika',
                'initials' => 'K'
            ], [
                'lastName' => 'Gopinathan',
                'firstName' => 'Nirmal Raj',
                'initials' => 'NR'
            ]
        ];
        $authors = [];
        foreach($authorData as $data) {
            $author = new Author();
            $author->setForeName($data['firstName']);
            $author->setLastName($data['lastName']);
            $author->setInitials($data['initials']);
            $authors[] = $author;
        }

        $this->table->createAuthors($authors, 1000);
        $this->assertEquals($length, $this->getConnection()->getRowCount('article_authors'));
        $actual = $this->table->fetchAuthorsByArticleId(1000);
        $this->assertEmpty($actual);
    }

    public function testDeleteAuthors()
    {
        $length = $this->getConnection()->getRowCount('article_authors');
        $authors = $this->table->fetchAuthorsByArticleId(243);
        $this->assertCount(6, $authors);
        $this->table->deleteAuthorsByArticleId(243);
        $authors = $this->table->fetchAuthorsByArticleId(243);
        $this->assertEmpty($authors);
        $this->assertEquals($length - 6, $this->getConnection()->getRowCount('article_authors'));
    }

    public function testDeleteAuthorsOfNonExistingArticleId()
    {
        $length = $this->getConnection()->getRowCount('article_authors');
        $this->table->deleteAuthorsByArticleId(300);
        $this->assertEquals($length, $this->getConnection()->getRowCount('article_authors'));
    }
}