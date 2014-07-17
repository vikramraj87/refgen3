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

    public function isEdited()
    {
        return $this->service->isEdited();
    }

    public function getName()
    {
        return $this->service->getActiveCollection()->getName();
    }

    public function getId()
    {
        return $this->service->getActiveCollection()->getId();
    }

    public function hasArticle($id)
    {
        return $this->service->getActiveCollection()->hasArticle($id);
    }

    public function isEmpty()
    {
        return !count($this->service->getActiveCollection());
    }
}