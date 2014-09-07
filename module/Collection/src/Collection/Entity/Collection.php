<?php
namespace Collection\Entity;

use DateTime;
use Collection\Entity\Collection\ItemInterface;


class Collection implements \Countable
{
    /** @var int */
    private $id = 0;

    /** @var string */
    private $name = '';

    /** @var DateTime */
    private $createdOn;

    /** @var DateTime */
    private $updatedOn;

    /** @var ItemInterface[] */
    private $items = array();

    /** @var int[] */
    private $position = array();

    /** @var bool */
    private $edited = false;

    /** @var int  */
    private $userId = 0;

    public function __construct(array $items = array(), $id = 0, $name = '', DateTime $createdOn = null, DateTime $updatedOn = null, $edited = false, $userId = 0)
    {
        foreach($items as $item) {
            /** @var ItemInterface $item */
            $this->items[$item->getId()] = $item;
        }
        $this->position = array_keys($this->items);
        $this->id = (int) $id;
        $this->name = $name;
        $this->createdOn = $createdOn;
        $this->updatedOn = $updatedOn;
        $this->edited = (bool) $edited;
        $this->userId = (int) $userId;
    }

    /**
     * @param \DateTime $createdOn
     */
    public function setCreatedOn(\DateTime $createdOn)
    {
        $this->createdOn = $createdOn;
    }


    /**
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = (int) $id;
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        if($name != $this->name) {
            $this->name = $name;
            $this->edited = true;
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param \DateTime $updatedOn
     */
    public function setUpdatedOn($updatedOn)
    {
        $this->updatedOn = $updatedOn;
    }


    /**
     * @return \DateTime
     */
    public function getUpdatedOn()
    {
        return $this->updatedOn;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
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
        return count($this->items);
    }

    /**
     * Returns an associative array of articles with key as article id
     * and value as article
     *
     * @return ItemInterface[]
     */
    public function getItems()
    {
        $items = array();
        foreach($this->position as $id) {
            $items[$id] = $this->items[$id];
        }
        return $items;
    }

    /**
     * Adds an item to the collection
     *
     * @param ItemInterface $item
     * @return bool indicating whether the collection is actually changed
     */
    public function addItem(ItemInterface $item)
    {
        $this->items[$item->getId()] = $item;
        if(in_array($item->getId(), $this->position)) {
            return;
        }
        $this->position[] = $item->getId();
        $this->edited = true;
    }

    /**
     * Removes an article from the collection
     *
     * @param int $id id of the article to be removed
     */
    public function removeItem($id = 0)
    {
        $id = (int) $id;
        if(!isset($this->items[$id])) {
            return;
        }
        unset($this->items[$id]);
        if(in_array($id, $this->position)) {
            $pos = array_keys($this->position, $id)[0];
            array_splice($this->position, $pos, 1);
            $this->edited = true;
        }
    }

    public function removeItems($ids = array())
    {
        foreach($ids as $id) {
            $this->removeItem($id);
        }
    }

    /**
     * Returns whether an article exists in the collection
     *
     * @param int $id
     * @return bool
     */
    public function hasItem($id = 0)
    {
        $id = (int) $id;
        return in_array($id, $this->position);
    }

    /**
     * Moves the selected items up in the collection
     *
     * @param array $items
     */
    public function moveUpItems($items = array())
    {
        /** @var int $start to prevent the items from rearranging if the items are selected in continuity */
        $start = 0;
        // Eg. Moving up [1,2,3] in [1,2,3,100] should be unchanged
        // and should not yield [2,3,1,100]

        foreach($items as $item) {
            if(in_array($item, $this->position)) {
                $item = (int) $item;
                $pos = array_keys($this->position, $item);
                if(!empty($pos) && $pos[0] !== $start) {
                    $rem = array_splice($this->position, $pos[0], 1);
                    array_splice($this->position, $pos[0] - 1, 0, $rem);
                    $this->edited = true;
                }
                $start++;
            }
        }
    }

    /**
     * Moves the selected items down in the collection
     *
     * @param array $items
     */
    public function moveDownItems($items = array())
    {
        /** @var int $end to prevent the items from rearranging if the items are selected in continuity */
        $end = count($this->position) - 1;
        // Eg. Moving down [7,8,9] in [1,2,3,7,8,9] should be unchanged
        // and should not yield [1,2,3,9,7,8]

        $items = array_reverse($items);
        foreach($items as $item) {
            if(in_array($item, $this->position)) {
                $item = (int) $item;
                $pos = array_keys($this->position, $item);
                if(!empty($pos) && $pos[0] < $end) {
                    $rem = array_splice($this->position, $pos[0], 1);
                    array_splice($this->position, $pos[0] + 1, 0, $rem);
                    $this->edited = true;
                }
                $end--;
            }
        }
    }

    /**
     * Serializes the object to array
     *
     * @return array
     */
    public function serialize()
    {
        $itemIds = array();
        foreach($this->position as $id) {
            $itemIds[] = $id;
        }
        return array(
            'id'         => $this->id,
            'name'       => $this->name,
            'createdOn'  => $this->createdOn,
            'updatedOn'  => $this->updatedOn,
            'itemIds'    => implode(',',$itemIds),
            'edited'     => $this->edited
        );
    }

    /**
     * Sorts the collection with the order of ids provided. Useful for
     * changing the order of articles by drag and drop
     *
     * @param array $ids
     */
    public function sort(array $ids = array())
    {
        $tmp = [];
        foreach($ids as $id) {
            if(in_array($id, $this->position)) {
                $tmp[] = $id;
            }
        }
        if($tmp === $this->position) {
            return;
        }
        $this->position = [];
        foreach($tmp as $id) {
            $id = (int) $id;
            $this->position[] = $id;
        }
        $this->edited = true;
    }

    /**
     * Resets the edited status of the collection.
     */
    public function resetEdit()
    {
        $this->edited = false;
    }

    /**
     * Edited status of the collection
     *
     * @return bool
     */
    public function isEdited()
    {
        return $this->edited;
    }
}