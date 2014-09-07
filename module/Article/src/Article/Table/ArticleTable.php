<?php
namespace Article\Table;

use Article\Entity\Article;
use Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\AdapterAwareInterface,
    Zend\Db\TableGateway\AbstractTableGateway,
    Zend\Db\Sql\Where,
    Zend\Db\Sql\Expression,
    Zend\Db\ResultSet\ResultSet;

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

    /**
     * Fetch article by id
     *
     * @param int $articleId
     * @return Article|null
     */
    public function fetchArticleById($articleId = 0)
    {
        $row = $this->select(array('id' => (int) $articleId))->current();
        if($row === false) {
            return null;
        }
        return $this->getArticleFromData($row->getArrayCopy());
    }

    /**
     * Fetch article by indexer id
     *
     * @param string $indexerId
     * @return Article|null
     */
    public function fetchArticleByIndexerId($indexerId = '')
    {
        $row = $this->select(array('indexer_id' => $indexerId))->current();
        if($row === false) {
            return null;
        }
        return $this->getArticleFromData($row->getArrayCopy());
    }

    /**
     * Fetches the article if exists else creates a new
     * entry
     *
     * @param Article $article
     * @return Article|null
     */
    public function checkArticle(Article $article)
    {
        $savedArticle = $this->fetchArticleByIndexerId($article->getIndexerId());
        if(null !== $savedArticle) {
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
     * if exists
     *
     * @param array $indexerIds
     * @return array
     */
    public function checkArticles(array $indexerIds = array())
    {
        if(empty($indexerIds)) {
            return array();
        }
        $where = new Where();
        $where->in('indexer_id', $indexerIds);
        $rowset = $this->select($where);

        return $this->articlesFromRowset($rowset);
    }

    /**
     * @param array $ids
     * @return array
     */
    public function fetchArticlesByIds($ids = array())
    {
        if(empty($ids)) {
            return array();
        }
        $where = new Where();
        $where->in('id', $ids);
        $rowset = $this->select($where);

        $tmp = $this->articlesFromRowset($rowset);
        $idMap  = array();
        foreach($tmp as $article) {
            /** @var \Article\Entity\Article $article */
            $idMap[$article->getId()] = $article->getIndexerId();
        }
        $articles = array();
        foreach($ids as $id) {
            if(array_key_exists($id, $idMap)) {
                $articleIndexerId = $idMap[$id];
                $article = $tmp[$articleIndexerId];
                $articles[$article->getIndexerId()] = $article;
            }
        }
        return $articles;
    }

    public function getTotalCount()
    {
        $select = $this->getSql()
            ->select()
            ->columns(array('count' => new Expression('COUNT(*)')));
        $row = $this->selectWith($select)->current();
        return $row['count'];
    }

    /**
     * @param array $data
     * @return Article|null
     */
    private function getArticleFromData(array $data = array())
    {
        $article = Article::createFromArray($data);

        $journalTable = $this->journalTable;
        $journal = $journalTable->fetchJournalById($data['journal_id']);
        if($journal === null) {
            /**
             * todo: delete the article data as the data is incomplete
             */
            return null;
        }
        $article->setJournal($journal);

        $authors = $this->authorTable->fetchAuthorsByArticleId($article->getId());
        $article->setAuthors($authors);

        $abstract = $this->abstractParaTable->fetchParasByArticleId($article->getId());
        $article->setAbstract($abstract);

        return $article;
    }

    /**
     * Saves the article to the database.
     *
     * @param Article $article
     * @return bool
     */
    private function saveArticle(Article $article)
    {
        $journal = $article->getJournal();
        $result = $this->journalTable->checkJournal($journal);
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
        $result = $this->authorTable->createAuthors($authors, $articleId);
        if($result === false) {
            $this->deleteArticle($articleId);
            return false;
        }
        $abstract = $article->getAbstract();
        $result = $this->abstractParaTable->createAbstract($abstract, $articleId);
        if($result === false) {
            $this->deleteArticle($articleId);
            return false;
        }
        return true;
    }

    /**
     * Delete an article and all the dependent data.
     *
     * @param int $id
     * @return bool
     */
    private function deleteArticle($id = 0)
    {
        $id = (int) $id;
        $this->authorTable->deleteAuthorsByArticleId($id);
        $this->abstractParaTable->deleteParasByArticleId($id);
        $this->delete(array('id' => $id));
        return true;
    }

    /**
     * Generates article from rowset data
     *
     * @param ResultSet $rowset
     * @return array
     */
    private function articlesFromRowset(ResultSet $rowset)
    {
        if(count($rowset) === 0) {
            return array();
        }

        $articles = array();
        $journalIds = array();
        $articleIds = array();

        /** @var array() $journalMap associative array to map article id to journal id */
        $journalMap = array();
        foreach($rowset as $row) {
            /** @var \ArrayObject $row */

            $article = Article::createFromArray($row->getArrayCopy());
            $articles[$article->getIndexerId()] = $article;
            $journalIds[] = $row['journal_id'];
            $articleIds[] = $row['id'];
            $journalMap[$row['id']] = $row['journal_id'];
        }
        $journalIds = array_unique($journalIds);
        $journals   = $this->journalTable->fetchJournalsByIds($journalIds);
        $authors    = $this->authorTable->fetchAuthorsByArticleIds($articleIds);
        $abstracts  = $this->abstractParaTable->fetchParasByAArticleIds($articleIds);

        foreach($articles as &$article) {
            /** @var Article $article */
            $article->setJournal($journals[$journalMap[$article->getId()]]);

            if(isset($authors[$article->getId()])) {
                $article->setAuthors(array_values($authors[$article->getId()]));
            }

            if(isset($abstracts[$article->getId()])) {
                $article->setAbstract(array_values($abstracts[$article->getId()]));
            }
        }
        return $articles;
    }

    /**
     * @param \Article\Table\AbstractParaTable $abstractParaTable
     */
    public function setAbstractParaTable(AbstractParaTable $abstractParaTable)
    {
        $this->abstractParaTable = $abstractParaTable;
    }

    /**
     * @param \Article\Table\AuthorTable $authorTable
     */
    public function setAuthorTable(AuthorTable $authorTable)
    {
        $this->authorTable = $authorTable;
    }

    /**
     * @param \Article\Table\JournalTable $journalTable
     */
    public function setJournalTable(JournalTable $journalTable)
    {
        $this->journalTable = $journalTable;
    }
}