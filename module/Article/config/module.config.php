<?php
return array(
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view'
        )
    ),
    'view_helpers' => array(
        'invokables' => array(
            'authors'  => 'Article\View\Helper\AuthorHelper',
            'abstract' => 'Article\View\Helper\AbstractTextHelper',
            'citation' => 'Article\View\Helper\VancouverHelper'
        )
    ),
    'router' => array(
        'routes' => array(
            'article' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/article/[:id]',
                    'defaults' => array(
                        'controller' => 'Article\Controller\Article',
                        'action'     => 'index'
                    )
                )
            )
        )
    ),
    'controllers' => array(
        'factories' => array(
            'Article\Controller\Article' => function(\Zend\Mvc\Controller\ControllerManager $cm) {
                $sm = $cm->getServiceLocator();
                $table = $sm->get('Article\Table\Article');
                $controller = new \Article\Controller\ArticleController();
                $controller->setTable($table);
                return $controller;
            }
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'Article\Table\Article' => function(\Zend\ServiceManager\ServiceManager $sm) {
                $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                $table   = new \Article\Table\ArticleTable();
                $table->setDbAdapter($adapter);
                return $table;
            }
        )
    )
);