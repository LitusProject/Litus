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

namespace CommonBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Entity\User\Barcode,
    CommonBundle\Entity\General\Address,
    CommonBundle\Entity\User\Person\Academic,
    CommonBundle\Entity\User\Status\Organization as OrganizationStatus,
    CommonBundle\Entity\User\Status\University as UniversityStatus,
    CommonBundle\Form\Admin\Academic\Add as AddForm,
    CommonBundle\Form\Admin\Academic\Edit as EditForm,
    DateTime,
    Doctrine\ORM\Query,
    Zend\View\Model\ViewModel;

/**
 * AcademicController
 *
 * @autor Pieter Maene <pieter.maene@litus.cc>
 */
class AcademicController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (null !== $this->getParam('field')) {
            $academics = $this->_search();

            $paginator = $this->paginator()->createFromQuery(
                $academics,
                $this->getParam('page')
            );
        }

        if (!isset($paginator)) {
            $paginator = $this->paginator()->createFromEntity(
                'CommonBundle\Entity\User\Person\Academic',
                $this->getParam('page'),
                array(
                    'canLogin' => 'true'
                ),
                array(
                    'username' => 'ASC'
                )
            );
        }

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
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

                $roles = array();
                $roles[] = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Acl\Role')
                    ->findOneByName('student');

                if (isset($formData['roles'])) {
                    foreach ($formData['roles'] as $role) {
                        if ('student' == $role) continue;
                        $roles[] = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\Acl\Role')
                            ->findOneByName($role);
                    }
                }

                $academic = new Academic(
                    $formData['username'],
                    $roles,
                    $formData['first_name'],
                    $formData['last_name'],
                    $formData['email'],
                    $formData['phone_number'],
                    $formData['sex'],
                    ('' == $formData['university_identification'] ? null : $formData['university_identification'])
                );

                $this->getEntityManager()->persist($academic);

                if ('' != $formData['organization_status']) {
                    $academic->addOrganizationStatus(
                        new OrganizationStatus(
                            $academic,
                            $formData['organization_status'],
                            $this->getCurrentAcademicYear()
                        )
                    );
                }

                if ('' != $formData['barcode']) {
                    $this->getEntityManager()->persist(
                        new Barcode(
                            $academic, $formData['barcode']
                        )
                    );
                }

                if ('' != $formData['university_status']) {
                    $academic->addUniversityStatus(
                        new UniversityStatus(
                            $academic,
                            $formData['university_status'],
                            $this->getCurrentAcademicYear()
                        )
                    );
                }

                $academic->activate(
                    $this->getEntityManager(),
                    $this->getMailTransport(),
                    !$formData['activation_code']
                );

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The academic was successfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'common_admin_academic',
                    array(
                        'action' => 'add'
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
        if (!($academic = $this->_getAcademic()))
            return new ViewModel();

        $form = new EditForm(
            $this->getCache(), $this->getEntityManager(), $this->getCurrentAcademicYear(), $academic
        );

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $roles = array();
                $roles[] = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Acl\Role')
                    ->findOneByName('student');

                if (isset($formData['roles'])) {
                    foreach ($formData['roles'] as $role) {
                        if ('student' == $role) continue;
                        $roles[] = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\Acl\Role')
                            ->findOneByName($role);
                    }
                }

                $studentDomain = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('student_email_domain');

                $universityEmail = preg_replace('/[^a-z0-9\.@]/i', '', iconv("UTF-8", "US-ASCII//TRANSLIT", $formData['university_email'])) . $studentDomain;

                $birthday = DateTime::createFromFormat('d/m/Y H:i', $formData['birthday'] . ' 00:00');
                if(!$birthday)
                    $birthday = null;
                $academic->setFirstName($formData['first_name'])
                    ->setLastName($formData['last_name'])
                    ->setEmail($formData['email'])
                    ->setSex($formData['sex'])
                    ->setPhoneNumber($formData['phone_number'])
                    ->setUniversityIdentification(
                        ('' == $formData['university_identification'] ? null : $formData['university_identification'])
                    )
                    ->setBirthday($birthday)
                    ->setUniversityEmail($universityEmail)
                    ->setRoles($roles);

                if ('' != $formData['organization_status']) {
                    if (null !== $academic->getOrganizationStatus($this->getCurrentAcademicYear())) {
                        $academic->getOrganizationStatus($this->getCurrentAcademicYear())
                            ->setStatus($formData['organization_status']);
                    } else {
                        $academic->addOrganizationStatus(
                            new OrganizationStatus(
                                $academic,
                                $formData['organization_status'],
                                $this->getCurrentAcademicYear()
                            )
                        );
                    }
                } else {
                    if (null !== $academic->getOrganizationStatus($this->getCurrentAcademicYear())) {
                        $status = $academic->getOrganizationStatus($this->getCurrentAcademicYear());

                        $academic->removeOrganizationStatus($status);
                        $this->getEntityManager()->remove($status);
                    }
                }

                if ('' != $formData['barcode']) {
                    if (null !== $academic->getBarcode()) {
                        if ($academic->getBarcode()->getBarcode() != $formData['barcode']) {
                            $this->getEntityManager()->remove($academic->getBarcode());
                            $this->getEntityManager()->persist(new Barcode($academic, $formData['barcode']));
                        }
                    } else {
                        $this->getEntityManager()->persist(new Barcode($academic, $formData['barcode']));
                    }
                }

                if ('' != $formData['university_status']) {
                    if (null !== $academic->getUniversityStatus($this->getCurrentAcademicYear())) {
                        $academic->getUniversityStatus($this->getCurrentAcademicYear())
                            ->setStatus($formData['university_status']);
                    } else {
                        $academic->addUniversityStatus(
                            new UniversityStatus(
                                $academic,
                                $formData['university_status'],
                                $this->getCurrentAcademicYear()
                            )
                        );
                    }
                } else {
                    if (null !== $academic->getUniversityStatus($this->getCurrentAcademicYear())) {
                        $status = $academic->getUniversityStatus($this->getCurrentAcademicYear());

                        $academic->removeUniversityStatus($status);
                        $this->getEntityManager()->remove($status);
                    }
                }

                if ('other' != $formData['primary_address_address_city'] && '' != $formData['primary_address_address_city']) {
                    $primaryCity = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Address\City')
                        ->findOneById($formData['primary_address_address_city']);
                    $primaryPostal = $primaryCity->getPostal();
                    $primaryCity = $primaryCity->getName();
                    $primaryStreet = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Address\Street')
                        ->findOneById($formData['primary_address_address_street_' . $formData['primary_address_address_city']])
                        ->getName();
                } else {
                    $primaryCity = $formData['primary_address_address_city_other'];
                    $primaryStreet = $formData['primary_address_address_street_other'];
                    $primaryPostal = $formData['primary_address_address_postal_other'];
                }

                if (null !== $academic->getPrimaryAddress()) {
                    $academic->getPrimaryAddress()
                        ->setStreet($primaryStreet)
                        ->setNumber($formData['primary_address_address_number'])
                        ->setMailbox($formData['primary_address_address_mailbox'])
                        ->setPostal($primaryPostal)
                        ->setCity($primaryCity)
                        ->setCountry('BE');
                } else {
                    $academic->setPrimaryAddress(
                        new Address(
                            $primaryStreet,
                            $formData['primary_address_address_number'],
                            $formData['primary_address_address_mailbox'],
                            $primaryPostal,
                            $primaryCity,
                            'BE'
                        )
                    );
                }

                if (null !== $academic->getSecondaryAddress()) {
                    $academic->getSecondaryAddress()
                        ->setStreet($formData['secondary_address_address_street'])
                        ->setNumber($formData['secondary_address_address_number'])
                        ->setMailbox($formData['secondary_address_address_mailbox'])
                        ->setPostal($formData['secondary_address_address_postal'])
                        ->setCity($formData['secondary_address_address_city'])
                        ->setCountry($formData['secondary_address_address_country']);
                } else {
                    $academic->setSecondaryAddress(
                        new Address(
                            $formData['secondary_address_address_street'],
                            $formData['secondary_address_address_number'],
                            $formData['primary_address_address_mailbox'],
                            $formData['secondary_address_address_postal'],
                            $formData['secondary_address_address_city'],
                            $formData['secondary_address_address_country']
                        )
                    );
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The academic was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                    'common_admin_academic',
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
                'academic' => $academic,
            )
        );
    }

    public function activateAction()
    {
        if (!($academic = $this->_getAcademic()))
            return new ViewModel();

        $academic->activate(
            $this->getEntityManager(),
            $this->getMailTransport(),
            false
        );

        $this->getEntityManager()->flush();

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'Succes',
                'The academic was successfully activated!'
            )
        );

        $this->redirect()->toRoute(
            'common_admin_academic',
            array(
                'action' => 'edit',
                'id' => $academic->getId(),
            )
        );

        return new ViewModel();
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($academic = $this->_getAcademic()))
            return new ViewModel();

        $sessions = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Session')
            ->findByPerson($academic->getId());

        foreach ($sessions as $session) {
            $session->deactivate();
        }
        $academic->disableLogin();

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array('status' => 'success'),
            )
        );
    }

    public function typeaheadAction()
    {
        $this->initAjax();

        $academics = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findAllByNameTypeahead($this->getParam('string'));

        $result = array();
        foreach ($academics as $academic) {
            $identification = $academic->getUniversityIdentification() ? $academic->getUniversityIdentification() : $academic->getUserName();

            $item = (object) array();
            $item->id = $academic->getId();
            $item->name = $academic->getFullName();
            $item->universityIdentification = $identification;
            $item->value = $academic->getFullName() . ' - ' . $identification;
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        $academics = $this->_search()
            ->setMaxResults($numResults)
            ->getResult();

        $result = array();
        foreach ($academics as $academic) {
            if ($academic->canLogin()) {
                $item = (object) array();
                $item->id = $academic->getId();
                $item->username = $academic->getUsername();
                $item->universityIdentification = (
                    null !== $academic->getUniversityIdentification() ? $academic->getUniversityIdentification() : ''
                );
                $item->fullName = $academic->getFullName();
                $item->email = $academic->getEmail();

                $result[] = $item;
            }
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * @return Query|null
     */
    private function _search()
    {
        switch ($this->getParam('field')) {
            case 'username':
                return $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic')
                    ->findAllByUsernameQuery($this->getParam('string'));
            case 'name':
                return $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic')
                    ->findAllByNameQuery($this->getParam('string'));
            case 'university_identification':
                return $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic')
                    ->findAllByUniversityIdentificationQuery($this->getParam('string'));
        }
    }

    /**
     * @return Academic|null
     */
    private function _getAcademic()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the academic!'
                )
            );

            $this->redirect()->toRoute(
                'common_admin_academic',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $academic = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findOneById($this->getParam('id'));

        if (null === $academic) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No academic with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'common_admin_academic',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $academic;
    }
}
