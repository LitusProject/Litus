<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace PageBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    PageBundle\Entity\Nodes\Page,
    PageBundle\Entity\Nodes\Translation,
    PageBundle\Form\Admin\Page\Add as AddForm,
    PageBundle\Form\Admin\Page\Edit as EditForm,
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
        $paginator = $this->paginator()->createFromEntity(
            'PageBundle\Entity\Nodes\Page',
            $this->getParam('page'),
            array(
                'endTime' => null
            )
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();

            if ($form->isValid($formData)) {
                $fallbackLanguage = \Zend\Registry::get('Litus_Localization_FallbackLanguage');

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
                    $formData['title_' . $fallbackLanguage->getAbbrev()],
                    $category,
                    $editRoles
                );

                if ('' != $formData['parent']) {
                    $parent = $this->getEntityManager()
                        ->getRepository('PageBundle\Entity\Nodes\Page')
                        ->findOneById($formData['parent']);

                    $page->setParent($parent);
                }

                $this->getEntityManager()->persist($page);

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                foreach($languages as $language) {
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

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The page was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_page',
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

    public function editAction()
    {
        if (!($page = $this->_getPage()))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $page);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->post()->toArray();

            if ($form->isValid($formData)) {
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

                if ('' != $formData['parent']) {
                    $parent = $this->getEntityManager()
                        ->getRepository('PageBundle\Entity\Nodes\Page')
                        ->findOneById($formData['parent']);

                    $newPage->setParent($parent);
                }

                $orphanedPages = $this->getEntityManager()
                        ->getRepository('PageBundle\Entity\Nodes\Page')
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

                $this->getEntityManager()->persist($newPage);

                $languages = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Language')
                    ->findAll();

                foreach($languages as $language) {
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

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The page was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_page',
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

    private function _getPage()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No id was given to identify the page!'
                )
            );

            $this->redirect()->toRoute(
                'admin_page',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $page = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Nodes\Page')
            ->findOneById($this->getParam('id'));

        if (null === $page) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No page with the given id was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_page',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        if (!$page->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'You do not have the permissions to modify this page!'
                )
            );

            $this->redirect()->toRoute(
                'admin_page',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $page;
    }
}
