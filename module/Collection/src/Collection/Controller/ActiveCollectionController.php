<?php
namespace Collection\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Collection\Service\CollectionService;

class ActiveCollectionController extends AbstractActionController
{
    /** @var CollectionService */
    private $service;

    public function addAction()
    {
        $id = $this->params()->fromRoute('id', 0);
        $redirect = urldecode($this->params()->fromQuery('redirect', '/'));

        $id = (int) $id;
        $this->service->addArticle($id);
        $this->redirect()->toUrl($redirect);
    }

    public function removeAction()
    {
        $id = $this->params()->fromRoute('id', 0);
        $redirect = urldecode($this->params()->fromQuery('redirect', '/'));

        $id = (int) $id;
        $this->service->removeArticle($id);
        $this->redirect()->toUrl($redirect);
    }

    /**
     * @param \Collection\Service\CollectionService $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }


} 