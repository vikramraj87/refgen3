<?php
namespace Article\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Article\Entity\Article,
    Article\Table\ArticleTable;
use Zend\View\Model\ViewModel;

class ArticleController extends AbstractActionController
{
    /** @var ArticleTable */
    private $table;

    public function indexAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $article = $this->table->fetchArticleById($id);
        if(null === $article) {
            $this->getResponse()->setStatusCode(404);
        }
        $view = new ViewModel();
        $view->setVariable('result', $article);
        return $view;
    }

    /**
     * @param \Article\Table\ArticleTable $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }


} 