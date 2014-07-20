<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'Pubmed\Service\Pubmed' => function(\Zend\ServiceManager\ServiceManager $sm) {
                $table = $sm->get('Article\Table\Article');
                $adapter = new \Pubmed\Entity\Adapter();
                $service = new \Pubmed\Service\PubmedService();
                $service->setAdapter($adapter);
                $service->setTable($table);
                return $service;
            }
        )
    )
);