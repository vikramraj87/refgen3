<?php
namespace Article\Table;

use Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\AdapterAwareInterface,
    Zend\Db\TableGateway\AbstractTableGateway,
    Zend\Db\Adapter\Exception\InvalidQueryException,
    Zend\Db\Sql\Where;
use Article\Entity\Author;

class AuthorTable extends AbstractTableGateway implements AdapterAwareInterface
{
    protected $table = 'article_authors';

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
     * Returns authors belonging to article id
     *
     * @param int $articleId
     * @return \Article\Entity\Author[]
     */
    public function fetchAuthorsByArticleId($articleId = 0)
    {
        $articleId = (int) $articleId;
        $select = $this->sql
                       ->select()
                       ->where(array('article_id' => $articleId))
                       ->order('position');
        $rowset = $this->selectWith($select);

        $authors = array();
        foreach($rowset as $row) {
            $authors[] = Author::createFromArray($row->getArrayCopy());
        }
        return $authors;
    }

    /**
     * Returns authors of multiple articles as an associative array
     *
     * @param array $articleIds
     * @return array
     */
    public function fetchAuthorsByArticleIds(array $articleIds = array())
    {
        $where = new Where();
        $where->in('article_id', $articleIds);
        $rowset = $this->select($where);

        $authors = array();
        foreach($articleIds as $id) {
            $authors[$id] = [];
        }

        foreach($rowset as $row) {
            $author = Author::createFromArray($row->getArrayCopy());
            $authors[$row['article_id']][$row['position'] - 1] = $author;
        }
        return $authors;
    }

    /**
     * Create authors
     *
     * @param Author[] $authors
     * @param int $articleId
     * @return bool
     */
    public function createAuthors($authors = array(), $articleId = 0)
    {
        $articleId = (int) $articleId;
        $position = 1;
        foreach($authors as $author) {
            /** @var \Article\Entity\Author $author */

            $data = $author->toArray();
            $data['position'] = $position;
            $data['article_id'] = $articleId;
            $this->insert($data);
            $position++;
        }
    }

    /**
     * Deletes authors belonging to an article
     *
     * @param int $articleId
     * @return bool
     */
    public function deleteAuthorsByArticleId($articleId = 0)
    {
        $articleId = (int) $articleId;
        $this->delete(array('article_id' => $articleId));
        return true;
    }
}