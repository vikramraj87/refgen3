<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use User\Table\UserTable,
    Collection\Table\CollectionTable,
    Troubleshooting\Table\ExceptionTable,
    Article\Table\ArticleTable;

class AdminController extends AbstractActionController
{
    /** @var UserTable */
    private $userTable;

    /** @var CollectionTable */
    private $collectionTable;

    /** @var ExceptionTable */
    private $exceptionTable;

    /** @var ArticleTable */
    private $articleTable;

    public function indexAction()
    {
        $totalUsers = $this->userTable->getTotalCount();
        $totalCollections = $this->collectionTable->getTotalCount();
        $totalArticles = $this->articleTable->getTotalCount();
        $totalExceptions = $this->exceptionTable->getTotalCount();
        return array(
            'totalUsers' => $totalUsers,
            'totalCollections' => $totalCollections,
            'totalArticles' => $totalArticles,
            'totalExceptions' => $totalExceptions
        );
    }

    public function usersAction()
    {
        $users = $this->userTable->fetchAllUsers();
        $collectionCounts = $this->collectionTable->fetchCollectionCountsByUser();
        $data = \Zend\Stdlib\ArrayUtils::merge($users, $collectionCounts, true);
        return array(
            'data' => $data
        );
    }

    public function exceptionsAction()
    {
        $exceptions = $this->exceptionTable->fetchAllExceptions();
        return array(
            'exceptions' => $exceptions
        );
    }

    /**
     * @param \Collection\Table\CollectionTable $collectionTable
     */
    public function setCollectionTable(CollectionTable $collectionTable)
    {
        $this->collectionTable = $collectionTable;
    }

    /**
     * @param \Troubleshooting\Table\ExceptionTable $exceptionTable
     */
    public function setExceptionTable(ExceptionTable $exceptionTable)
    {
        $this->exceptionTable = $exceptionTable;
    }

    /**
     * @param \User\Table\UserTable $userTable
     */
    public function setUserTable(UserTable $userTable)
    {
        $this->userTable = $userTable;
    }

    /**
     * @param \Article\Table\ArticleTable $articleTable
     */
    public function setArticleTable(ArticleTable $articleTable)
    {
        $this->articleTable = $articleTable;
    }


} 