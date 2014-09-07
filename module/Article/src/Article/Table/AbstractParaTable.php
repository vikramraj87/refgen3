<?php
namespace Article\Table;

use Article\Entity\AbstractPara;
use Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\AdapterAwareInterface,
    Zend\Db\TableGateway\AbstractTableGateway,
    Zend\Db\Adapter\Exception\InvalidQueryException,
    Zend\Db\Sql\Where;

class AbstractParaTable extends AbstractTableGateway implements AdapterAwareInterface
{
    protected $table = 'article_abstract_paras';

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
     * Fetches the abstract paras of a particular article
     *
     * @param int $articleId
     * @return AbstractPara[]
     */
    public function fetchParasByArticleId($articleId = 0)
    {
        $articleId = (int) $articleId;
        $select = $this->sql
                       ->select()
                       ->where(array('article_id' => $articleId))
                       ->order('position');
        $rowset = $this->selectWith($select);

        $abstract = array();
        foreach($rowset as $row) {
            $abstract[] = AbstractPara::createFromArray($row->getArrayCopy());
        }
        return $abstract;
    }

    /**
     * Fetches the abstract paras of multiple articles
     *
     * @param array $articleIds
     * @return array
     */
    public function fetchParasByAArticleIds(array $articleIds = array())
    {
        $where = new Where();
        $where->in('article_id', $articleIds);
        $rowset = $this->select($where);

        $paras = array();
        foreach($rowset as $row) {
            $para = AbstractPara::createFromArray($row->getArrayCopy());
            $paras[$row['article_id']][$row['position'] - 1] = $para;
        }
        return $paras;
    }

    /**
     * Create abstract para entries for a particular article
     *
     * @param array $paras
     * @param int $articleId
     * @return bool
     */
    public function createAbstract(array $paras = array(), $articleId = 0)
    {
        $articleId = (int) $articleId;
        $position  = 1;
        foreach($paras as $para) {
            /** @var \Article\Entity\AbstractPara $para */

            $data = $para->toArray();
            $data['position'] = $position;
            $data['article_id'] = $articleId;
            $this->insert($data);
            $position++;
        }
        return true;
    }

    /**
     * Deletes abstract paras of a particular article
     *
     * @param int $articleId
     * @return bool
     */
    public function deleteParasByArticleId($articleId = 0)
    {
        $articleId = (int) $articleId;
        $this->delete(array('article_id' => $articleId));
        return true;
    }
} 