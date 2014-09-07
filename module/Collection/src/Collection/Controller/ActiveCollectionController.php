<?php
namespace Collection\Controller;

use Article\Entity\Article;
use Zend\Config\Writer\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Collection\Service\CollectionService;
use Article\View\Helper\VancouverHelper;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ActiveCollectionController extends AbstractActionController
{
    /** @var CollectionService */
    private $service;

    public function addAction()
    {
        $id = $this->params()->fromRoute('id', 0);
        $redirect = urldecode($this->params()->fromQuery('redirect', '/'));

        $id = (int) $id;
        $addedArticle = $this->service->addArticle($id);
        if(!$this->getRequest()->isXmlHttpRequest()) {
           return $this->redirect()->toUrl($redirect);
        }
        if($addedArticle instanceof Article) {
            return new JsonModel([
                'id' => $addedArticle->getId(),
                'title' => $addedArticle->getTitle()
            ]);
        }
        return new JsonModel([]);
    }

    public function processMultipleAction()
    {
        $selected = $this->params()->fromPost('selected');
        $action = $this->params()->fromPost('action');
        $redirect = urldecode($this->params()->fromQuery('redirect', '/'));
        if($selected) {
            switch($action) {
                case 'remove':
                    $this->service->removeArticles($selected);
                    break;
                case 'up':
                    $this->service->moveUpItems($selected);
                    break;
                case 'down':
                    $this->service->moveDownItems($selected);
                    break;
            }
        }
        return $this->redirect()->toUrl($redirect);
    }

    public function removeAction()
    {
        $ids = explode(',', $this->params()->fromRoute('ids', ''));
        $this->service->removeArticles($ids);
        return new JsonModel([]);
    }

    public function sortAction()
    {
        $ids = explode(',', $this->params()->fromRoute('ids', ''));
        $this->service->sortItems($ids);
        return new JsonModel([]);
    }

    public function renderAction()
    {
        $view = new ViewModel();
        $view->setTemplate('partials/active-collection');
        $view->setVariable('service', $this->service);
        $view->setTerminal(true);
        return $view;
    }

    /**
     * @param \Collection\Service\CollectionService $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }


} 