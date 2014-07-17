<?php
namespace Collection\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Collection\Service\CollectionService;
use Article\View\Helper\VancouverHelper;

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

    public function exportAction()
    {
        require './vendor/PHPWord/PHPWord.php';

        $collection = $this->service->getActiveCollection();

        $phpWord = new \PHPWord();
        $phpWord->setDefaultFontName('Tahoma');
        $phpWord->setDefaultFontSize(12);

        $phpWord->getProperties()->setCreator('Kivi Refgen')
                                 ->setCompany('Kivi Refgen')
                                 ->setTitle('Collection ' . $this->service->getActiveCollection()->getName())
                                 ->setCreated((new \DateTime())->getTimestamp());
        $section = $phpWord->createSection();
        $section->getSettings()->setPortrait();
        $section->getSettings()->setMarginBottom(900);
        $section->getSettings()->setMarginTop(900);
        $section->getSettings()->setMarginRight(900);
        $section->getSettings()->setMarginLeft(900);

        $headingStyle = array(
            'color' => 'faa900',
            'size'  => 18
        );

        $section->addText('Collection - ' . $collection->getName(), $headingStyle);

        $vh = new VancouverHelper();

        foreach($collection->getArticles() as $article) {
            $section->addListItem('citation', 0, null, 7 ,null);
        }

        header('Content-Type: application/vnd.ms-word');
        header('Content-Disposition: attachment;filename="ref.docx"');
        header('Cache-Control: max-age=0');
        $writer = \PHPWord_IOFactory::createWriter($phpWord,"Word2007");
        $writer->save("php://output");


    }

    /**
     * @param \Collection\Service\CollectionService $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }


} 