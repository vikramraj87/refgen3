<?php
namespace Article\Entity;
use Collection\Entity\Collection\ItemInterface;

class Article implements ItemInterface
{
    /** @var int  */
    protected $id = 0;

    /** @var string */
    protected $indexerId = '';

    /** @var JournalIssue */
    protected $journalIssue;

    /** @var Journal  */
    protected $journal = null;

    /** @var string  */
    protected $title = '';

    /** @var AbstractPara[] */
    protected $abstract = array();

    /** @var Author[] */
    protected $authors = array();

    /**
     * @param \Article\Entity\AbstractPara[] $abstract
     */
    public function setAbstract(array $abstract = array())
    {
        $this->abstract = $abstract;
    }

    /**
     * @return \Article\Entity\AbstractPara[]
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * @param \Article\Entity\Author[] $authors
     */
    public function setAuthors(array $authors = array())
    {
        $this->authors = $authors;
    }

    /**
     * @return \Article\Entity\Author[]
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $indexerId
     */
    public function setIndexerId($indexerId)
    {
        $this->indexerId = $indexerId;
    }

    /**
     * @return string
     */
    public function getIndexerId()
    {
        return $this->indexerId;
    }

    /**
     * @param \Article\Entity\Journal $journal
     */
    public function setJournal(Journal $journal)
    {
        $this->journal = $journal;
    }

    /**
     * @return \Article\Entity\Journal
     */
    public function getJournal()
    {
        return $this->journal;
    }

    /**
     * @param JournalIssue $journalIssue
     */
    public function setJournalIssue(JournalIssue $journalIssue)
    {
        $this->journalIssue = $journalIssue;
    }

    /**
     * @return JournalIssue
     */
    public function getJournalIssue()
    {
        return $this->journalIssue;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Factory function to create article from data
     *
     * @param array $data
     * @return Article
     */
    public static function createFromArray(array $data = array())
    {
        $id        = isset($data['id'])         ? (int) $data['id']          : 0;
        $indexerId = isset($data['indexer_id']) ? $data['indexer_id']        : '';
        $volume    = isset($data['volume'])     ? $data['volume']            : '';
        $issue     = isset($data['issue'])      ? $data['issue']             : '';
        $pages     = isset($data['pages'])      ? $data['pages']             : '';
        $year      = isset($data['year'])       ? $data['year']              : '';
        $month     = isset($data['month'])      ? $data['month']             : '';
        $day       = isset($data['day'])        ? $data['day']               : '';
        $title     = isset($data['title'])      ? $data['title']             : '';
        $pubStatus = isset($data['pub_status']) ? (bool) $data['pub_status'] : 0;

        $pubDate = new PubDate();
        $pubDate->setDay($day);
        $pubDate->setMonth($month);
        $pubDate->setYear($year);

        $journalIssue = new JournalIssue();
        $journalIssue->setVolume($volume);
        $journalIssue->setIssue($issue);
        $journalIssue->setPages($pages);
        $journalIssue->setPubStatus($pubStatus);
        $journalIssue->setPubDate($pubDate);

        $article = new self;
        $article->setId($id);
        $article->setIndexerId($indexerId);
        $article->setTitle($title);
        $article->setJournalIssue($journalIssue);

        return $article;
    }
}