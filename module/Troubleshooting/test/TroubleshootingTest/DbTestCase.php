<?php
namespace TroubleshootingTest;

use PHPUnit_Extensions_Database_TestCase,
    PHPUnit_Extensions_Database_DB_IDatabaseConnection,
    PHPUnit_Extensions_Database_DataSet_IDataSet;

use PDO;
use Zend\Db\Adapter\Adapter;

class DbTestCase extends PHPUnit_Extensions_Database_TestCase
{
    static private $pdo = null;

    private $config = array(
        'driver'   => 'Pdo',
        'dsn'      => 'mysql:host=localhost;dbname=RefgenTest',
        'username' => 'root',
        'password' => 'K1rth1k@s1n1',
    );

    private $dbName = 'RefgenTest';
    /**
     * Returns the test database connection.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    final public function getConnection()
    {
        static $conn = null;
        if($conn == null) {
            if(self::$pdo == null) {
                self::$pdo = new PDO(
                    $this->config['dsn'],
                    $this->config['username'],
                    $this->config['password']
                );
            }
            $conn = $this->createDefaultDBConnection(self::$pdo, $this->dbName);
        }
        return $conn;
    }

    /**
     * Returns the test dataset.
     *
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {
        return $this->createXMLDataSet('fixture.xml');
    }

    /**
     * Configures and returns the adapter
     *
     * @return Adapter
     */
    protected function getAdapter()
    {
        /** @var \Zend\Db\Adapter\Adapter $adapter */
        static $adapter = null;

        if($adapter == null) {
            $adapter = new Adapter($this->config);
        }
        return $adapter;
    }

} 