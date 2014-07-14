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

namespace MailBundle\Controller\Admin;

use MailBundle\Entity\MailingList\Entry\MailingList as MailingListEntry,
    MailBundle\Entity\MailingList\Entry\Person\Academic as AcademicEntry,
    MailBundle\Entity\MailingList\Entry\Person\External as ExternalEntry,
    MailBundle\Entity\MailingList\AdminMap as ListAdmin,
    MailBundle\Entity\MailingList\AdminRoleMap as ListAdminRole,
    Zend\View\Model\ViewModel;

/**
 * MailingListController
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class MailingListController extends \MailBundle\Component\Controller\AdminController
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
        $form = $this->getForm('mail_mailinglist_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $list = $form->hydrateObject();
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The list was succesfully created!'
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

        $academicForm = $this->getForm('mail_mailinglist_entry_person_academic', array('list' => $list));
        $externalForm = $this->getForm('mail_mailinglist_entry_person_external', array('list' => $list));
        $mailingListForm = $this->getForm('mail_mailinglist_entry_mailinglist', array('person' => $this->getAuthentication()->getPersonObject(), 'list' => $list));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $academicForm->setData($formData);
            $externalForm->setData($formData);
            $mailingListForm->setData($formData);

            $entry = null;
            if (isset($formData['academic_add']) && $academicForm->isValid()) {
                $entry = $academicForm->hydrateObject(
                    new AcademicEntry($list)
                );
            } elseif (isset($formData['external_add']) && $externalForm->isValid()) {
                $entry = $externalForm->hydrateObject(
                    new ExternalEntry($list)
                );
            } elseif (isset($formData['list_add']) && $mailingListForm->isValid()) {
                $entry = $mailingListForm->hydrateObject(
                    new MailingListEntry($list)
                );
            }

            if (null !== $entry) {
                $this->getEntityManager()->persist($entry);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The entry was succesfully created!'
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
        if (!($list = $this->_getList()))
            return new ViewModel();

        if (!$this->_checkAccess($list, true))
            return new ViewModel();

        $adminForm = $this->getForm('mail_mailinglist_admin', array('list' => $list));
        $adminRoleForm = $this->getForm('mail_mailinglist_adminrole', array('list' => $list));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $adminForm->setData($formData);
            $adminRoleForm->setData($formData);

            if (isset($formData['admin_map']) && $adminForm->isValid()) {
                $admin = $adminForm->hydrateObject(
                    new ListAdmin($list)
                );

                $this->getEntityManager()->persist($admin);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The admin was succesfully added!'
                );

                $this->redirect()->toRoute(
                    'mail_admin_list',
                    array(
                        'action' => 'admins',
                        'id' => $list->getId(),
                    )
                );

                return new ViewModel();
            }

            if (isset($formData['admin_role']) && $adminRoleForm->isValid()) {
                $adminRole = $adminRoleForm->hydrateObject(
                    new ListAdminRole($list)
                );

                $this->getEntityManager()->persist($adminRole);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The admin role was succesfully added!'
                );

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

        $this->flashMessenger()->success(
            'Success',
            'All entries were removed from the list.'
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
        foreach ($lists as $list) {
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
    * @return \Doctrine\ORM\Query|null
    */
    private function _search()
    {
        switch ($this->getParam('field')) {
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('MailBundle\Entity\MailingList')
                    ->findAllByNameQuery($this->getParam('string'));
        }
    }

    private function _getList()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the list!'
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
            $this->flashMessenger()->error(
                'Error',
                'No list with the given ID was found!'
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
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the entry!'
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
            $this->flashMessenger()->error(
                'Error',
                'No entry with the given ID was found!'
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
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the admin!'
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
            $this->flashMessenger()->error(
                'Error',
                'No admin with the given ID was found!'
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
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the admin role!'
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
            $this->flashMessenger()->error(
                'Error',
                'No admin role with the given ID was found!'
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

    /**
     * @param boolean $adminEdit
     */
    private function _checkAccess($list, $adminEdit)
    {
        $person = $this->getAuthentication()->getPersonObject();
        if (!$list->canBeEditedBy($person, $adminEdit)) {
            $this->flashMessenger()->error(
                'Error',
                'You don\'t have access to manage the admins for the given list!'
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
