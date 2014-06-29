<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'Pubmed\Service\Pubmed' => function(\Zend\ServiceManager\ServiceManager $sm) {
                $table = $sm->get('Article\Table\Article');
                $service = new \Pubmed\Service\PubmedService();
                $service->setTable($table);
                return $service;
            }
        )
    )
);