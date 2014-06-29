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