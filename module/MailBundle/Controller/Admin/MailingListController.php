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

namespace MailBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    MailBundle\Entity\Entries\Academic as AcademicEntry,
    MailBundle\Entity\Entries\External as ExternalEntry,
    MailBundle\Entity\MailingList\Named as NamedList,
    MailBundle\Entity\MailingList\AdminMap as ListAdmin,
    MailBundle\Form\Admin\MailingList\Add as AddForm,
    MailBundle\Form\Admin\MailingList\Admin as AdminForm,
    MailBundle\Form\Admin\MailingList\Entry\Academic as AcademicForm,
    MailBundle\Form\Admin\MailingList\Entry\External as ExternalForm,
    Zend\View\Model\ViewModel;

class MailingListController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $person = $this->getAuthentication()->getPersonObject();
        $editor = false;
        foreach ($person->getFlattenedRoles() as $role) {
            if ($role->getName() == 'editor') {
                $editor = true;
                break;
            }
        }

        if (!$editor) {
            $paginator = $this->paginator()->createFromArray(
                $this->getEntityManager()
                ->getRepository('MailBundle\Entity\MailingList\Named')
                ->findAllByAdmin($person),
                $this->getParam('page')
            );
        } else {
            $paginator = $this->paginator()->createFromArray(
                $this->getEntityManager()
                ->getRepository('MailBundle\Entity\MailingList\Named')
                ->findAll(),
                $this->getParam('page')
            );
        }

        return new ViewModel(
            array(
                'person' => $person,
                'entityManager' => $this->getEntityManager(),
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $list = new NamedList($formData['name']);
                $this->getEntityManager()->persist($list);

                if (!isset($formData['person_id']) || $formData['person_id'] == '') {
                    $academic = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\Users\People\Academic')
                        ->findOneByUsername($formData['person_name']);
                } else {
                    $academic = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\Users\People\Academic')
                        ->findOneById($formData['person_id']);
                }

                $admin = new ListAdmin($list, $academic, true);
                $this->getEntityManager()->persist($admin);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCES',
                        'The list was succesfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'mail_admin_list',
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

    public function entriesAction()
    {
        if(!($list = $this->_getList()))
            return new ViewModel();

        if (!$this->_checkAccess($list, false))
            return new ViewModel();

        $externalForm = new ExternalForm($this->getEntityManager());
        $academicForm = new AcademicForm($this->getEntityManager());

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if (isset($formData['first_name'], $formData['last_name'], $formData['email'])) {
                $externalForm->setData($formData);
                $form = $externalForm;
            } else {
                $academicForm->setData($formData);
                $form = $academicForm;
            }

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $entry = null;
                if (isset($formData['first_name'], $formData['last_name'], $formData['email'])) {
                    $repositoryCheck = $this->getEntityManager()
                        ->getRepository('MailBundle\Entity\Entries\External')
                        ->findOneBy(
                            array(
                                'list' => $list,
                                'email' => $formData['email']
                            )
                        );

                    if (null !== $repositoryCheck) {
                        $this->flashMessenger()->addMessage(
                            new FlashMessage(
                                FlashMessage::ERROR,
                                'SUCCES',
                                'This external address already has been subscribed to this list!'
                            )
                        );
                    } else {
                        $entry = new ExternalEntry(
                            $list,
                            $formData['first_name'],
                            $formData['last_name'],
                            $formData['email']
                        );
                    }
                } else {
                    if (!isset($formData['person_id']) || $formData['person_id'] == '') {
                        $academic = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\Users\People\Academic')
                            ->findOneByUsername($formData['person_name']);
                    } else {
                        $academic = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\Users\People\Academic')
                            ->findOneById($formData['person_id']);
                    }

                    $repositoryCheck = $this->getEntityManager()
                        ->getRepository('MailBundle\Entity\Entries\Academic')
                        ->findOneBy(
                            array(
                                'list' => $list,
                                'academic' => $academic
                            )
                        );

                    if (null !== $repositoryCheck) {
                        $this->flashMessenger()->addMessage(
                            new FlashMessage(
                                FlashMessage::ERROR,
                                'SUCCES',
                                'This member already has been subscribed to this list!'
                            )
                        );
                    } else {
                        $entry = new AcademicEntry($list, $academic);
                    }
                }

                if (null !== $entry) {
                    $this->getEntityManager()->persist($entry);
                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'SUCCES',
                            'The entry was succesfully created!'
                        )
                    );
                }

                $this->redirect()->toRoute(
                    'mail_admin_list',
                    array(
                        'action' => 'entries',
                        'id' => $list->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        $entries = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\Entry')
            ->findByList($list);

        return new ViewModel(
            array(
                'list' => $list,
                'externalForm' => $externalForm,
                'academicForm' => $academicForm,
                'entries' => $entries,
            )
        );
    }

    public function adminsAction()
    {
        if(!($list = $this->_getList()))
            return new ViewModel();

        if (!$this->_checkAccess($list, true))
            return new ViewModel();

        $form = new AdminForm($this->getEntityManager());

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $entry = null;

                if (!isset($formData['person_id']) || $formData['person_id'] == '') {
                    $academic = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\Users\People\Academic')
                        ->findOneByUsername($formData['person_name']);
                } else {
                    $academic = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\Users\People\Academic')
                        ->findOneById($formData['person_id']);
                }

                $repositoryCheck = $this->getEntityManager()
                    ->getRepository('MailBundle\Entity\MailingList\AdminMap')
                    ->findOneBy(
                        array(
                            'list' => $list,
                            'academic' => $academic
                        )
                    );

                if (null !== $repositoryCheck) {
                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::ERROR,
                            'ERROR',
                            'This member already has admin rights to this list!'
                        )
                    );
                } else {
                    $admin = new ListAdmin($list, $academic, $formData['edit_admin']);

                    $this->getEntityManager()->persist($admin);
                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'SUCCES',
                            'The admin was succesfully added!'
                        )
                    );
                }

                $this->redirect()->toRoute(
                    'mail_admin_list',
                    array(
                        'action' => 'admins',
                        'id' => $list->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        $admins = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\MailingList\AdminMap')
            ->findByList($list);

        return new ViewModel(
            array(
                'list' => $list,
                'form' => $form,
                'admins' => $admins,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($list = $this->_getList()))
            return new ViewModel();

        if (!$this->_checkAccess($list, false))
            return new ViewModel();

        $this->getEntityManager()->remove($list);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
            )
        );
    }

    public function deleteEntryAction()
    {
        $this->initAjax();

        if (!($entry = $this->_getEntry()))
            return new ViewModel();

        if (!$this->_checkAccess($entry->getList(), false))
            return new ViewModel();

        $this->getEntityManager()->remove($entry);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
            )
        );
    }

    public function deleteAdminAction()
    {
        $this->initAjax();

        if (!($admin = $this->_getAdmin()))
            return new ViewModel();

        if (!$this->_checkAccess($admin->getList(), true))
            return new ViewModel();

        $this->getEntityManager()->remove($admin);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
            )
        );
    }

    private function _getList()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the list!'
                )
            );

            $this->redirect()->toRoute(
                'mail_admin_list',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $list = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\MailingList')
            ->findOneById($this->getParam('id'));

        if (null === $list) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No list with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'mail_admin_list',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $list;
    }

    private function _getEntry()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the entry!'
                )
            );

            $this->redirect()->toRoute(
                'mail_admin_list',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $entry = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\Entry')
            ->findOneById($this->getParam('id'));

        if (null === $entry) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No entry with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'mail_admin_list',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $entry;
    }

    private function _getAdmin()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the admin!'
                )
            );

            $this->redirect()->toRoute(
                'mail_admin_list',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $admin = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\MailingList\AdminMap')
            ->findOneById($this->getParam('id'));

        if (null === $admin) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No admin with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'mail_admin_list',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $admin;
    }

    private function _checkAccess($list, $adminEdit) {
        $person = $this->getAuthentication()->getPersonObject();
        if (!$list->canBeEditedBy($person, $this->getEntityManager(), $adminEdit)) {

            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'You don\'t have access to manage the admins for the given list!'
                )
            );

            $this->redirect()->toRoute(
                'mail_admin_list',
                array(
                    'action' => 'manage'
                )
            );

            return false;
        }

        return true;
    }
}
