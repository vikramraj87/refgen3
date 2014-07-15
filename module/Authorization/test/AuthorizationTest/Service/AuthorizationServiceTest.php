<?php
namespace AuthorizationTest\AuthorizationTest\Service;

use PHPUnit_Framework_TestCase;
use Authorization\Service\AuthorizationService;
class AuthorizationServiceTest extends PHPUnit_Framework_TestCase
{
    private $config = array();

    public function testAclWithMember()
    {
        $authService = $this->getMock('Authentication\Service\AuthenticationService', array('hasIdentity'));
        $authService->expects($this->any())
                    ->method('hasIdentity')
                    ->will($this->returnValue(true));
        $service = new AuthorizationService($this->config(), $authService);
        $this->assertFalse($service->hasAccess('User\Controller\User', 'login'));
        $this->assertTrue($service->hasAccess('User\Controller\User', 'logout'));
        $this->assertTrue($service->hasAccess('User\Controller\User', 'profile'));
        $this->assertTrue($service->hasAccess('Collection\Controller\Collection', 'new'));
        $this->assertTrue($service->hasAccess('Collection\Controller\Collection', 'edit'));
        $this->assertTrue($service->hasAccess('Collection\Controller\Collection', 'open'));
        $this->assertTrue($service->hasAccess('Collection\Controller\Collection', 'delete'));
    }

    public function testAclWithGuest()
    {
        $authService = $this->getMock('Authentication\Service\AuthenticationService', array('hasIdentity'));
        $authService->expects($this->any())
            ->method('hasIdentity')
            ->will($this->returnValue(false));
        $service = new AuthorizationService($this->config(), $authService);
        $this->assertTrue($service->hasAccess('User\Controller\User', 'login'));
        $this->assertFalse($service->hasAccess('User\Controller\User', 'logout'));
        $this->assertFalse($service->hasAccess('Collection\Controller\Collection', 'new'));
        $this->assertFalse($service->hasAccess('Collection\Controller\Collection', 'edit'));
        $this->assertFalse($service->hasAccess('Collection\Controller\Collection', 'open'));
        $this->assertFalse($service->hasAccess('Collection\Controller\Collection', 'delete'));
    }

    private function config()
    {
        if(empty($this->config)) {
            $this->config = array(
                'guest' => array(
                    'User\Controller\User' => array(
                        'login'
                    )
                ),
                'member' => array(
                    'User\Controller\User' => array(
                        'profile',
                        'logout'
                    ),
                    'Collection\Controller\Collection'
                )
            );
        }
        return $this->config;
    }
} 