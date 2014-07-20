<?php
/**
 * Created by PhpStorm.
 * User: vikramraj
 * Date: 14/07/14
 * Time: 4:28 PM
 */

namespace Collection\Controller;

use Collection\Table\CollectionTable,
    Collection\Entity\Collection,
    Collection\Service\CollectionService,
    Authentication\Service\AuthenticationService,
    Article\View\Helper\VancouverHelper;
use PhpOffice\PhpWord\PhpWord,
    PhpOffice\PhpWord\IOFactory;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\InputFilter\InputFilter,
    Zend\InputFilter\Input,
    Zend\Validator\StringLength,
    Zend\Validator\Regex,
    Zend\Filter\StringTrim,
    Zend\Filter\StripTags,
    Zend\Validator\NotEmpty;


class CollectionController extends AbstractActionController
{
    /** @var CollectionTable */
    private $collectionTable;

    /** @var CollectionService */
    private $collectionService;

    /** @var AuthenticationService */
    private $authService;

    public function newAction()
    {
        $redirect = urldecode($this->params()->fromQuery('redirect', '/'));
        $this->collectionService->setCollection(null);
        return $this->redirect()->toUrl($redirect);
    }

    public function listAction()
    {
        $authService = $this->getServiceLocator()->get('Authentication\Service\Authentication');
        $userId = $authService->getIdentity()->getId();
        $redirect = urldecode($this->params()->fromQuery('redirect', '/'));
        return array(
            'collections' => $this->collectionTable->fetchEntireByUserId($userId),
            'redirect'    => $redirect
        );
    }

    public function openAction()
    {
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id', 0);
        if(0 == $id) {
            $id = $this->params()->fromQuery('id');
        }
        $collection = $this->collectionTable->fetchCollectionById(
            $id,
            $this->authService->getIdentity()->getId()
        );
        if(null == $collection) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        $this->collectionService->setOpenCollectionId($collection->getId());
        return array(
            'collection' => $collection
        );
    }

    public function setActiveAction()
    {
        $redirect = urldecode($this->params()->fromQuery('redirect', '/'));
        $id = $this->params()->fromRoute('id', 0);
        if($id) {
            $collection = $this->collectionTable->fetchCollectionById(
                $id,
                $this->authService->getIdentity()->getId()
            );
            if($collection instanceof Collection) {
                $this->collectionService->setCollection($collection);
            }
        }
        return $this->redirect()->toUrl($redirect);
    }

    public function saveAction()
    {
        $redirect = urldecode($this->params()->fromQuery('redirect', '/'));
        $request = $this->getRequest();
        if($request->isPost()) {
            $name = $this->params()->fromPost('txt-collection-name');
            $nameFilter = new Input('collection-name');
            $nameFilter->getFilterChain()
                 ->attach(new StringTrim())
                 ->attach(new StripTags());

            $notEmpty = new NotEmpty();
            $notEmpty->setMessage('Collection name cannot be empty', NotEmpty::IS_EMPTY);

            $strLenValidator = new StringLength();
            $strLenValidator->setMin(3);
            $strLenValidator->setMax(60);
            $strLenValidator->setMessages(array(
                    StringLength::TOO_SHORT => 'Collection name must be atleast 3 characters long',
                    StringLength::TOO_LONG  => 'Collection name cannot be more than 60 characters long'
                )
            );
            $alnum = new Regex(
                array('pattern' => '/^[a-zA-Z0-9 ]+$/')
            );
            $alnum->setMessage('Collection name contain only alphabets, numbers and spaces.', Regex::NOT_MATCH);

            $nameFilter->getValidatorChain()
                 ->attach($notEmpty, true)
                 ->attach($strLenValidator, true)
                 ->attach($alnum);
            $nameFilter->setValue($name);
            if($nameFilter->isValid()) {
                $collection = $this->collectionService->getActiveCollection();
                $collection->setName($nameFilter->getValue());

                $authService = $this->getServiceLocator()->get('Authentication\Service\Authentication');
                $userId = $authService->getIdentity()->getId();
                $checkCollection = $this->collectionTable->select(array(
                        'user_id' => $userId,
                        'name'    => $collection->getName()
                    )
                )->current();


                if(0 == $collection->getId()) {
                    if(false != $checkCollection) {
                        $this->flashMessenger()->addErrorMessage('Collection with name ' . $collection->getName() . ' already exists');
                    } else {
                        $result = $this->collectionTable->saveCollection($collection, $userId);
                        if($result instanceof Collection) {
                            $this->collectionService->setCollection($result);
                            $this->flashMessenger()->addSuccessMessage('Collection ' . $result->getName() . ' successfully created');
                        } else {
                            $this->flashMessenger()->addErrorMessage('Error saving collection. Please try again later');
                        }
                    }
                } else {
                    if(false != $checkCollection && $collection->getId() != $checkCollection['id']) {
                        $this->flashMessenger()->addErrorMessage('Collection with name ' . $collection->getName() . ' already exists');
                    } else {
                        $result = $this->collectionTable->updateCollection($collection);
                        if($result instanceof Collection) {
                            $this->collectionService->setCollection($result);
                            $this->flashMessenger()->addSuccessMessage('Collection ' . $result->getName() . ' successfully updated');
                        } else {
                            $this->flashMessenger()->addErrorMessage('Error updating collection. Please try again later');
                        }
                    }

                }
            } else {
                $errors = $nameFilter->getMessages();
                foreach($errors as $error) {
                    $this->flashMessenger()->addErrorMessage($error);
                }
            }
        }
        return $this->redirect()->toUrl($redirect);
    }

    public function deleteAction()
    {
        $redirect = urldecode($this->params()->fromQuery('redirect', '/'));
        $this->collectionTable->deleteCollectionById(
            $this->collectionService->getActiveCollection()->getId(),
            $this->authService->getIdentity()->getId()
        );
        $this->collectionService->setCollection(null);
        return $this->redirect()->toUrl($redirect);
    }

    public function exportAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $collection = $this->collectionTable->fetchCollectionById(
            $id,
            $this->authService->getIdentity()->getId()
        );
        if(null === $collection) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        return array(
            'collection' => $collection
        );
    }

    public function downloadAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        $collection = $this->collectionTable->fetchCollectionById(
            $id,
            $this->authService->getIdentity()->getId()
        );
        if(null === $collection) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        $word = new PhpWord();
        $word->setDefaultFontName('Verdana');
        $word->setDefaultFontSize(14);

        $word->getDocumentProperties()
             ->setCreator('Kivi Refgen')
             ->setCompany('Kivi Refgen')
             ->setTitle($collection->getName())
             ->setCreated((new \DateTime())->getTimestamp());

        $section = $word->addSection();
        $section->getSettings()
                ->setPortrait()
                ->setMarginBottom(900)
                ->setMarginTop(900)
                ->setMarginRight(900)
                ->setMarginLeft(900);
        $headingStyle = array(
            'color' => 'faa900',
            'size'  => 20
        );
        $section->addText($collection->getName(), $headingStyle);

        $vh = new VancouverHelper();

        foreach($collection->getArticles() as $article) {
            $section->addListItem($vh($article), 0, null, \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER);
        }


        $file = $collection->getName() . '.docx';
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        $xmlWriter = IOFactory::createWriter($word, 'Word2007');
        ob_clean();
        $xmlWriter->save("php://output");

    }

    public function setCollectionTable(CollectionTable $collectionTable)
    {
        $this->collectionTable = $collectionTable;
    }

    /**
     * @param \Collection\Service\CollectionService $collectionService
     */
    public function setCollectionService(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    /**
     * @param mixed $authService
     */
    public function setAuthService($authService)
    {
        $this->authService = $authService;
    }


} 