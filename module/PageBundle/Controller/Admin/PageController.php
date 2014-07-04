<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace PageBundle\Controller\Admin;

use PageBundle\Entity\Node\Page,
    PageBundle\Entity\Node\Translation,
    PageBundle\Form\Admin\Page\Add as AddForm,
    PageBundle\Form\Admin\Page\Edit as EditForm,
    Zend\File\Transfer\Adapter\Http as FileUpload,
    Zend\View\Model\ViewModel;

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
        if (null !== $this->getParam('field'))
            $pages = $this->_search();

        if (!isset($pages)) {
            $pages = $this->getEntityManager()
                ->getRepository('PageBundle\Entity\Node\Page')
                ->findBy(
                    array(
                        'endTime' => null
                    ),
                    array(
                        'name' => 'ASC'
                    )
                );
        }

        foreach ($pages as $key => $page) {
            if (!$page->canBeEditedBy($this->getAuthentication()->getPersonObject()))
                unset($pages[$key]);
        }

        $paginator = $this->paginator()->createFromArray(
            $pages,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(false),
            )
        );
    }

    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $fallbackLanguage = \Locale::getDefault();

                $category = $this->getEntityManager()
                    ->getRepository('PageBundle\Entity\Category')
                    ->findOneById($formData['category']);

                $editRoles = array();
                if (isset($formData['edit_roles'])) {
                    foreach ($formData['edit_roles'] as $editRole) {
                        $editRoles[] = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\Acl\Role')
                            ->findOneByName($editRole);
                    }
                }

                $page = new Page(
                    $this->getAuthentication()->getPersonObject(),
                    $formData['title_' . $fallbackLanguage],
                    $category,
                    $editRoles
                );

                if ('' != $formData['parent_' . $category->getId()]) {
                    $parent = $this->getEntityManager()
                        ->getRepository('PageBundle\Entity\Node\Page')
                        ->findOneById($formData['parent_' . $category->getId()]);

                    $page->setParent($parent);
                }

                $this->getEntityManager()->persist($page);

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                foreach ($languages as $language) {
                    if ('' != $formData['title_' . $language->getAbbrev()] && '' != $formData['content_' . $language->getAbbrev()]) {
                        $translation = new Translation(
                            $page,
                            $language,
                            $formData['title_' . $language->getAbbrev()],
                            $formData['content_' . $language->getAbbrev()]
                        );

                        $this->getEntityManager()->persist($translation);
                    }
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The page was successfully added!'
                );

                $this->redirect()->toRoute(
                    'page_admin_page',
                    array(
                        'action' => 'manage'
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
        if (!($page = $this->_getPage()))
            return new ViewModel();

        if (null !== $page->getEndTime()) {
            $activeVersion = $this->getEntityManager()
                ->getRepository('PageBundle\Entity\Node\Page')
                ->findOneByName($page->getName());

            $this->redirect()->toRoute(
                    'page_admin_page',
                    array(
                        'action' => 'edit',
                        'id'     => $activeVersion->getId()
                    )
                );
        }

        $form = new EditForm($this->getEntityManager(), $page);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $page->close();

                $category = $this->getEntityManager()
                    ->getRepository('PageBundle\Entity\Category')
                    ->findOneById($formData['category']);

                $editRoles = array();
                if (isset($formData['edit_roles'])) {
                    foreach ($formData['edit_roles'] as $editRole) {
                        $editRoles[] = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\Acl\Role')
                            ->findOneByName($editRole);
                    }
                }

                $newPage = new Page(
                    $this->getAuthentication()->getPersonObject(),
                    $page->getName(),
                    $category,
                    $editRoles
                );

                if ('' != $formData['parent_' . $category->getId()]) {
                    $parent = $this->getEntityManager()
                        ->getRepository('PageBundle\Entity\Node\Page')
                        ->findOneById($formData['parent_' . $category->getId()]);

                    $newPage->setParent($parent);
                }

                $orphanedPages = $this->getEntityManager()
                        ->getRepository('PageBundle\Entity\Node\Page')
                        ->findByParent($page->getId());

                foreach ($orphanedPages as $orphanedPage) {
                    $orphanedPage->setParent($newPage);
                }

                $orphanedCategories = $this->getEntityManager()
                        ->getRepository('PageBundle\Entity\Category')
                        ->findByParent($page->getId());

                foreach ($orphanedCategories as $orphanedCategory) {
                    $orphanedCategory->setParent($newPage);
                }

                $orphanedLinks = $this->getEntityManager()
                        ->getRepository('PageBundle\Entity\Link')
                        ->findByParent($page->getId());

                foreach ($orphanedLinks as $orphanedLink) {
                    $orphanedLink->setParent($newPage);
                }

                $this->getEntityManager()->persist($newPage);

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                foreach ($languages as $language) {
                    if ('' != $formData['title_' . $language->getAbbrev()] && '' != $formData['content_' . $language->getAbbrev()]) {
                        $translation = new Translation(
                            $newPage,
                            $language,
                            $formData['title_' . $language->getAbbrev()],
                            $formData['content_' . $language->getAbbrev()]
                        );

                        $this->getEntityManager()->persist($translation);
                    }
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The page was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'page_admin_page',
                    array(
                        'action' => 'manage'
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($page = $this->_getPage()))
            return;

        $page->close();

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                )
            )
        );
    }

    public function uploadAction()
    {
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            $upload = new FileUpload();

            if ('image' == $formData['type'])
                $upload->addValidator(new IsImageValidator(array('image/jpeg', 'image/jpg', 'image/pjpeg', 'image/png', 'image/gif')));

            if ($upload->isValid()) {
                $filePath = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('page.file_path') . '/';

                do {
                    $fileName = sha1(uniqid());
                } while (file_exists($filePath . $fileName));

                $upload->addFilter('Rename', $filePath . $fileName);
                $upload->receive();

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
                        )
                    )
                );
            }
        }

        return new ViewModel();
    }

    public function searchAction()
    {
        $this->initAjax();

        $pages = $this->_search();

        foreach ($pages as $key => $page) {
            if (!$page->canBeEditedBy($this->getAuthentication()->getPersonObject()))
                unset($pages[$key]);
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

    private function _search()
    {
        switch ($this->getParam('field')) {
            case 'title':
                return $this->getEntityManager()
                    ->getRepository('PageBundle\Entity\Node\Page')
                    ->findAllByTitle($this->getParam('string'));
        }
    }

    private function _getPage()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the page!'
            );

            $this->redirect()->toRoute(
                'page_admin_page',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $page = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Node\Page')
            ->findOneById($this->getParam('id'));

        if (null === $page) {
            $this->flashMessenger()->error(
                'Error',
                'No page with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'page_admin_page',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        if (!$page->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
            $this->flashMessenger()->error(
                'Error',
                'You do not have the permissions to modify this page!'
            );

            $this->redirect()->toRoute(
                'page_admin_page',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $page;
    }
}
