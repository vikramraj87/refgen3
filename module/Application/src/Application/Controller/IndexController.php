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
use Zend\View\Model\ViewModel;
use Pubmed\Service\PubmedService;


class IndexController extends AbstractActionController
{
    /** @var PubmedService */
    protected $pubmedService;

    public function indexAction()
    {

    }

    public function searchAction()
    {

    }

    /**
     * @param PubmedService $pubmedService
     */
    public function setPubmedService(PubmedService $pubmedService)
    {
        $this->pubmedService = $pubmedService;
    }

}
