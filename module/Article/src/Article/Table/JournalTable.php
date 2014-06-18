<?php
namespace Article\Table;

use Article\Entity\Journal;
use Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\AdapterAwareInterface,
    Zend\Db\TableGateway\AbstractTableGateway,
    Zend\Db\Sql\Where;

Class JournalTable extends AbstractTableGateway implements AdapterAwareInterface
{
    protected $table = 'journals';

    /**
     * Set db adapter
     *
     * @param Adapter $adapter
     * @return AdapterAwareInterface
     */
    public function setDbAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    /**
     * Fetches journal by id
     *
     * @param int $journalId
     * @return Journal|null null if no journal found
     */
    public function fetchJournalById($journalId = 0)
    {
        $journalId = (int) $journalId;
        $rowset    = $this->select(array(
            'id' => $journalId
        ));
        $row = $rowset->current();

        if($row === false) {
            return null;
        }
        $data = $row->getArrayCopy();
        return Journal::createFromArray($data);
    }

    public function fetchJournalByIssn($issn = '')
    {
        $rowset = $this->select(array('issn' => $issn));
        $row    = $rowset->current();
        if($row === false) {
            return null;
        }
        $data = $row->getArrayCopy();
        return Journal::createFromArray($data);
    }

    public function checkJournal(Journal &$journal)
    {
        $data = $this->fetchJournalByIssn($journal->getIssn());
        if($data) {
            $journal = $data;
            return true;
        }
        $this->insert($journal->toArray());
        return $this->checkJournal($journal);
    }

    public function fetchJournalsByIds(array $ids = array())
    {
        $where = new Where();
        $where->in('id', $ids);

        $rowset = $this->select($where);
        $journals = array();
        foreach($rowset as $row) {
            $journal = Journal::createFromArray($row->getArrayCopy());
            $journals[$journal->getId()] = $journal;
        }
        return $journals;
    }
}
