<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel,
    Zend\Paginator\Paginator;
use Pubmed\Service\PubmedService,
    Pubmed\Pagination\ArticlesAdapter as Adapter;


class IndexController extends AbstractActionController
{
    /** @var PubmedService */
    protected $pubmedService;

    public function indexAction()
    {
    }

    public function searchAction()
    {
        $term = $this->params()->fromRoute('term', '');
        if('' == $term) {
            $term = $this->params()->fromQuery('term', '');
        }
        $page = $this->params('page', 1);
        if($term === '') {
            $this->redirect()->toRoute('home');
        }

        $this->layout()->setVariable('term', $term);
        $ids = $this->pubmedService->search($term, $page);
        if(null === $ids) {
            throw new \RuntimeException('Pubmed server error');
        }
        $incomplete = false;
        $result = $this->pubmedService->fetchArticlesByIndexerIds($ids, $incomplete);
        $viewModel = new ViewModel();
        $viewModel->setVariable('term', $term);

        if(null === $result) {
            throw new \RuntimeException('Pubmed server error');
        }
        if(empty($result)) {
            $viewModel->setTemplate('application/index/search-empty');
        }
        if($incomplete) {
            /** Display results incomplete page */
        }
        if(count($result) === 1) {
            reset($result);
            $viewModel->setTemplate('application/index/search-single');
            $viewModel->setVariable('result', current($result));
        }
        if(count($result) > 1) {
            $adapter = new Adapter($result, $this->pubmedService->getLastSearchCount());
            $paginator = new Paginator($adapter);
            $paginator->setCurrentPageNumber($page);
            $paginator->setPageRange(10);
            $viewModel->setVariable('paginator', $paginator);
        }
        return $viewModel;
    }

    /**
     * @param PubmedService $pubmedService
     */
    public function setPubmedService(PubmedService $pubmedService)
    {
        $this->pubmedService = $pubmedService;
    }

}
