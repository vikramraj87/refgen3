<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 25/05/14
 * Time: 5:07 PM
 */

namespace Article\Entity;

class Article
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
    protected $abstract;

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

    public function toArray()
    {
        $journalData = array(
            'journalTitle' => $this->journal->getTitle(),
            'journalAbbr'  => $this->journal->getAbbr(),
            'journalIssn'  => $this->journal->getIssn()
        );
        return array_merge(array(
                'id'        => $this->id,
                'indexerId' => $this->indexerId,
                'title'     => $this->title,
                'abstract'  => $this->abstract,
                'authors'   => $this->authors
            ),
            $journalData,
            $this->journalIssue->toArray()
        );
    }

    /**
     * Helper function to populate the data from database
     *
     * @param array $data
     */
    public function populateFromArray(array $data = array())
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

        $this->id           = $id;
        $this->indexerId    = $indexerId;
        $this->title        = $title;
        $this->journalIssue = $journalIssue;
    }

    /**
     * Creates Article object from data array
     *
     * @param array $data
     * @return Article
     */
    public static function createArticleFromArray(array $data = array())
    {
        $pubDateData = $data['journal_issue']['pub_date'];
        $pubDate = new PubDate();
        $pubDate->setDay($pubDateData['day']);
        $pubDate->setMonth($pubDateData['month']);
        $pubDate->setYear($pubDateData['year']);

        $journalIssueData = $data['journal_issue'];
        $journalIssue = new JournalIssue();
        $journalIssue->setIssue($journalIssueData['issue']);
        $journalIssue->setVolume($journalIssueData['volume']);
        $journalIssue->setPages($journalIssueData['pages']);
        $journalIssue->setPubStatus($journalIssueData['pub_status']);
        $journalIssue->setPubDate($pubDate);

        $journalData = $data['journal'];
        $journal = Journal::createFromArray($journalData);

        $abstract = array();
        foreach($data['abstract'] as $para) {
            $abstract[] = AbstractPara::createFromArray($para);
        }

        $authors = array();
        foreach($data['authors'] as $author) {
            $authors[] = Author::createFromArray($author);
        }

        $article = new self();
        $article->setTitle($data['title']);
        $article->setIndexerId($data['indexer_id']);
        $article->setAbstract($abstract);
        $article->setAuthors($authors);
        $article->setJournal($journal);
        $article->setJournalIssue($journalIssue);

        return $article;
    }
}