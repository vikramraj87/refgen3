<?php
namespace Collection\View\Helper;

use Zend\View\Helper\AbstractHelper,
    Zend\Paginator\Adapter\ArrayAdapter;
use Collection\Service\CollectionService;
class ActiveCollectionHelper extends AbstractHelper
{
    /** @var CollectionService */
    private $service;

    public function __construct(CollectionService $service)
    {
        $this->service = $service;
    }

    public function __invoke()
    {
        return $this;
    }

    public function render()
    {
        return $this->getView()->partial('partials/active-collection', array('service' => $this->service));
    }
}