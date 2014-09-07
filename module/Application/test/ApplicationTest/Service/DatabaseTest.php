<?php
namespace ApplicationTest\Service;

use PDO,
    SimpleXMLElement;
class DatabaseTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {

    }

    public function testCreateXml()
    {
        $pdo = new PDO(
            'mysql:host=localhost;dbname=refgen3',
            'root',
            'K1rth1k@s1n1'
        );
        $tables = array();
        $result = $pdo->query('SHOW TABLES');
        while($row = $result->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }
        $dataset = new SimpleXMLElement('<dataset/>');
        foreach($tables as $table) {
            $result = $pdo->query(sprintf('SELECT * FROM %s', $table));
            $columnCount = $result->columnCount();
            $columns = array();
            for($i = 0; $i < $columnCount; $i++) {
                $columnData = $result->getColumnMeta($i);
                $columns[] = $columnData['name'];
            }
            $tableTag = $dataset->addChild('table');
            $tableTag->addAttribute('name', $table);
            foreach($columns as $column) {
                $tableTag->addChild('column', $column);
            }
            if($result->rowCount()) {
                while($row = $result->fetch(PDO::FETCH_NUM)) {
                    $rowTag = $tableTag->addChild('row');
                    foreach($row as $col) {
                        if(null !== $col) {
                            $rowTag->addChild('value', htmlentities($col));
                        } else {
                            $rowTag->addChild('null');
                        }
                    }
                }
            }

        }
        $dataset->asXML('test.xml');
    }
}
 