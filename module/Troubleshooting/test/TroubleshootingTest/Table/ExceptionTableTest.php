<?php
namespace TroubleshootingTest\Table;

use TroubleshootingTest\DbTestCase;

use Troubleshooting\Table\ExceptionTable,
    Troubleshooting\Table\ExceptionTraceTable,
    Troubleshooting\Entity\Exception;

class ExceptionTableTest extends DbTestCase
{
    /** @var ExceptionTable */
    private $table;

    /** @var ExceptionTraceTable */
    private $traceTable;

    protected function setUp()
    {
        $conn = $this->getConnection();
        $conn->getConnection()->query('set foreign_key_checks=0');
        $adapter = $this->getAdapter();

        $this->table = new ExceptionTable();
        $this->table->setDbAdapter($adapter);

        $this->traceTable = new ExceptionTraceTable();
        $this->traceTable->setDbAdapter($adapter);
        $this->table->setTraceTable($this->traceTable);

        parent::setUp();
        $conn->getConnection()->query('set foreign_key_checks=1');
    }

    public function testFetchById()
    {
        /** @var Exception $exception */
        $exception = $this->table->fetchExceptionById(1);

        $trace = array(
            '/Users/vikramraj/Sites/refgen3/vendor/zendframework/zendframework/library/Zend/Mvc/Controller/AbstractActionController.php(83): Application\Controller\IndexController->searchAction()',
            '[internal function]: Zend\Mvc\Controller\AbstractActionController->onDispatch(Object(Zend\Mvc\MvcEvent))',
            '/Users/vikramraj/Sites/refgen3/vendor/zendframework/zendframework/library/Zend/EventManager/EventManager.php(468): call_user_func(Array, Object(Zend\Mvc\MvcEvent))',
            '/Users/vikramraj/Sites/refgen3/vendor/zendframework/zendframework/library/Zend/EventManager/EventManager.php(207): Zend\EventManager\EventManager->triggerListeners(\'dispatch\', Object(Zend\Mvc\MvcEvent), Object(Closure))',
            '/Users/vikramraj/Sites/refgen3/vendor/zendframework/zendframework/library/Zend/Mvc/Controller/AbstractController.php(117): Zend\EventManager\EventManager->trigger(\'dispatch\', Object(Zend\Mvc\MvcEvent), Object(Closure))',
            '/Users/vikramraj/Sites/refgen3/vendor/zendframework/zendframework/library/Zend/Mvc/DispatchListener.php(114): Zend\Mvc\Controller\AbstractController->dispatch(Object(Zend\Http\PhpEnvironment\Request), Object(Zend\Http\PhpEnvironment\Response))',
            '[internal function]: Zend\Mvc\DispatchListener->onDispatch(Object(Zend\Mvc\MvcEvent))',
            '/Users/vikramraj/Sites/refgen3/vendor/zendframework/zendframework/library/Zend/EventManager/EventManager.php(468): call_user_func(Array, Object(Zend\Mvc\MvcEvent))',
            '/Users/vikramraj/Sites/refgen3/vendor/zendframework/zendframework/library/Zend/EventManager/EventManager.php(207): Zend\EventManager\EventManager->triggerListeners(\'dispatch\', Object(Zend\Mvc\MvcEvent), Object(Closure))',
            '/Users/vikramraj/Sites/refgen3/vendor/zendframework/zendframework/library/Zend/Mvc/Application.php(313): Zend\EventManager\EventManager->trigger(\'dispatch\', Object(Zend\Mvc\MvcEvent), Object(Closure))',
            '/Users/vikramraj/Sites/refgen3/public_html/index.php(17): Zend\Mvc\Application->run()',
            '{main}'
        );

        $this->assertEquals(1, $exception->getId());
        $this->assertEquals('RuntimeException', $exception->getClass());
        $this->assertEquals(0, $exception->getCode());
        $this->assertEquals('Pubmed server error', $exception->getMessage());
        $this->assertEquals('/Users/vikramraj/Sites/refgen3/module/Application/src/Application/Controller/IndexController.php', $exception->getFile());
        $this->assertEquals(42, $exception->getLine());
        $this->assertEquals($trace, $exception->getTrace());
        $this->assertEquals(new \DateTime('2014-07-20 14:20:00'), $exception->getRaisedOn());

        $previousTrace = array(
            '[internal function]: TroubleshootingTest\TroubleshootingTest\Table\ExceptionTableTest->testExceptionToArray()',
            '/usr/local/php5-5.4.21-20131021-133854/lib/php/PHPUnit/Framework/TestCase.php(983): ReflectionMethod->invokeArgs(Object(TroubleshootingTest\TroubleshootingTest\Table\ExceptionTableTest), Array)',
            '/usr/local/php5-5.4.21-20131021-133854/lib/php/PHPUnit/Framework/TestCase.php(838): PHPUnit_Framework_TestCase->runTest()',
            '/usr/local/php5-5.4.21-20131021-133854/lib/php/PHPUnit/Framework/TestResult.php(648): PHPUnit_Framework_TestCase->runBare()',
            '/usr/local/php5-5.4.21-20131021-133854/lib/php/PHPUnit/Framework/TestCase.php(783): PHPUnit_Framework_TestResult->run(Object(TroubleshootingTest\TroubleshootingTest\Table\ExceptionTableTest))',
            '/usr/local/php5-5.4.21-20131021-133854/lib/php/PHPUnit/Framework/TestSuite.php(775): PHPUnit_Framework_TestCase->run(Object(PHPUnit_Framework_TestResult))',
            '/usr/local/php5-5.4.21-20131021-133854/lib/php/PHPUnit/Framework/TestSuite.php(745): PHPUnit_Framework_TestSuite->runTest(Object(TroubleshootingTest\TroubleshootingTest\Table\ExceptionTableTest), Object(PHPUnit_Framework_TestResult))',
            '/usr/local/php5-5.4.21-20131021-133854/lib/php/PHPUnit/Framework/TestSuite.php(705): PHPUnit_Framework_TestSuite->run(Object(PHPUnit_Framework_TestResult), false, Array, Array, false)',
            '/usr/local/php5-5.4.21-20131021-133854/lib/php/PHPUnit/TextUI/TestRunner.php(349): PHPUnit_Framework_TestSuite->run(Object(PHPUnit_Framework_TestResult), false, Array, Array, false)',
            '/usr/local/php5-5.4.21-20131021-133854/lib/php/PHPUnit/TextUI/Command.php(176): PHPUnit_TextUI_TestRunner->doRun(Object(PHPUnit_Framework_TestSuite), Array)',
            '/usr/local/php5-5.4.21-20131021-133854/lib/php/PHPUnit/TextUI/Command.php(129): PHPUnit_TextUI_Command->run(Array, true)',
            '/usr/local/php5-5.4.21-20131021-133854/bin/phpunit(46): PHPUnit_TextUI_Command::main()',
            '{main}'
        );

        $previous = $exception->getPrevious();
        $this->assertEquals(2, $previous->getId());
        $this->assertEquals('Exception', $previous->getClass());
        $this->assertEquals(13, $previous->getCode());
        $this->assertEquals('', $previous->getMessage());
        $this->assertEquals('/Users/vikramraj/Sites/refgen3/module/Troubleshooting/test/TroubleshootingTest/Table/ExceptionTableTest.php', $previous->getFile());
        $this->assertEquals(10, $previous->getLine());
        $this->assertEquals($previousTrace, $previous->getTrace());
        $this->assertEquals(new \DateTime('2014-07-20 14:20:22'), $previous->getRaisedOn());

        $prevPrevious = $previous->getPrevious();
        $this->assertEquals(3, $prevPrevious->getId());
        $this->assertEquals('Exception', $prevPrevious->getClass());
        $this->assertEquals('', $prevPrevious->getCode());
        $this->assertEquals('Test Exception', $prevPrevious->getMessage());
        $this->assertEquals('/Users/vikramraj/Sites/refgen3/module/Troubleshooting/test/TroubleshootingTest/Table/ExceptionTraceTableTest.php', $prevPrevious->getFile());
        $this->assertEquals(7, $prevPrevious->getLine());
        $this->assertEquals(array(), $prevPrevious->getTrace());
        $this->assertEquals(new \DateTime('2014-07-20 14:20:23'), $prevPrevious->getRaisedOn());
    }

    public function testSaveException()
    {
        try {
            try {
                try {
                    throw new \RuntimeException();
                } catch (\RuntimeException $e) {
                    throw new \InvalidArgumentException('Rerethrow', 0, $e);
                }
            } catch(\InvalidArgumentException $e) {
                throw new \Exception('Rethrow an exception', 21, $e);
            }
        } catch(\Exception $e) {
            $this->table->saveException($e);
        }
        $exception = $this->table->fetchExceptionById(4);
        $this->assertEquals('Exception', $exception->getClass());
        $this->assertEquals('InvalidArgumentException', $exception->getPrevious()->getClass());
        $this->assertEquals('RuntimeException', $exception->getPrevious()->getPrevious()->getClass());
    }
} 