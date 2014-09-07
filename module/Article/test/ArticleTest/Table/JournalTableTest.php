<?php
namespace ArticleTest\Table;

use ArticleTest\DbTestCase;
use Article\Table\JournalTable;
use Article\Entity\Journal;

class JournalTableTest extends DbTestCase
{
    /** @var \Article\Table\JournalTable */
    protected $table;

    protected function setUp()
    {
        $conn = $this->getConnection();
        $conn->getConnection()->query('set foreign_key_checks=0');
        $adapter = $this->getAdapter();
        $this->table = new JournalTable();
        $this->table->setDbAdapter($adapter);
        parent::setUp();
        $conn->getConnection()->query('set foreign_key_checks=1');
    }

    public function testFetchById()
    {
        $journal = $this->table->fetchJournalById(194);
        $this->assertEquals(194, $journal->getId());
        $this->assertEquals('J Trop Pediatr', $journal->getAbbr());
        $this->assertEquals('Journal of tropical pediatrics', $journal->getTitle());
        $this->assertEquals('0142-6338', $journal->getIssn());
    }

    public function testFetchByIds()
    {
        $journals = $this->table->fetchJournalsByIds([1,2,197,198,199]);
        $expected = [1,197,199];
        foreach($journals as $id => $journal) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $id);
            $this->assertEquals($this->table->fetchJournalById($expectedId), $journal);
            next($expected);
        }
    }

    public function testFetchByNonExistingIds()
    {
        $journals = $this->table->fetchJournalsByIds([2,3,4,5]);
        $this->assertEmpty($journals);
    }

    public function testFetchByNonExistingId()
    {
        $this->assertNull($this->table->fetchJournalById(1234));
    }

    public function testFetchByIssn()
    {
        $journal = $this->table->fetchJournalByIssn('1022-386X');
        $this->assertEquals(197, $journal->getId());
        $this->assertEquals('1022-386X', $journal->getIssn());
        $this->assertEquals('J Coll Physicians Surg Pak', $journal->getAbbr());
        $this->assertEquals('Journal of the College of Physicians and Surgeons--Pakistan : JCPSP', $journal->getTitle());
    }

    public function testFetchByNonExistingIssn()
    {
        $this->assertNull($this->table->fetchJournalByIssn('1234-5678'));
    }

    public function testCheckJournalWithExistingIssn()
    {
        $rowCount = $this->getConnection()->getRowCount('journals');

        $journal = new Journal();
        $journal->setIssn('0019-6061');
        $journal->setTitle('Indian pediatrics');
        $journal->setAbbr('Indian Pediatr');

        $this->table->checkJournal($journal);
        $this->assertEquals($rowCount, $this->getConnection()->getRowCount('journals'));
        $this->assertEquals(199, $journal->getId());
    }

    public function testCheckingJournalWithNonExistingIssn()
    {
        $rowCount = $this->getConnection()->getRowCount('journals');

        $journal = new Journal();
        $journal->setIssn('1234-5678');
        $journal->setAbbr('Ind J Cyt');
        $journal->setTitle('Indian Journal of Cytology');

        $this->table->checkJournal($journal);
        $this->assertEquals($rowCount + 1, $this->getConnection()->getRowCount('journals'));
        $this->assertEquals(243, $journal->getId());
    }
}