<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener,
    Zend\Mvc\MvcEvent;
use Zend\Session\SessionManager,
    Zend\Session\Container,
    Zend\Session\Config\SessionConfig;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $serviceManager = $e->getApplication()->getServiceManager();
        $config         = $serviceManager->get('config');
        $sessionConfig  = new SessionConfig();
        $sessionConfig->setOptions($config['session']);
        $sessionManager = new SessionManager($sessionConfig);
        $sessionManager->start();

        if(array_key_exists('php_settings', $config)) {
            $phpSettings = $config['php_settings'];
            foreach($phpSettings as $k => $v) {
                ini_set($k, $v);
            }
        }

        if(array_key_exists('error_handling', $config)) {
            $errorSettings = $config['error_handling'];
            $redirectUrl = '';
            if(array_key_exists('recover_from_fatal', $errorSettings) &&
                $errorSettings['recover_from_fatal']) {
                $redirectUrl = $errorSettings['redirect_url'];
            }

            $callBack = null;
            if(array_key_exists('fatal_errors_callback', $errorSettings)) {
                $callBack = $errorSettings['fatal_errors_callback'];
            }

            register_shutdown_function(
                array('Application\Module', 'handleFatalPhpErrors'),
                $redirectUrl
            );
            set_error_handler(array('Application\Module', 'handlePhpErrors'));
        }
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public static function handlePhpErrors($i_type, $s_message, $s_file, $i_line)
    {
        if(!($i_type & error_reporting())) {return; }
        throw new \Exception('Error: ' . $s_message . ' in file ' . $s_file . ' at line' . $i_line);
    }

    public static function handleFatalPhpErrors($redirect, $callBack = null)
    {
        header("location: ". $redirect);
        return false;
    }
}
