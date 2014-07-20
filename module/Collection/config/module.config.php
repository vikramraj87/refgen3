<?php
return array(
    'router' => array(
        'routes' => array(
            'collection' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/collection',
                    'defaults' => array(
                        'controller' => 'Collection\Controller\Collection'
                    )
                ),
                'may_terminate' => false,
                'child_routes' => array(
                    'new' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/new',
                            'defaults' => array(
                                'action' => 'new'
                            )
                        )
                    ),
                    'open' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => '/open[/:id]',
                            'constraints' => array(
                                'id' => '\d+'
                            ),
                            'defaults' => array(
                                'id' => 0,
                                'action' => 'open'
                            )
                        )
                    ),
                    'save' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/save',
                            'defaults' => array(
                                'action' => 'save'
                            )
                        )
                    ),
                    'delete' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/delete',
                            'defaults' => array(
                                'action' => 'delete'
                            )
                        )
                    ),
                    'list' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/list',
                            'defaults' => array(
                                'action' => 'list'
                            )
                        )
                    ),
                    'set-active' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => '/set-active/[:id]',
                            'constraints' => array(
                                'id' => '\d+'
                            ),
                            'defaults' => array(
                                'action' => 'set-active',
                                'id' => 0
                            )
                        )
                    ),
                    'export' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => '/export/[:id]',
                            'constraints' => array(
                                'id' => '\d+'
                            ),
                            'defaults' => array(
                                'action' => 'export',
                                'id' => 0
                            )
                        )
                    ),
                    'download' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => '/download/[:id]',
                            'constraints' => array(
                                'id' => '\d+'
                            ),
                            'defaults' => array(
                                'action' => 'download',
                                'id' => 0
                            )
                        )
                    )
                )
            ),
            'active-collection' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/active-collection',
                    'defaults' => array(
                        'controller' => 'Collection\Controller\ActiveCollection',
                        'action'     => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'add' => array(
                        'type' => 'Zend\Mvc\Router\http\Segment',
                        'options' => array(
                            'route' => '/add/[:id]',
                            'constraints' => array(
                                'id' => '\d+'
                            ),
                            'defaults' => array(
                                'id' => '0',
                                'action' => 'add'
                            )
                        )
                    ),
                    'remove' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => '/remove/[:id]',
                            'constraints' => array(
                                'id' => '\d+'
                            ),
                            'defaults' => array(
                                'id' => 0,
                                'action' => 'remove'
                            )
                        )
                    )
                )
            )
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'Collection\Service\Collection' => function(\Zend\ServiceManager\ServiceManager $sm) {
                $articleTable    = $sm->get('Article\Table\Article');
                $collectionTable = $sm->get('Collection\Table\Collection');
                $service = new \Collection\Service\CollectionService();
                $service->setTable($collectionTable);
                $service->setArticleTable($articleTable);
                $service->init();
                return $service;
            },
            'Collection\Table\CollectionArticle' => function(\Zend\ServiceManager\ServiceManager $sm) {
                $adapter      = $sm->get('Zend\Db\Adapter\Adapter');
                $articleTable = $sm->get('Article\Table\Article');
                $table = new \Collection\Table\CollectionArticleTable();
                $table->setDbAdapter($adapter);
                $table->setArticleTable($articleTable);
                return $table;
            },
            'Collection\Table\Collection' => function(\Zend\ServiceManager\ServiceManager $sm) {
                $adapter                = $sm->get('Zend\Db\Adapter\Adapter');
                $collectionArticleTable = $sm->get('Collection\Table\CollectionArticle');
                $table = new \Collection\Table\CollectionTable();
                $table->setDbAdapter($adapter);
                $table->setCollectionArticleTable($collectionArticleTable);
                return $table;
            }
        )
    ),
    'controllers' => array(
        'factories' => array(
            'Collection\Controller\ActiveCollection' => function(\Zend\Mvc\Controller\ControllerManager $cm) {
                $sm = $cm->getServiceLocator();
                $service = $sm->get('Collection\Service\Collection');
                $controller = new \Collection\Controller\ActiveCollectionController();
                $controller->setService($service);
                return $controller;
            },
            'Collection\Controller\Collection' => function (\Zend\Mvc\Controller\ControllerManager $cm) {
                $sm = $cm->getServiceLocator();
                $table = $sm->get('Collection\Table\Collection');
                $controller = new \Collection\Controller\CollectionController();
                $service = $sm->get('Collection\Service\Collection');
                $authService = $sm->get('Authentication\Service\Authentication');
                $controller->setCollectionTable($table);
                $controller->setCollectionService($service);
                $controller->setAuthService($authService);
                return $controller;
            }
        )
    ),
    'view_helpers' => array(
        'factories' => array(
            'activeCollection' => function (\Zend\View\HelperPluginManager $vm) {
                $sm = $vm->getServiceLocator();
                $service = $sm->get('Collection\Service\Collection');
                $helper = new \Collection\View\Helper\ActiveCollectionHelper($service);
                return $helper;
            },
            'collections' => function(\Zend\View\HelperPluginManager $vm) {
                $sm = $vm->getServiceLocator();
                $service = $sm->get('Authentication\Service\Authentication');
                $table = $sm->get('Collection\Table\Collection');
                $helper = new \Collection\View\Helper\CollectionHelper($table, $service);
                return $helper;
            }
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view'
        )
    )
);