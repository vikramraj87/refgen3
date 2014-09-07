<?php
namespace Application\Adapter;

use Zend\Paginator\Adapter\AdapterInterface;

class IndeterminateCountAdapter implements AdapterInterface
{
    /** @var int */
    private $count = 0;

    /** @var array */
    private $items = array();

    public function __construct(array $items = array(), $count)
    {
        $this->items = $items;
        $this->count = $count;
    }

    /**
     * Returns a collection of items for a page.
     *
     * @param  int $offset Page offset
     * @param  int $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        return $this->items;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return $this->count;
    }

} 