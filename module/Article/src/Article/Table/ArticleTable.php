<?php
namespace Article\Table;

use Article\Entity\Article;
use Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\AdapterAwareInterface,
    Zend\Db\TableGateway\AbstractTableGateway,
    Zend\Db\Sql\Where;

class ArticleTable extends AbstractTableGateway implements AdapterAwareInterface
{
    protected $table = 'articles';

    /** @var JournalTable */
    private $journalTable;

    /** @var AuthorTable */
    private $authorTable;

    /** @var AbstractParaTable */
    private $abstractParaTable;

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

    public function fetchArticleById($articleId = 0)
    {
        $articleId = (int) $articleId;
        $rowset = $this->select(array('id' => $articleId));

        $row = $rowset->current();
        if($row === false) {
            return null;
        }

        $data = $row->getArrayCopy();
        return $this->getArticleFromData($data);
    }

    public function fetchArticleByIndexerId($indexerId = '')
    {
        $row = $this->select(array('indexer_id' => $indexerId))->current();
        if($row === false) {
            return null;
        }
        $data = $row->getArrayCopy();
        return $this->getArticleFromData($data);
    }

    /**
     * @param Article $article
     * @return Article|null
     */
    public function checkArticle(Article $article)
    {
        $savedArticle = $this->fetchArticleByIndexerId($article->getIndexerId());
        if(null != $savedArticle) {
            return $savedArticle;
        }
        $result = $this->saveArticle($article);
        if(false == $result) {
            return null;
        }
        return $this->checkArticle($article);
    }

    /**
     * Returns the articles with ids provided in the array
     *
     * @param array $indexerIds
     * @return array
     */
    public function checkArticles(array $indexerIds = array())
    {
        $where = new Where();
        $where->in('indexer_id', $indexerIds);
        $rowset = $this->select($where);
        $articles = array();

        $journalIds = array();
        $articleIds = array();

        $map = array();
        foreach($rowset as $row) {
            $article = new Article();
            $article->populateFromArray($row->getArrayCopy());
            $articles[$article->getIndexerId()] = $article;
            $journalIds[] = $row['journal_id'];
            $articleIds[] = $row['id'];
            $map[$row['id']] = $row['journal_id'];
        }
        $journalIds = array_unique($journalIds);
        $journals   = $this->getJournalTable()->fetchJournalsByIds($journalIds);
        $authors    = $this->getAuthorTable()->fetchAuthorsByArticleIds($articleIds);
        $abstracts  = $this->getAbstractParaTable()->fetchParasByAArticleIds($articleIds);

        foreach($articles as &$article) {
            /** @var Article $article */

            $article->setJournal($journals[$map[$article->getId()]]);
            if(isset($authors[$article->getId()])) {
                $article->setAuthors(array_values($authors[$article->getId()]));
            } else {
                $article->setAuthors(array());
            }
            if(isset($abstracts[$article->getId()])) {
                $article->setAbstract(array_values($abstracts[$article->getId()]));
            } else {
                $article->setAbstract(array());
            }
        }

        return $articles;
    }

    /**
     * Lazy loading of journal table
     *
     * @return JournalTable
     */
    private function getJournalTable()
    {
        if($this->journalTable == null) {
            $this->journalTable = new JournalTable();
            $this->journalTable->setDbAdapter($this->adapter);
        }
        return $this->journalTable;
    }

    /**
     * Lazy loading of author table
     *
     * @return AuthorTable
     */
    private function getAuthorTable()
    {
        if($this->authorTable == null) {
            $this->authorTable = new AuthorTable();
            $this->authorTable->setDbAdapter($this->adapter);
        }
        return $this->authorTable;
    }

    /**
     * Lazy loading of AbstractParaTable
     *
     * @return AbstractParaTable
     */
    private function getAbstractParaTable()
    {
        if($this->abstractParaTable == null) {
            $this->abstractParaTable = new AbstractParaTable();
            $this->abstractParaTable->setDbAdapter($this->adapter);
        }
        return $this->abstractParaTable;
    }

    /**
     * @param array $data
     * @return Article|null
     */
    private function getArticleFromData(array $data = array())
    {
        $article = new Article();
        $article->populateFromArray($data);

        $journalTable = $this->getJournalTable();
        $journal = $journalTable->fetchJournalById($data['journal_id']);
        if($journal === null) {
            /**
             * todo: delete the article data as the data is incomplete
             */
            return null;
        }
        $article->setJournal($journal);

        $authorTable = $this->getAuthorTable();
        $authors = $authorTable->fetchAuthorsByArticleId($article->getId());
        $article->setAuthors($authors);

        $abstractParaTable = $this->getAbstractParaTable();
        $abstract = $abstractParaTable->fetchParasByArticleId($article->getId());
        $article->setAbstract($abstract);

        return $article;
    }

    /**
     * Saves the article to the database. todo: Raise an event in case of failure
     *
     * @param Article $article
     * @return bool
     */
    private function saveArticle(Article $article)
    {
        $journal = $article->getJournal();
        $result = $this->getJournalTable()->checkJournal($journal);
        if($result === false) {
            return false;
        }
        $data = array(
            'indexer_id' => $article->getIndexerId(),
            'volume'     => $article->getJournalIssue()->getVolume(),
            'issue'      => $article->getJournalIssue()->getIssue(),
            'pages'      => $article->getJournalIssue()->getPages(),
            'year'       => $article->getJournalIssue()->getPubDate()->getYear(),
            'month'      => $article->getJournalIssue()->getPubDate()->getMonth(),
            'day'        => $article->getJournalIssue()->getPubDate()->getDay(),
            'pub_status' => $article->getJournalIssue()->getPubStatus(),
            'journal_id' => $journal->getId(),
            'title'      => $article->getTitle()
        );
        $numRowsAffected = $this->insert($data);
        if(!$numRowsAffected) {
            return false;
        }
        $articleId = $this->getLastInsertValue();
        $authors = $article->getAuthors();
        $result = $this->getAuthorTable()->createAuthors($authors, $articleId);
        if($result === false) {
            /**
             * todo: raise an event
             */
            $this->deleteArticle($articleId);
            return false;
        }

        $abstract = $article->getAbstract();
        $result = $this->getAbstractParaTable()->createAbstract($abstract, $articleId);
        if($result === false) {
            /**
             * todo: raise an event
             */
            $this->deleteArticle($articleId);
            return false;
        }

        return true;
    }

    /**
     * Delete an article and all the dependent data. [Tested]
     *
     * @param int $id
     * @return bool
     */
    private function deleteArticle($id = 0)
    {
        $id = (int) $id;
        $this->getAuthorTable()->deleteAuthorsByArticleId($id);
        $this->getAbstractParaTable()->deleteParasByArticleId($id);
        $this->delete(array('id' => $id));
        return true;
    }
} 