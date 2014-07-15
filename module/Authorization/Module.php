<?php
namespace Authorization;

use Zend\Mvc\MvcEvent,
    Zend\Mvc\Router\RouteMatch;
use Authorization\Service\AuthorizationService;
use Zend\View\Model\ViewModel;


class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $events = $e->getApplication()->getEventManager();
        $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'dispatchErrorListener'));
        $events->attach('route', array($this, 'checkAcl'));
    }

    public function checkAcl(MvcEvent $e)
    {
        $app = $e->getApplication();
        $sm = $app->getServiceManager();

        /** @var AuthorizationService $aclService */
        $service = $sm->get('Authorization\Service\Authorization');

        /** @var RouteMatch $routeMatch */
        $routeMatch = $e->getRouteMatch();

        $resource = $routeMatch->getParam('controller');
        $privilege = $routeMatch->getParam('action');

        if($service->hasResource($resource)) {
            if(!$service->hasAccess($resource, $privilege)) {
                $e->setError('ACCESS_DENIED')
                  ->setParam('route', $routeMatch->getMatchedRouteName());
                $app->getEventManager()->trigger('dispatch.error', $e);
            }
        }
    }

    public function dispatchErrorListener(MvcEvent $e)
    {
        $error = $e->getError();
        if(empty($error) || $error != 'ACCESS_DENIED') {
            return;
        }
        $result = $e->getResult();
        if($result instanceof \Zend\Stdlib\Response) {
            return;
        }

        $baseModel = new ViewModel();
        $baseModel->setTemplate('layout/layout');

        $model = new ViewModel();
        $model->setTemplate('error/403');

        $baseModel->addChild($model);
        $baseModel->setTerminal(true);

        $e->setViewModel($baseModel);

        $response = $e->getResponse();
        $response->setStatusCode(403);

        $e->setResponse($response);
        $e->setResult($baseModel);
        return false;
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
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                )
            )
        );
    }
} 