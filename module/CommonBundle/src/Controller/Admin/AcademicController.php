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

namespace CommonBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Entity\Users\Barcode,
    CommonBundle\Entity\Users\People\Academic,
    CommonBundle\Entity\Users\Statuses\Organization as OrganizationStatus,
    CommonBundle\Entity\Users\Statuses\University as UniversityStatus,
    CommonBundle\Form\Admin\Academic\Add as AddForm,
    CommonBundle\Form\Admin\Academic\Edit as EditForm,
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
        $paginator = $this->paginator()->createFromEntity(
            'CommonBundle\Entity\Users\People\Academic',
            $this->getParam('page'),
            array(
                'canLogin' => 'true'
            ),
            array(
                'username' => 'ASC'
            )
        );

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
                $roles = array();
                $roles[] = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Acl\Role')
                    ->findOneByName('student');
                if ($formData['roles']) {
                    foreach ($formData['roles'] as $role) {
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
                            $registration->getAcademic(), $formData['barcode']
                        )
                    );
                }

                $academic->addUniversityStatus(
                    new UniversityStatus(
                        $academic,
                        $formData['university_status'],
                        $this->getCurrentAcademicYear()
                    )
                );

                $academic->activate(
                    $this->getEntityManager(),
                    $this->getMailTransport(),
                    !$formData['activation_code']
                );

                $this->getEntityManager()->persist($academic);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The user was successfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_academic',
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
            $this->getEntityManager(), $this->getCurrentAcademicYear(), $academic
        );

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $roles = array();
                $roles[] = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Acl\Role')
                    ->findOneByName('student');
                if ($formData['roles']) {
                    foreach ($formData['roles'] as $role) {
                        $roles[] = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\Acl\Role')
                            ->findOneByName($role);
                    }
                }

                $academic->setFirstName($formData['first_name'])
                    ->setLastName($formData['last_name'])
                    ->setEmail($formData['email'])
                    ->setSex($formData['sex'])
                    ->setPhoneNumber($formData['phone_number'])
                    ->setUniversityIdentification(
                        ('' == $formData['university_identification'] ? null : $formData['university_identification'])
                    )
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

                if ($status = $academic->getUniversityStatus($this->getCurrentAcademicYear())) {
                    $status->setStatus($formData['university_status']);
                } else {
                    $academic->addUniversityStatus(
                        new UniversityStatus(
                            $academic,
                            $formData['university_status'],
                            $this->getCurrentAcademicYear()
                        )
                    );
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The user was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_academic',
                    array(
                        'action' => 'manage',
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

        if (!($academic = $this->_getAcademic()))
            return new ViewModel();

        $sessions = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Users\Session')
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
            ->getRepository('CommonBundle\Entity\Users\People\Academic')
            ->findAllByNameTypeahead($this->getParam('string'));

        $result = array();
        foreach($academics as $academic) {
            $item = (object) array();
            $item->id = $academic->getId();
            $item->value = $academic->getFullName() . ' - ' . $academic->getUniversityIdentification();
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

        switch($this->getParam('field')) {
            case 'username':
                $academics = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Users\People\Academic')
                    ->findAllByUsername($this->getParam('string'));
                break;
            case 'name':
                $academics = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Users\People\Academic')
                    ->findAllByName($this->getParam('string'));
                break;
            case 'university_identification':
                $academics = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Users\People\Academic')
                    ->findAllByUniversityIdentification($this->getParam('string'));
                break;
        }

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($academics, $numResults);

        $result = array();
        foreach($academics as $academic) {
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
                'admin_academic',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $academic = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Users\People\Academic')
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
                'admin_academic',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $academic;
    }
}
