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
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => '/delete/:id',
                            'constraints' => array(
                                'id' => '\d+'
                            ),
                            'defaults' => array(
                                'id' => 0,
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
                    'render' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/render',
                            'defaults' => array(
                                'action' => 'render'
                            )
                        )
                    ),
                    'processMultiple' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/process-multiple',
                            'defaults' => array(
                                'action' => 'process-multiple'
                            )
                        )
                    ),
                    'remove' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => '/remove/:ids',
                            'constraints' => array(
                                'ids' => '\d+[(,\d+)]*'
                            ),
                            'defaults' => array(
                                'action' => 'remove'
                            )
                        )
                    ),
                    'sort' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => '/sort/:ids',
                            'constraints' => array(
                                'ids' => '\d+[(,\d+)]*'
                            ),
                            'defaults' => array(
                                'action' => 'sort'
                            )
                        )
                    )
                )
            )
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'Collection\Service\Collection'      => 'Collection\Service\CollectionServiceFactory',
            'Collection\Table\CollectionArticle' => 'Collection\Table\CollectionArticleTableServiceFactory',
            'Collection\Table\Collection'        => 'Collection\Table\CollectionTableServiceFactory'
        )
    ),
    'controllers' => array(
        'factories' => array(
            'Collection\Controller\ActiveCollection' => 'Collection\Controller\ActiveCollectionControllerServiceFactory',
            'Collection\Controller\Collection'       => 'Collection\Controller\CollectionControllerServiceFactory'
        )
    ),
    'view_helpers' => array(
        'factories' => array(
            'activeCollection' => 'Collection\View\Helper\ActiveCollectionHelperServiceFactory',
            'collections'      => 'Collection\View\Helper\CollectionHelperServiceFactory'
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view'
        )
    )
);