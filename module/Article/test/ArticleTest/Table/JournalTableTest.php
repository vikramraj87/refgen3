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
        $expected = new Journal();
        $expected->setId(1);
        $expected->setIssn('1526-0976');
        $expected->setAbbr('J Low Genit Tract Dis');
        $expected->setTitle('Journal of lower genital tract disease');

        $result = $this->table->fetchJournalById(1);
        $this->assertEquals($expected, $result);

        $expected2 = new Journal();
        $expected2->setId(2);
        $expected2->setIssn('1752-8062');
        $expected2->setAbbr('Clin Transl Sci');
        $expected2->setTitle('Clinical and translational science');

        $result2 = $this->table->fetchJournalById(2);
        $this->assertEquals($expected2, $result2);
    }

    public function testFetchByIdWithNonExistentId()
    {
        $this->assertEquals(null, $this->table->fetchJournalById(1001));
    }

    public function testFetchByExistingIssn()
    {
        $journal = new Journal();
        $journal->setId(3);
        $journal->setIssn('1879-355X');
        $journal->setTitle('International journal of radiation oncology, biology, physics');
        $journal->setAbbr('Int J Radiat Oncol Biol Phys');

        $this->assertEquals($journal, $this->table->fetchJournalByIssn('1879-355X'));
    }

    public function testFetchByNonExistingIssn()
    {
        $this->assertEquals(null, $this->table->fetchJournalByIssn('xxxx-yyyy'));
    }

    public function testCheckJournalWithExistingId()
    {
        $journal = new Journal();
        $journal->setIssn('1879-355X');
        $journal->setTitle('International journal of radiation oncology, biology, physics');
        $journal->setAbbr('Int J Radiat Oncol Biol Phys');

        $this->table->checkJournal($journal);
        $this->assertEquals(3, $journal->getId());
    }

    public function testCheckJournalWithNonExistingId()
    {
        $rowCount = $this->getConnection()->getRowCount('journals');
        $journal = new Journal();
        $journal->setAbbr('J Biomed Nanotechnol');
        $journal->setIssn('1550-7033');
        $journal->setTitle('Journal of biomedical nanotechnology');

        $this->table->checkJournal($journal);
        $this->assertEquals($rowCount + 1, $journal->getId());
    }

} 