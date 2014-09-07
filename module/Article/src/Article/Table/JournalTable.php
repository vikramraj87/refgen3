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

    /**
     * Fetches journal by issn
     *
     * @param string $issn
     * @return Journal|null
     */
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

    /**
     * Checks for the existence of the journal by issn. If exists, returns it.
     * Else, creates a new one and returns it
     *
     * @param Journal $journal
     * @return bool
     */
    public function checkJournal(Journal &$journal)
    {
        $data = $this->fetchJournalByIssn($journal->getIssn());
        if($data) {
            $journal = $data;
            return true;
        }

        $this->insert($journal->toArray());
        $id = $this->getLastInsertValue();
        $journal->setId($id);

        return $journal;
    }

    /**
     * Fetches multiple journals for array of journal ids
     *
     * @param array $ids
     * @return array
     */
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
