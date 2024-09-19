<?php

namespace MailBundle\Controller\Admin;

use CommonBundle\Entity\User\Person\Academic;
use Google;
use Laminas\View\Model\ViewModel;
use MailBundle\Entity\MailingList;
use MailBundle\Entity\MailingList\AdminMap as ListAdmin;
use MailBundle\Entity\MailingList\AdminRoleMap as ListAdminRole;
use MailBundle\Entity\MailingList\Entry\MailingList as MailingListEntry;
use MailBundle\Entity\MailingList\Entry\Person\Academic as AcademicEntry;
use MailBundle\Entity\MailingList\Entry\Person\External as ExternalEntry;

putenv('GOOGLE_APPLICATION_CREDENTIALS=/home/it/service-account.json');

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
        // Dit is nodig voor de authenticatie
        $client = $this->getGoogleClient();

        /**
         * Reference:
         * Groups: https://developers.google.com/admin-sdk/directory/reference/rest/v1/groups#Group
         * Members: https://developers.google.com/admin-sdk/directory/reference/rest/v1/members
         * Settings: https://developers.google.com/admin-sdk/groups-settings/v1/reference/groups
         *
         * Examples:
         * Google directory API: $directory = new \Google_Service_Directory($client)
         * Google Groups Settings API: $settings = new \Google_Service_Groupssettings($client)
         * Create empty Group Object: $group = new \Google_Service_Directory_Group() of new Google\Service\Directory\Group()
         * Create empty Member Objcet: $member = new \Google_Service_Group_Member() of new Google\Service\Directory\Member()
         *
         * Upload group to workspace: $upload = $directory->groups->insert($group)
         * Insert members into group: $insert = $directory->members->insert('group email', $member)
         * Delete member from group: $delete = $directory->members->delete('group email', 'member email')
         *
         * Get group settings: $group_settings = $settings->groups->get('group email', array('alt' => 'json'))
         *
         *
         */
        $directory = new \Google_Service_Directory($client);
        $setting_service = new \Google_Service_Groupssettings($client);

        $default_settings = new Google\Service\Groupssettings\Groups();
        $default_settings->setWhoCanPostMessage('ANYONE_CAN_POST'); // Allow external people to send a mail to this list
        $default_settings->setWhoCanJoin('INVITED_CAN_JOIN'); // Only invited users can join
        $default_settings->setWhoCanViewMembership('ALL_MEMBERS_CAN_VIEW'); // Members of this group can view messages
        $default_settings->setWhoCanViewGroup('ALL_MEMBERS_CAN_VIEW'); // Members of this group can view who's a member
        $default_settings->setAllowWebPosting('false'); // Only messages through mail

//        $member = new \Google_Service_Directory_Member(array('email' => 'stancardinaels@gmail.com'));
//        $member = new \Google_Service_Directory_Member();
//        $member->setEmail("stancardinaels@gmail.com");

//        $insert = $directory->members->insert('it@vtk.be', $member);
//        $group = new \Google_Service_Directory_Group();
//        $group->setEmail("testgroup@vtk.be");
//        $group->setName("Test Group");
//        $upload = $directory->groups->insert($group);

//        $member = new Google\Service\Directory\Member();
//        $member->setEmail('stan.cardinaels@vtk.be');
//        $member->setKind('admin#directory#member');
//        $member->setRole('OWNER');
//        $member->setType("USER");

//        $insert = $directory->members->insert('testgroup@vtk.be', $member);
//        $delete = $directory->members->delete('testgroup@vtk.be', 'stan.cardinaels@vtk.be');
//        $settings = $setting_service->groups->get('it@vtk.be', array('alt' => 'json'));
//        $test = $directory->groups->get('it@vtk.be');
//        die(var_dump($test));


        $form = $this->getForm('mail_mailingList_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $list = $form->hydrateObject();

                $data = $form->getData();
                $list_name = $data['name'];
                $list_mail = $data['name'] . '@vtk.be';

                $group = new Google\Service\Directory\Group();
                $group->setName($list_name);
                $group->setEmail($list_mail);

                try {
                    $directory->groups->insert($group);
                } catch (\Exception $e) {
                }

                $setting_service->groups->update($list_mail, $default_settings);

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
        $client = $this->getGoogleClient();

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
                $mail = $entry->getEmailAddress();
            } elseif (isset($formData['external_add']) && $externalForm->isValid()) {
                $entry = $externalForm->hydrateObject(
                    new ExternalEntry($list)
                );
                $mail = $entry->getEmailAddress();
            } elseif (isset($formData['list_add']) && $mailingListForm->isValid()) {
                $entry = $mailingListForm->hydrateObject(
                    new MailingListEntry($list)
                );
                $mail = $entry->getEmailAddress() . '@vtk.be';
            }

            if ($entry !== null) {
                $list_name = $list->getName();
                $list_mail = $list_name . '@vtk.be';
                $directory = new \Google_Service_Directory($client);

                $member = new Google\Service\Directory\Member();
                $member->setEmail($mail);

                try {
                    $directory->members->insert($list_mail, $member);
                } catch (\Exception $e) {
                }

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

        $client = $this->getGoogleClient();

        $list = $this->getMailingListEntity();
        if ($list === null) {
            return new ViewModel();
        }

        if (!$this->checkAccess($list, false)) {
            return new ViewModel();
        }
        $list_name = $list->getName();
        $list_email = $list_name . '@vtk.be';

        $directory = new \Google_Service_Directory($client);
        try {
            $directory->groups->delete($list_email);
        } catch (\Exception $e) {
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

        $list_name = $entry->getList()->getName();
        $list_email = $list_name . '@vtk.be';
        $email = $entry->getEmailAddress();

        if (!$this->checkAccess($entry->getList(), false)) {
            return new ViewModel();
        }

        $client = $this->getGoogleClient();

        $directory = new \Google_Service_Directory($client);
        try {
            $directory->members->delete($list_email, $email);
        } catch (\Exception $e) {
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

    public function addAllAction()
    {
        $client = $this->getGoogleClient();

        $directory = new \Google_Service_Directory($client);
        $setting_service = new \Google_Service_Groupssettings($client);

        $default_settings = new Google\Service\Groupssettings\Groups();
        $default_settings->setWhoCanPostMessage('ANYONE_CAN_POST'); // Allow external people to send a mail to this list
        $default_settings->setWhoCanJoin('INVITED_CAN_JOIN'); // Only invited users can join
        $default_settings->setWhoCanViewMembership('ALL_MEMBERS_CAN_VIEW'); // Members of this group can view messages
        $default_settings->setWhoCanViewGroup('ALL_MEMBERS_CAN_VIEW'); // Members of this group can view who's a member
        $default_settings->setAllowWebPosting('false'); // Only messages through mail

        $lists = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\MailingList')
            ->findAll();

        foreach ($lists as $list) {
            $list_name = $list->getName();
            $list_mail = $list_name . '@vtk.be';

            $group = new Google\Service\Directory\Group();
            $group->setName($list_name);
            $group->setEmail($list_mail);

            try {
                $directory->groups->insert($group);
                $setting_service->groups->update($list_mail, $default_settings);
            } catch (\Exception $e) {
                error_log($list_mail);
                error_log($e->getMessage());
            }

            $entries = $list->getEntries();
            foreach ($entries as $entry) {
                $mail = $entry->getEmailAddress();
                if (strpos($mail, '@') === false) {
                    $mail .= '@vtk.be';
                }

                $member = new Google\Service\Directory\Member();
                $member->setEmail($mail);
                try {
                    $directory->members->insert($list_mail, $member);
                } catch (\Exception $e) {
                    error_log($mail);
                    error_log($e->getMessage());
                }
            }
        }

        return new ViewModel();
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

    private function getGoogleClient()
    {
        $client = new Google\Client();

        $client->useApplicationDefaultCredentials();
        $client->addScope(Google\Service\Directory::ADMIN_DIRECTORY_GROUP);
        $client->addScope(Google\Service\Directory::ADMIN_DIRECTORY_GROUP_MEMBER);
        $client->addScope(Google\Service\Directory::ADMIN_DIRECTORY_GROUP_MEMBER_READONLY);
        $client->addScope(Google\Service\Directory::ADMIN_DIRECTORY_GROUP_READONLY);
        $client->addScope(Google\Service\Groupssettings::APPS_GROUPS_SETTINGS);
        $client->setSubject('stan.cardinaels@vtk.be');
        $client->authorize();

        return $client;
    }
}
