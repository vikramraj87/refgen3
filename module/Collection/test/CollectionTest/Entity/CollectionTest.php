<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 26/06/14
 * Time: 11:48 PM
 */

namespace CollectionTest\CollectionTest\Entity;

use PHPUnit_Framework_TestCase;
use Article\Entity\Article;
use Collection\Entity\Collection;
use DateTime;

class CollectionTest extends PHPUnit_Framework_TestCase
{
    private static $sample1;

    /** @var Collection */
    private $collection;

    public static function setUpBeforeClass()
    {
        $ids = array(
            13,12,11,1,2,3,10,9,8,7
        );
        foreach($ids as $id) {
            $article = new Article();
            $article->setId($id);
            self::$sample1["$id"] = $article;
        }
    }

    protected function setUp()
    {
        $this->collection = new Collection(self::$sample1);
    }

    public function testCount()
    {
        $collection = new Collection();
        $this->assertCount(0, $collection);

        $collection = new Collection(self::$sample1);
        $this->assertCount(10, $collection);
    }

    public function testGetItems()
    {
        $collection = $this->collection;
        $this->assertEquals(self::$sample1, $collection->getItems());
    }

    public function testAddItem()
    {
        $collection = new Collection();
        $this->assertFalse($collection->isEdited());

        // new item
        $item = new Article();
        $item->setId(23);
        $collection->addItem($item);
        $this->assertTrue($collection->isEdited());
        $this->assertCount(1, $collection);

        // new item
        $item = new Article();
        $item->setId(43);
        $collection->addItem($item);
        $this->assertTrue($collection->isEdited());
        $this->assertCount(2, $collection);

        $collection->resetEdit();
        $this->assertFalse($collection->isEdited());

        // existing item
        $item = new Article();
        $item->setId(23);
        $collection->addItem($item);
        $this->assertFalse($collection->isEdited());
        $this->assertCount(2, $collection);
    }

    public function testRemovingItem()
    {
        $collection = new Collection(self::$sample1);
        $this->assertCount(10, $collection);
        $this->assertFalse($collection->isEdited());

        $collection->removeItem(4);
        $this->assertCount(10, $collection);
        $this->assertFalse($collection->isEdited());

        $expected = self::$sample1;
        unset($expected["1"]);
        $collection->removeItem(1);
        $this->assertCount(9, $collection);
        $this->assertTrue($collection->isEdited());
        $this->assertEquals($expected, $collection->getItems());
    }

    public function testHasItem()
    {
        $collection = new Collection(self::$sample1);

        $this->assertFalse($collection->hasItem(4));
        $this->assertFalse($collection->hasItem(5));

        $this->assertTrue($collection->hasItem(3));
        $this->assertTrue($collection->hasItem(10));
    }

    public function testMoveUpItems()
    {
        $collection = new Collection(self::$sample1);
        $this->assertCount(10, $collection);
        $expected = [13,12,11,1,2,3,10,9,8,7];
        $this->assertFalse($collection->isEdited());

        // move up [1]
        $expected = [13,12,1,11,2,3,10,9,8,7];
        $collection->moveUpItems([1]);
        foreach($collection->getItems() as $item) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $item->getId());
            next($expected);
        }
        $this->assertTrue($collection->isEdited());

        // move up [12,1,2,8,7]
        $expected = [12,1,13,2,11,3,10,8,7,9];
        $collection->moveUpItems([12,1,2,8,7]);
        foreach($collection->getItems() as $item) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $item->getId());
            next($expected);
        }
        $this->assertTrue($collection->isEdited());

        // move up [100,12,1,13,2]
        $collection->resetEdit();
        $expected = [12,1,13,2,11,3,10,8,7,9];
        $this->assertFalse($collection->isEdited());
        $collection->moveUpItems([100,12,1,13,2]);
        foreach($collection->getItems() as $item) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $item->getId());
            next($expected);
        }
        $this->assertFalse($collection->isEdited());

        // move up [12,1,13,3,10]
        $collection->resetEdit();
        $expected = [12,1,13,2,3,10,11,8,7,9];
        $this->assertFalse($collection->isEdited());
        $collection->moveUpItems([12,1,13,3,10]);
        foreach($collection->getItems() as $item) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $item->getId());
            next($expected);
        }
        $this->assertTrue($collection->isEdited());

        // move up [1,2,3]
        $collection->resetEdit();
        $expected = [1,12,2,3,13,10,11,8,7,9];
        $this->assertFalse($collection->isEdited());
        $collection->moveUpItems([1,2,3]);
        foreach($collection->getItems() as $item) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $item->getId());
            next($expected);
        }
        $this->assertTrue($collection->isEdited());

        // move up [2,3,10,11,7]
        $collection->resetEdit();
        $expected = [1,2,3,12,10,11,13,7,8,9];
        $this->assertFalse($collection->isEdited());
        $collection->moveUpItems([2,3,10,11,7]);
        foreach($collection->getItems() as $item) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $item->getId());
            next($expected);
        }
        $this->assertTrue($collection->isEdited());

        // move up [10,11,7,8,9]
        $collection->resetEdit();
        $expected = [1,2,3,10,11,12,7,8,9,13];
        $this->assertFalse($collection->isEdited());
        $collection->moveUpItems([10,11,7,8,9]);
        foreach($collection->getItems() as $item) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $item->getId());
            next($expected);
        }
        $this->assertTrue($collection->isEdited());

        // move up [7,8,9]
        $collection->resetEdit();
        $expected = [1,2,3,10,11,7,8,9,12,13];
        $this->assertFalse($collection->isEdited());
        $collection->moveUpItems([7,8,9]);
        foreach($collection->getItems() as $item) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $item->getId());
            next($expected);
        }
        $this->assertTrue($collection->isEdited());

        // move up [7,8,9]
        $collection->resetEdit();
        $expected = [1,2,3,10,7,8,9,11,12,13];
        $this->assertFalse($collection->isEdited());
        $collection->moveUpItems([7,8,9]);
        foreach($collection->getItems() as $item) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $item->getId());
            next($expected);
        }
        $this->assertTrue($collection->isEdited());

        // move up [7,8,9]
        $collection->resetEdit();
        $expected = [1,2,3,7,8,9,10,11,12,13];
        $this->assertFalse($collection->isEdited());
        $collection->moveUpItems([7,8,9]);
        foreach($collection->getItems() as $item) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $item->getId());
            next($expected);
        }
        $this->assertTrue($collection->isEdited());

        // move up [100,99,1,2,3,7,8,9]
        $collection->resetEdit();
        $expected = [1,2,3,7,8,9,10,11,12,13];
        $this->assertFalse($collection->isEdited());
        $collection->moveUpItems([100,99,1,2,3,7,8,9]);
        foreach($collection->getItems() as $item) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $item->getId());
            next($expected);
        }
        $this->assertFalse($collection->isEdited());
    }

    public function testMoveDownItems()
    {
        $collection = new Collection(self::$sample1);
        $this->assertCount(10, $collection);
        $expected = [13,12,11,1,2,3,10,9,8,7];
        $this->assertFalse($collection->isEdited());
        foreach($collection->getItems() as $item) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $item->getId());
            next($expected);
        }

        // move Down [13]
        $expected = [12,13,11,1,2,3,10,9,8,7];
        $collection->moveDownItems([13]);
        foreach($collection->getItems() as $item) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $item->getId());
            next($expected);
        }
        $this->assertTrue($collection->isEdited());

        // move Down [12,13]
        $collection->resetEdit();
        $expected = [11,12,13,1,2,3,10,9,8,7];
        $collection->moveDownItems([12,13]);
        foreach($collection->getItems() as $item) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $item->getId());
            next($expected);
        }
        $this->assertTrue($collection->isEdited());

        // move Down [8,7,200,300]
        $collection->resetEdit();
        $expected = [11,12,13,1,2,3,10,9,8,7];
        $collection->moveDownItems([8,7,200,300]);
        foreach($collection->getItems() as $item) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $item->getId());
            next($expected);
        }
        $this->assertFalse($collection->isEdited());

        // move Down [8]
        $collection->resetEdit();
        $expected = [11,12,13,1,2,3,10,9,7,8];
        $collection->moveDownItems([8]);
        foreach($collection->getItems() as $item) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $item->getId());
            next($expected);
        }
        $this->assertTrue($collection->isEdited());

        // move Down [11,12,13]
        $collection->resetEdit();
        $expected = [1,11,12,13,2,3,10,9,7,8];
        $collection->moveDownItems([11,12,13]);
        foreach($collection->getItems() as $item) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $item->getId());
            next($expected);
        }
        $this->assertTrue($collection->isEdited());

        // move Down [100,11,12,13]
        $collection->resetEdit();
        $expected = [1,2,11,12,13,3,10,9,7,8];
        $collection->moveDownItems([100,11,12,13]);
        foreach($collection->getItems() as $item) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $item->getId());
            next($expected);
        }
        $this->assertTrue($collection->isEdited());

        // move Down [11,12,13]
        $collection->resetEdit();
        $expected = [1,2,3,11,12,13,10,9,7,8];
        $collection->moveDownItems([11,12,13]);
        foreach($collection->getItems() as $item) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $item->getId());
            next($expected);
        }
        $this->assertTrue($collection->isEdited());

        // move Down [11,12,13]
        $collection->resetEdit();
        $expected = [1,2,3,10,11,12,13,9,7,8];
        $collection->moveDownItems([11,12,13]);
        foreach($collection->getItems() as $item) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $item->getId());
            next($expected);
        }
        $this->assertTrue($collection->isEdited());

        // move Down [10,11,12,13]
        $collection->resetEdit();
        $expected = [1,2,3,9,10,11,12,13,7,8];
        $collection->moveDownItems([10,11,12,13]);
        foreach($collection->getItems() as $item) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $item->getId());
            next($expected);
        }
        $this->assertTrue($collection->isEdited());

        // move Down [9,10,11,12,13]
        $collection->resetEdit();
        $expected = [1,2,3,7,9,10,11,12,13,8];
        $collection->moveDownItems([9,10,11,12,13]);
        foreach($collection->getItems() as $item) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $item->getId());
            next($expected);
        }
        $this->assertTrue($collection->isEdited());

        // move Down [9,10,11,12,13]
        $collection->resetEdit();
        $expected = [1,2,3,7,8,9,10,11,12,13];
        $collection->moveDownItems([9,10,11,12,13]);
        foreach($collection->getItems() as $item) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $item->getId());
            next($expected);
        }
        $this->assertTrue($collection->isEdited());

        // move Down [9,10,11,12,13]
        $collection->resetEdit();
        $expected = [1,2,3,7,8,9,10,11,12,13];
        $collection->moveDownItems([9,10,11,12,13]);
        foreach($collection->getItems() as $item) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $item->getId());
            next($expected);
        }
        $this->assertFalse($collection->isEdited());
    }

    public function testRemoveItems()
    {
        $collection = new Collection(self::$sample1);
        $this->assertFalse($collection->isEdited());
        $expected = [13,12,11,1,2,3,10,9,8,7];
        foreach($collection->getItems() as $item) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $item->getId());
            next($expected);
        }

        $collection->removeItems([100,101,102,103]);
        $this->assertFalse($collection->isEdited());

        $collection->removeItems([100,101,11,1,2]);
        $this->assertTrue($collection->isEdited());
        $expected = [13,12,3,10,9,8,7];
        foreach($collection->getItems() as $item) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $item->getId());
            next($expected);
        }
    }

    public function testSort()
    {
        $collection = $this->collection;
        $this->assertFalse($collection->isEdited());
        $order = [1,2,3,11,12,13,7,8,9,10];
        $collection->sort($order);
        foreach($collection->getItems() as $item) {
            $expectedId = current($order);
            $this->assertEquals($expectedId, $item->getId());
            next($order);
        }
        $this->assertTrue($collection->isEdited());

        $order = range(0,14);
        $collection->resetEdit();
        $expected = [1,2,3,7,8,9,10,11,12,13];
        $collection->sort($order);
        foreach($collection->getItems() as $item) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $item->getId());
            next($expected);
        }
        $this->assertTrue($collection->isEdited());

        $order = range(0,14);
        $collection->resetEdit();
        $expected = [1,2,3,7,8,9,10,11,12,13];
        $collection->sort($order);
        foreach($collection->getItems() as $item) {
            $expectedId = current($expected);
            $this->assertEquals($expectedId, $item->getId());
            next($expected);
        }
        $this->assertFalse($collection->isEdited());
    }

    public function testSerialize()
    {
        $expected = array(
            'id'         => 0,
            'name'       => '',
            'createdOn'  => null,
            'updatedOn'  => null,
            'itemIds' => '13,12,11,1,2,3,10,9,8,7',
            'edited' => false
        );
        $collection = new Collection(self::$sample1);
        $this->assertEquals($expected, $collection->serialize());

        $expected = array(
            'id' => 233,
            'name' => 'Pancytopenia in HIV',
            'createdOn' => new DateTime('2014-07-21'),
            'updatedOn' => new DateTime('2014-07-23'),
            'itemIds' => '13,12,11,1,2,3,10,9,8,7,4',
            'edited' => true
        );
        $collection = new Collection(
            self::$sample1,
            233,
            'Pancytopenia in HIV',
            new DateTime('2014-07-21'),
            new DateTime('2014-07-23')
        );
        $item = new Article();
        $item->setId(4);
        $collection->addItem($item);
        $this->assertEquals($expected, $collection->serialize());
    }
}