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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace MailBundle\Controller\Admin;

use CommonBundle\Entity\User\Person\Academic;
use MailBundle\Entity\MailingList;
use MailBundle\Entity\MailingList\AdminMap as ListAdmin;
use MailBundle\Entity\MailingList\AdminRoleMap as ListAdminRole;
use MailBundle\Entity\MailingList\Entry\MailingList as MailingListEntry;
use MailBundle\Entity\MailingList\Entry\Person\Academic as AcademicEntry;
use MailBundle\Entity\MailingList\Entry\Person\External as ExternalEntry;
use Laminas\View\Model\ViewModel;

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
            $lists = $this->getEntityManager()
                ->getRepository('MailBundle\Entity\MailingList\Named')
                ->findBy(array(), array('name' => 'ASC'));

            $paginatorArray = array();
            foreach ($lists as $list) {
                if ($list->canBeEditedBy($person)) {
                    $paginatorArray[] = $list;
                }
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
                    'name' => 'ASC',
                )
            );
        }

        return new ViewModel(
            array(
                'person'            => $person,
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('mail_mailingList_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $list = $form->hydrateObject();

                $this->getEntityManager()->persist($list);
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
        $list = $this->getMailingListEntity();
        if ($list === null) {
            return new ViewModel();
        }

        if (!$this->checkAccess($list, false)) {
            return new ViewModel();
        }

        $academicForm = $this->getForm('mail_mailingList_entry_person_academic', array('list' => $list));
        $externalForm = $this->getForm('mail_mailingList_entry_person_external', array('list' => $list));
        $mailingListForm = $this->getForm('mail_mailingList_entry_mailingList', array('person' => $this->getAuthentication()->getPersonObject(), 'list' => $list));

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

            if ($entry !== null) {
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
                        'id'     => $list->getId(),
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
                'list'            => $list,
                'academicForm'    => $academicForm,
                'externalForm'    => $externalForm,
                'mailingListForm' => $mailingListForm,
                'entries'         => $entries,
            )
        );
    }

    public function adminsAction()
    {
        $list = $this->getMailingListEntity();
        if ($list === null) {
            return new ViewModel();
        }

        if (!$this->checkAccess($list, true)) {
            return new ViewModel();
        }

        $adminForm = $this->getForm('mail_mailingList_admin', array('list' => $list));
        $adminRoleForm = $this->getForm('mail_mailingList_adminRole', array('list' => $list));

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
                        'id'     => $list->getId(),
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
                        'id'     => $list->getId(),
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
                'list'          => $list,
                'adminForm'     => $adminForm,
                'adminRoleForm' => $adminRoleForm,
                'admins'        => $admins,
                'adminRoles'    => $adminRoles,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $list = $this->getMailingListEntity();
        if ($list === null) {
            return new ViewModel();
        }

        if (!$this->checkAccess($list, false)) {
            return new ViewModel();
        }

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

        $entry = $this->getMailingListEntryEntity();
        if ($entry === null) {
            return new ViewModel();
        }

        if (!$this->checkAccess($entry->getList(), false)) {
            return new ViewModel();
        }

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
        $list = $this->getMailingListEntity();
        if ($list === null) {
            return new ViewModel();
        }

        $entries = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\MailingList\Entry')
            ->findByList($list);

        foreach ($entries as $entry) {
            $this->getEntityManager()->remove($entry);
        }

        $this->getEntityManager()->flush();

        $this->flashMessenger()->success(
            'Success',
            'All entries were removed from the list.'
        );

        $this->redirect()->toRoute(
            'mail_admin_list',
            array(
                'action' => 'entries',
                'id'     => $list->getId(),
            )
        );

        return new ViewModel();
    }

    public function deleteAdminAction()
    {
        $this->initAjax();

        $admin = $this->getAdminMapEntity();
        if ($admin === null) {
            return new ViewModel();
        }

        if (!$this->checkAccess($admin->getList(), true)) {
            return new ViewModel();
        }

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

        $adminRole = $this->getAdminRoleMapEntity();
        if ($adminRole === null) {
            return new ViewModel();
        }

        if (!$this->checkAccess($adminRole->getList(), true)) {
            return new ViewModel();
        }

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

        $lists = $this->search()
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
    private function search()
    {
        switch ($this->getParam('field')) {
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('MailBundle\Entity\MailingList')
                    ->findAllByNameQuery($this->getParam('string'));
        }
    }

    /**
     * @return MailingList|null
     */
    private function getMailingListEntity()
    {
        $list = $this->getEntityById('MailBundle\Entity\MailingList');

        if (!($list instanceof MailingList)) {
            $this->flashMessenger()->error(
                'Error',
                'No mailing list was found!'
            );

            $this->redirect()->toRoute(
                'mail_admin_list',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $list;
    }

    /**
     * @return ExternalEntry|AcademicEntry|null
     */
    private function getMailingListEntryEntity()
    {
        $entry = $this->getEntityById('MailBundle\Entity\MailingList\Entry');

        if (!($entry instanceof ExternalEntry || $entry instanceof AcademicEntry || $entry instanceof MailingListEntry)) {
            $this->flashMessenger()->error(
                'Error',
                'No mailing list entry was found!'
            );

            $this->redirect()->toRoute(
                'mail_admin_list',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $entry;
    }

    /**
     * @return ListAdmin|null
     */
    private function getAdminMapEntity()
    {
        $entry = $this->getEntityById('MailBundle\Entity\MailingList\AdminMap');

        if (!($entry instanceof ListAdmin)) {
            $this->flashMessenger()->error(
                'Error',
                'No admin map was found!'
            );

            $this->redirect()->toRoute(
                'mail_admin_list',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $entry;
    }

    /**
     * @return ListAdminRole|null
     */
    private function getAdminRoleMapEntity()
    {
        $entry = $this->getEntityById('MailBundle\Entity\MailingList\AdminRoleMap');

        if (!($entry instanceof ListAdminRole)) {
            $this->flashMessenger()->error(
                'Error',
                'No admin role map was found!'
            );

            $this->redirect()->toRoute(
                'mail_admin_list',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $entry;
    }

    /**
     * @param  MailingList $list
     * @param  boolean     $adminEdit
     * @return boolean
     */
    private function checkAccess(MailingList $list, $adminEdit)
    {
        $person = $this->getAuthentication()->getPersonObject();

        if (!($person instanceof Academic)) {
            return false;
        }

        if (!$list->canBeEditedBy($person, $adminEdit)) {
            $this->flashMessenger()->error(
                'Error',
                'You don\'t have access to manage the admins for the given list!'
            );

            $this->redirect()->toRoute(
                'mail_admin_list',
                array(
                    'action' => 'manage',
                )
            );

            return false;
        }

        return true;
    }
}
