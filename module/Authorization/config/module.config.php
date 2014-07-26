<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'Authorization\Service\Authorization' => 'Authorization\Service\AuthorizationServiceFactory'
        )
    ),
    'view_helpers' => array(
        'factories' => array(
            'hasAccess' => 'Authorization\View\Helper\HasAccessServiceFactory'
        )
    )
);