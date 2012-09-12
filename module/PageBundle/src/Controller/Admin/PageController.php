<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace PageBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    PageBundle\Entity\Nodes\Page,
    PageBundle\Entity\Nodes\Translation,
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
        $paginator = $this->paginator()->createFromEntity(
            'PageBundle\Entity\Nodes\Page',
            $this->getParam('page'),
            array(
                'endTime' => null
            ),
            array(
                'name' => 'ASC'
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
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid($formData)) {
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
                'form' => $form,
                'uploadProgressName' => ini_get('session.upload_progress.name'),
                'uploadProgressId' => uniqid(),
            )
        );
    }

    public function editAction()
    {
        if (!($page = $this->_getPage()))
            return new ViewModel();

        $form = new EditForm($this->getEntityManager(), $page);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
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

    public function uploadAction()
    {
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if (!(in_array($_FILES['file']['type'], array('image/jpeg', 'image/jpg', 'image/pjpeg', 'image/png', 'image/gif')) && $_POST['type'] == 'image') &&
                    $_POST['type'] !== 'file') {
                return new ViewModel();
            }

            $filePath = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('page.file_path') . '/';

            $upload = new FileUpload();

            $fileName = '';
            do{
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

    public function uploadProgressAction()
    {
        $uploadId = ini_get('session.upload_progress.prefix') . $this->getRequest()->getPost()->get('upload_id');

        return new ViewModel(
            array(
                'result' => isset($_SESSION[$uploadId]) ? $_SESSION[$uploadId] : '',
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
                    'No ID was given to identify the page!'
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
                    'No page with the given ID was found!'
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
