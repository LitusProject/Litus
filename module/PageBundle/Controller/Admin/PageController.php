<?php

namespace PageBundle\Controller\Admin;

use CommonBundle\Entity\General\Node\FAQ\FAQPageMap;
use Laminas\Filter\File\RenameUpload as RenameUploadFilter;
use Laminas\Validator\File\IsImage as IsImageValidator;
use Laminas\Validator\File\UploadFile as UploadFileValidator;
use Laminas\Validator\ValidatorChain;
use Laminas\View\Model\ViewModel;
use PageBundle\Entity\Node\Page;

/**
 * PageController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class PageController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $pages = array();
        if ($this->getParam('field') !== null) {
            $pages = $this->search();
        }

        if (count($pages) == 0) {
            $pages = $this->getEntityManager()
                ->getRepository('PageBundle\Entity\Node\Page')
                ->findBy(
                    array(
                        'endTime' => null,
                    ),
                    array(
                        'name' => 'ASC',
                    )
                );
        }

        foreach ($pages as $key => $page) {
            if (!$page->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
                unset($pages[$key]);
            }
        }

        $paginator = $this->paginator()->createFromArray(
            $pages,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(false),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('page_page_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $page = $form->hydrateObject();

                $this->getEntityManager()->persist($page);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The page was successfully added!'
                );

                $this->redirect()->toRoute(
                    'page_admin_page',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        $page = $this->getPageEntity();
        if ($page === null) {
            return new ViewModel();
        }

        if ($page->getEndTime() !== null) {
            $activeVersion = $this->getEntityManager()
                ->getRepository('PageBundle\Entity\Node\Page')
                ->findOneByName($page->getName());

            $this->redirect()->toRoute(
                'page_admin_page',
                array(
                    'action' => 'edit',
                    'id'     => $activeVersion->getId(),
                )
            );
        }

        $maps = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Node\FAQ\FAQPageMap')
            ->findAllByPageQuery($page)->getResult();

        $form = $this->getForm('page_page_edit', array('page' => $page));
        $faqForm = $this->getForm('page_page_FAQ', array('page' => $page));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);
            $faqForm->setData($formData);

            if (isset($formData['page_edit']) && $faqForm->isValid()) {
                error_log('hereeeeeeeeeeeee!');
                $faq = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Node\FAQ\FAQ')
                    ->findOneById(intval($formData['faq_typeahead']['id']));

                $map = new FAQPageMap($faq, $page);
                $this->getEntityManager()->persist($map);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The faq was successfully added!'
                );

                $this->redirect()->toRoute(
                    'page_admin_page',
                    array(
                        'action' => 'edit',
                        'id'     => $page->getId(),
                    )
                );
                return new ViewModel();
            } elseif (isset($formData['page_edit']) && $form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The page was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'page_admin_page',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'    => $form,
                'faqForm' => $faqForm,
                'maps'    => $maps,
                'page'    => $page,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $page = $this->getPageEntity();
        if ($page === null) {
            return new ViewModel();
        }

        $maps = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Node\FAQ\FAQPageMap')
            ->findAllByPageQuery($page)->getResult();

        foreach ($maps as $map) {
            $this->getEntityManager()->remove($map);
        }

        $page->close();

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    public function uploadAction()
    {
        if ($this->getRequest()->isPost()) {
            $form = $this->getRequest()->getPost();

            $validatorChain = new ValidatorChain();
            $validatorChain->attach(new UploadFileValidator());
            if ($form['type'] == 'image') {
                $validatorChain->attach(
                    new IsImageValidator(
                        array('image/gif', 'image/jpeg', 'image/png')
                    )
                );
            }

            $file = $this->getRequest()->getFiles()['file'];
            if ($validatorChain->isValid($file)) {
                $filePath = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('page.file_path') . '/';

                do {
                    $fileName = sha1(uniqid());
                } while (file_exists($filePath . $fileName));

                $renameUploadFilter = new RenameUploadFilter();
                $renameUploadFilter->setTarget($filePath . $fileName)
                    ->filter($file);

                $url = $this->url()->fromRoute(
                    'page_file',
                    array(
                        'name' => $fileName,
                    )
                );

                return new ViewModel(
                    array(
                        'result' => array(
                            'name' => $url,
                        ),
                    )
                );
            }
        }

        return new ViewModel();
    }

    public function searchAction()
    {
        $this->initAjax();

        $pages = $this->search();

        foreach ($pages as $key => $page) {
            if (!$page->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
                unset($pages[$key]);
            }
        }

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($pages, $numResults);

        $result = array();
        foreach ($pages as $page) {
            $item = (object) array();
            $item->id = $page->getId();
            $item->title = $page->getTitle($this->getLanguage());
            $item->category = $page->getCategory() ? $page->getCategory()->getName($this->getLanguage()) : '';
            $item->parent = $page->getParent() ? $page->getParent()->getTitle($this->getLanguage()) : '';
            $item->author = $page->getCreationPerson()->getFullName();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function typeaheadAction()
    {
        $this->initAjax();

        $pages = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Node\Page')
            ->findAllByTitleQuery($this->getParam('string'))
            ->setMaxResults(15)
            ->getResult();

        $result = array();
        foreach ($pages as $page) {
            $item = (object) array();
            $item->id = $page->getId();
            $item->title = $page->getName();
            $item->category = $page->getCategory()->getName();
            $item->value = $page->getCategory()->getName() . ' - ' . $page->getTitle();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @return array
     */
    private function search()
    {
        switch ($this->getParam('field')) {
            case 'title':
                return $this->getEntityManager()
                    ->getRepository('PageBundle\Entity\Node\Page')
                    ->findAllByTitle($this->getParam('string'));
        }

        return array();
    }

    /**
     * @return Page|null
     */
    private function getPageEntity()
    {
        $page = $this->getEntityById('PageBundle\Entity\Node\Page');

        if (!($page instanceof Page) || !$page->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
            $this->flashMessenger()->error(
                'Error',
                'No page was found!'
            );

            $this->redirect()->toRoute(
                'page_admin_page',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $page;
    }
}
