<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 11/06/14
 * Time: 11:46 PM
 */

namespace User;


class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                )
            )
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
} 