<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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
    MailBundle\Entity\MailingList\Entry\MailingList as MailingListEntry,
    MailBundle\Entity\MailingList\Entry\Person\Academic as AcademicEntry,
    MailBundle\Entity\MailingList\Entry\Person\External as ExternalEntry,
    MailBundle\Entity\MailingList\Named as NamedList,
    MailBundle\Entity\MailingList\AdminMap as ListAdmin,
    MailBundle\Entity\MailingList\AdminRoleMap as ListAdminRole,
    MailBundle\Form\Admin\MailingList\Add as AddForm,
    MailBundle\Form\Admin\MailingList\Admin as AdminForm,
    MailBundle\Form\Admin\MailingList\AdminRole as AdminRoleForm,
    MailBundle\Form\Admin\MailingList\Entry\MailingList as MailingListForm,
    MailBundle\Form\Admin\MailingList\Entry\Person\Academic as AcademicForm,
    MailBundle\Form\Admin\MailingList\Entry\Person\External as ExternalForm,
    Zend\View\Model\ViewModel;

/**
 * MailingListController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class MailingListController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $editor = false;
        $person = $this->getAuthentication()->getPersonObject();
        foreach ($person->getFlattenedRoles() as $role) {
            if ($role->getName() == 'editor') {
                $editor = true;
                break;
            }
        }

        if (!$editor) {
            $lists =  $this->getEntityManager()
                ->getRepository('MailBundle\Entity\MailingList\Named')
                ->findBy(array(), array('name' => 'ASC'));

            $paginatorArray = array();
            foreach ($lists as $list) {
                if ($list->canBeEditedBy($person))
                    $paginatorArray[] = $list;
            }

            $paginator = $this->paginator()->createFromArray(
                $paginatorArray,
                $this->getParam('page')
            );
        } else {
            $paginator = $this->paginator()->createFromEntity(
                'MailBundle\Entity\MailingList\Named',
                $this->getParam('page'),
                array(),
                array(
                    'name' => 'ASC'
                )
            );
        }

        return new ViewModel(
            array(
                'person' => $person,
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
                        ->getRepository('CommonBundle\Entity\User\Person\Academic')
                        ->findOneByUsername($formData['person_name']);
                } else {
                    $academic = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Person\Academic')
                        ->findOneById($formData['person_id']);
                }

                $admin = new ListAdmin($list, $academic, true);
                $this->getEntityManager()->persist($admin);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The list was succesfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'mail_admin_list',
                    array(
                        'action' => 'add',
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

        $academicForm = new AcademicForm($this->getEntityManager());
        $externalForm = new ExternalForm($this->getEntityManager());
        $mailingListForm = new MailingListForm(
            $this->getEntityManager(), $this->getAuthentication()->getPersonObject(), $list
        );

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if (isset($formData['first_name'], $formData['last_name'], $formData['email'])) {
                $externalForm->setData($formData);
                $form = $externalForm;
            } elseif (isset($formData['entry'])) {
                $mailingListForm->setData($formData);
                $form = $mailingListForm;
            } else {
                $academicForm->setData($formData);
                $form = $academicForm;
            }

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $entry = null;
                if (isset($formData['first_name'], $formData['last_name'], $formData['email'])) {
                    $repositoryCheck = $this->getEntityManager()
                        ->getRepository('MailBundle\Entity\MailingList\Entry\Person\External')
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
                                'Success',
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
                } elseif (isset($formData['entry'])) {
                    $entry = $this->getEntityManager()
                        ->getRepository('MailBundle\Entity\MailingList\Named')
                        ->findOneById($formData['entry']);

                    $repositoryCheck = $this->getEntityManager()
                        ->getRepository('MailBundle\Entity\MailingList\Entry\MailingList')
                        ->findOneBy(
                            array(
                                'list' => $list,
                                'entry' => $entry
                            )
                        );

                    if (null !== $repositoryCheck) {
                        $this->flashMessenger()->addMessage(
                            new FlashMessage(
                                FlashMessage::ERROR,
                                'Success',
                                'This list already has been subscribed to this list!'
                            )
                        );
                    } else {
                        $entry = new MailingListEntry($list, $entry);
                    }
                } else {
                    if (!isset($formData['person_id']) || $formData['person_id'] == '') {
                        $academic = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\User\Person\Academic')
                            ->findOneByUsername($formData['person_name']);
                    } else {
                        $academic = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\User\Person\Academic')
                            ->findOneById($formData['person_id']);
                    }

                    $repositoryCheck = $this->getEntityManager()
                        ->getRepository('MailBundle\Entity\MailingList\Entry\Person\Academic')
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
                                'Success',
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
                            'Success',
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
            ->getRepository('MailBundle\Entity\MailingList\Entry')
            ->findByList($list);

        return new ViewModel(
            array(
                'list' => $list,
                'academicForm' => $academicForm,
                'externalForm' => $externalForm,
                'mailingListForm' => $mailingListForm,
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

        $adminForm = new AdminForm($this->getEntityManager());
        $adminRoleForm = new AdminRoleForm($this->getEntityManager());

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $adminForm->setData($formData);
            $adminRoleForm->setData($formData);

            if ($adminForm->isValid()) {
                $formData = $adminForm->getFormData($formData);

                if (!isset($formData['person_id']) || $formData['person_id'] == '') {
                    $academic = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Person\Academic')
                        ->findOneByUsername($formData['person_name']);
                } else {
                    $academic = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Person\Academic')
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
                            'Error',
                            'This member already has admin rights on this list!'
                        )
                    );
                } else {
                    $admin = new ListAdmin($list, $academic, $formData['edit_admin']);

                    $this->getEntityManager()->persist($admin);
                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'Success',
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

            if ($adminRoleForm->isValid()) {
                $formData = $adminRoleForm->getFormData($formData);

                $role = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Acl\Role')
                    ->findOneByName($formData['role']);

                $repositoryCheck = $this->getEntityManager()
                    ->getRepository('MailBundle\Entity\MailingList\AdminRoleMap')
                    ->findOneBy(
                        array(
                            'list' => $list,
                            'role' => $role
                        )
                    );

                if (null !== $repositoryCheck) {
                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::ERROR,
                            'Error',
                            'This role already has admin rights on this list!'
                        )
                    );
                } else {
                    $adminRole = new ListAdminRole($list, $role, $formData['edit_admin']);

                    $this->getEntityManager()->persist($adminRole);
                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'Success',
                            'The admin role was succesfully added!'
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

        $adminRoles = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\MailingList\AdminRoleMap')
            ->findByList($list);

        return new ViewModel(
            array(
                'list' => $list,
                'adminForm' => $adminForm,
                'adminRoleForm' => $adminRoleForm,
                'admins' => $admins,
                'adminRoles' => $adminRoles,
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
                'result' => (object) array('status' => 'success'),
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
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function deleteAllEntriesAction()
    {
        if (!($list = $this->_getList()))
            return new ViewModel();

        $entries = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\MailingList\Entry')
            ->findByList($list);

        foreach ($entries as $entry)
            $this->getEntityManager()->remove($entry);

        $this->getEntityManager()->flush();

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'Success',
                'All entries were removed from the list.'
            )
        );

        $this->redirect()->toRoute(
            'mail_admin_list',
            array(
                'action' => 'entries',
                'id' => $list->getId(),
            )
        );

        return new ViewModel();
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
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function deleteAdminRoleAction()
    {
        $this->initAjax();

        if (!($adminRole = $this->_getAdminRole()))
            return new ViewModel();

        if (!$this->_checkAccess($adminRole->getList(), true))
            return new ViewModel();

        $this->getEntityManager()->remove($adminRole);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $lists = $this->_search()
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach($lists as $list) {
            $item = (object) array();
            $item->id = $list->getId();
            $item->name = $list->getName();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
    * @return \Doctrine\ORM\Query
    */
    private function _search()
    {
        switch($this->getParam('field')) {
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('MailBundle\Entity\MailingList')
                    ->findAllByNameQuery($this->getParam('string'));
        }
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
            ->getRepository('MailBundle\Entity\MailingList\Entry')
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

    private function _getAdminRole()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the admin role!'
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

        $adminRole = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\MailingList\AdminRoleMap')
            ->findOneById($this->getParam('id'));

        if (null === $adminRole) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No admin role with the given ID was found!'
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

        return $adminRole;
    }

    private function _checkAccess($list, $adminEdit) {
        $person = $this->getAuthentication()->getPersonObject();
        if (!$list->canBeEditedBy($person, $adminEdit)) {
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
