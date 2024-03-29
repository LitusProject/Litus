<?php

namespace SecretaryBundle\Controller\Admin;

use CommonBundle\Component\Util\AcademicYear;
use CommonBundle\Entity\User\Barcode\Ean12;
use CommonBundle\Entity\User\Barcode\Qr;
use CommonBundle\Entity\User\Person;
use CommonBundle\Entity\User\Person\Organization\AcademicYearMap;
use CommonBundle\Entity\User\Status\Organization as OrganizationStatus;
use CudiBundle\Component\Socket\Sale\Printer;
use InvalidArgumentException;
use Laminas\Validator\Barcode\Ean12 as Ean12Validator;
use Laminas\View\Model\ViewModel;
use SecretaryBundle\Component\Registration\Articles as RegistrationArticles;
use SecretaryBundle\Entity\Organization\MetaData;
use SecretaryBundle\Entity\Registration;

/**
 * RegistrationController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class RegistrationController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
            return new ViewModel();
        }

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $organizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        $paginator = $this->paginator()->createFromEntity(
            'SecretaryBundle\Entity\Registration',
            $this->getParam('page'),
            array(
                'academicYear' => $academicYear,
            ),
            array(
                'timestamp' => 'ASC',
            )
        );

        return new ViewModel(
            array(
                'paginator'           => $paginator,
                'paginationControl'   => $this->paginator()->createControl(),
                'activeAcademicYear'  => $academicYear,
                'academicYears'       => $academicYears,
                'organizations'       => $organizations,
                'currentOrganization' => $this->getOrganizationEntity(),
            )
        );
    }

    public function barcodeAction()
    {
        $registration = $this->getRegistrationEntity();
        if ($registration === null) {
            return new ViewModel();
        }

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $organizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        $form = $this->getForm('secretary_registration_barcode', array('person' => $registration->getAcademic()));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                if ($registration->getAcademic()->getBarcode() !== null) {
                    if ($registration->getAcademic()->getBarcode()->getBarcode() != $formData['barcode']) {
                        $this->getEntityManager()->remove($registration->getAcademic()->getBarcode());
                        $this->getEntityManager()->persist(
                            $this->createBarcode(
                                $formData['type'],
                                $registration->getAcademic(),
                                $formData['barcode']
                            )
                        );
                    }
                } else {
                    $this->getEntityManager()->persist(
                        $this->createBarcode(
                            $formData['type'],
                            $registration->getAcademic(),
                            $formData['barcode']
                        )
                    );
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The barcode was successfully set!'
                );

                $this->redirect()->toRoute(
                    'secretary_admin_registration',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'registration'        => $registration,
                'activeAcademicYear'  => $registration->getAcademicYear(),
                'academicYears'       => $academicYears,
                'form'                => $form,
                'organizations'       => $organizations,
                'currentOrganization' => $this->getOrganizationEntity(),
            )
        );
    }

    public function addAction()
    {
        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
            return new ViewModel();
        }

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $organizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        $form = $this->getForm('secretary_registration_add');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                $academic = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic')
                    ->findOneById($formData['person']['id']);

                $registration = $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Registration')
                    ->findOneByAcademicAndAcademicYear($academic, $academicYear);

                $organization = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Organization')
                    ->findOneById($formData['organization']);

                if ($registration !== null) {
                    $this->flashMessenger()->warn(
                        'WARNING',
                        'There was already a registration for this academic!'
                    );

                    $this->redirect()->toRoute(
                        'secretary_admin_registration',
                        array(
                            'action' => 'edit',
                            'id'     => $registration->getId(),
                        )
                    );

                    return;
                }

                $metaData = new MetaData(
                    $academic,
                    $academicYear
                );
                $metaData->setBecomeMember(false)
                    ->setIrreeelAtCudi($formData['irreeel'])
                    ->setBakskeByMail($formData['bakske'])
                    ->setTshirtSize($formData['tshirt_size']);
                $this->getEntityManager()->persist($metaData);

                $organizationMap = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Organization\AcademicYearMap')
                    ->findOneByAcademicAndAcademicYear($academic, $academicYear);

                if ($organizationMap !== null) {
                    $organizationMap->setOrganization($organization);
                } else {
                    $this->getEntityManager()->persist(new AcademicYearMap($academic, $academicYear, $organization));
                }

                RegistrationArticles::book(
                    $this->getEntityManager(),
                    $academic,
                    $organization,
                    $academicYear,
                    array(
                        'payed'      => $formData['payed'],
                        'tshirtSize' => $formData['tshirt_size'],
                    )
                );

                $registration = new Registration(
                    $academic,
                    $this->getCurrentAcademicYear()
                );
                $registration->setPayed($formData['payed']);

                if ($formData['payed'] == true) {
                    $status = $registration->getAcademic()
                        ->getOrganizationStatus($this->getCurrentAcademicYear());
                    if ($status === null) {
                        $registration->getAcademic()
                            ->addOrganizationStatus(
                                new OrganizationStatus(
                                    $registration->getAcademic(),
                                    'member',
                                    $this->getCurrentAcademicYear()
                                )
                            );
                    } else {
                        if ($status->getStatus() === 'non_member') {
                            $status->setStatus('member');
                        }
                    }
                } else {
                    $status = $registration->getAcademic()
                        ->getOrganizationStatus($this->getCurrentAcademicYear());
                    if ($status === null) {
                        $registration->getAcademic()
                            ->addOrganizationStatus(
                                new OrganizationStatus(
                                    $registration->getAcademic(),
                                    'non_member',
                                    $this->getCurrentAcademicYear()
                                )
                            );
                    } else {
                        if ($status->getStatus() === 'non_member') {
                            $status->setStatus('non_member');
                        }
                    }
                }

                $this->getEntityManager()->persist($registration);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The registration was successfully created!'
                );

                $this->redirect()->toRoute(
                    'secretary_admin_registration',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form'                => $form,
                'activeAcademicYear'  => $academicYear,
                'academicYears'       => $academicYears,
                'organizations'       => $organizations,
                'currentOrganization' => $this->getOrganizationEntity(),
            )
        );
    }

    public function editAction()
    {
        $registration = $this->getRegistrationEntity();
        if ($registration === null) {
            return new ViewModel();
        }

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $organizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        $metaData = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Organization\MetaData')
            ->findOneByAcademicAndAcademicYear($registration->getAcademic(), $registration->getAcademicYear());

        $form = $this->getForm('secretary_registration_edit', array('registration' => $registration, 'metaData' => $metaData));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $registration->setPayed($formData['payed'])
                    ->setCancelled($formData['cancel']);

                if ($formData['payed'] == true && !$formData['cancel']) {
                    $status = $registration->getAcademic()
                        ->getOrganizationStatus($this->getCurrentAcademicYear());

                    if ($status === null) {
                        $registration->getAcademic()
                            ->addOrganizationStatus(
                                new OrganizationStatus(
                                    $registration->getAcademic(),
                                    'member',
                                    $this->getCurrentAcademicYear()
                                )
                            );
                    } else {
                        if ($status->getStatus() === 'non_member') {
                            $status->setStatus('member');
                        }
                    }
                } else {
                    $status = $registration->getAcademic()
                        ->getOrganizationStatus($this->getCurrentAcademicYear());

                    if ($status === null) {
                        $registration->getAcademic()
                            ->addOrganizationStatus(
                                new OrganizationStatus(
                                    $registration->getAcademic(),
                                    'non_member',
                                    $this->getCurrentAcademicYear()
                                )
                            );
                    } else {
                        if ($status->getStatus() === 'member') {
                            $status->setStatus('non_member');
                        }
                    }
                }

                $organization = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Organization')
                    ->findOneById($formData['organization']);

                $organizationMap = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Organization\AcademicYearMap')
                    ->findOneByAcademicAndAcademicYear($registration->getAcademic(), $registration->getAcademicYear());

                if ($organizationMap !== null) {
                    $organizationMap->setOrganization($organization);
                } else {
                    $this->getEntityManager()->persist(new AcademicYearMap($registration->getAcademic(), $registration->getAcademicYear(), $organization));
                }

                if (!$formData['cancel']) {
                    RegistrationArticles::book(
                        $this->getEntityManager(),
                        $registration->getAcademic(),
                        $organization,
                        $registration->getAcademicYear(),
                        array(
                            'payed'      => $formData['payed'],
                            'tshirtSize' => $formData['tshirt_size'],
                        )
                    );
                }

                if ($metaData === null) {
                    $metaData = new MetaData(
                        $registration->getAcademic(),
                        $registration->getAcademicYear()
                    );
                    $metaData->setBecomeMember(false)
                        ->setIrreeelAtCudi($formData['irreeel'])
                        ->setBakskeByMail($formData['bakske'])
                        ->setTshirtSize($formData['tshirt_size']);
                    $this->getEntityManager()->persist($metaData);
                } else {
                    $metaData->setIrreeelAtCudi($formData['irreeel'])
                        ->setBakskeByMail($formData['bakske'])
                        ->setTshirtSize($formData['tshirt_size']);
                }

                if ($formData['cancel']) {
                    $this->cancelRegistration($registration);
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The registration was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'secretary_admin_registration',
                    array(
                        'action' => 'edit',
                        'id'     => $registration->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'registration'        => $registration,
                'activeAcademicYear'  => $registration->getAcademicYear(),
                'academicYears'       => $academicYears,
                'form'                => $form,
                'organizations'       => $organizations,
                'currentOrganization' => $this->getOrganizationEntity(),
            )
        );
    }

    public function cancelAction()
    {
        $this->initAjax();

        $registration = $this->getRegistrationEntity();
        if ($registration === null) {
            return new ViewModel();
        }

        $academic = $registration->getAcademic();
        $organizationStatus = $academic->getOrganizationStatus($registration->getAcademicYear());

        if ($organizationStatus !== null && $organizationStatus->getStatus() == 'praesidium') {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        } elseif ($registration->isCancelled()) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'success'),
                )
            );
        } else {
            $this->cancelRegistration($registration);
            $this->getEntityManager()->flush();

            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'success'),
                )
            );
        }
    }

    public function searchAction()
    {
        $this->initAjax();

        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
            return new ViewModel();
        }


        $registrations = array();
        $organization = $this->getOrganizationEntity();
        switch ($this->getParam('field')) {
            case 'university_identification':
                $registrations = $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Registration')
                    ->findAllByUniversityIdentification(
                        $this->getParam('string'),
                        $academicYear,
                        $organization
                    );
                break;
            case 'name':
                $registrations = $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Registration')
                    ->findAllByName(
                        $this->getParam('string'),
                        $academicYear,
                        $organization
                    );
                break;
            case 'barcode':
                $registrations = $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Registration')
                    ->findAllByBarcode(
                        $this->getParam('string'),
                        $academicYear,
                        $organization
                    );
                break;
        }

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($registrations, $numResults);

        $result = array();
        foreach ($registrations as $registration) {
            if ($registration->getAcademic()->canLogin()) {
                $item = (object) array();
                $item->id = $registration->getId();
                $item->universityIdentification = ($registration->getAcademic()->getUniversityIdentification() !== null ? $registration->getAcademic()->getUniversityIdentification() : '');
                $item->name = $registration->getAcademic()->getFullName();
                $item->date = $registration->getTimestamp()->format('d/m/Y H:i');
                $item->payed = $registration->hasPayed();
                $item->cancelled = $registration->isCancelled();
                $item->barcode = $registration->getAcademic()->getBarcode() ? $registration->getAcademic()->getBarcode()->getBarcode() : '';
                $item->organization = $registration->getAcademic()->getOrganization($academicYear) ? $registration->getAcademic()->getOrganization($academicYear)->getName() : '';
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
     * @return \CommonBundle\Entity\General\AcademicYear|null
     */
    private function getAcademicYearEntity()
    {
        if ($this->getParam('academicyear') === null) {
            return $this->getCurrentAcademicYear();
        }

        $start = AcademicYear::getDateTime($this->getParam('academicyear'));
        $start->setTime(0, 0);

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByUniversityStart($start);

        if ($academicYear === null) {
            $this->flashMessenger()->error(
                'Error',
                'No academic year was found!'
            );

            $this->redirect()->toRoute(
                'secretary_admin_registration',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $academicYear;
    }

    /**
     * @return Registration|null
     */
    private function getRegistrationEntity()
    {
        $registration = $this->getEntityById('SecretaryBundle\Entity\Registration');

        if (!($registration instanceof Registration)) {
            $this->flashMessenger()->error(
                'Error',
                'No registration was found!'
            );

            $this->redirect()->toRoute(
                'secretary_admin_registration',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $registration;
    }

    public function reprintAction()
    {
        $this->initAjax();

        $registration = $this->getRegistrationEntity();
        if ($registration === null) {
            return new ViewModel();
        }

        $academic = $registration->getAcademic();
        $organizationStatus = $academic->getOrganizationStatus($registration->getAcademicYear());

        if ($organizationStatus !== null && $organizationStatus->getStatus() == 'praesidium') {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        } elseif ($registration->isCancelled()) {
            return new ViewModel(
                array(
                    'result' => (object) array('status' => 'error'),
                )
            );
        } else {
            Printer::membershipCard(
                $this->getEntityManager(),
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('cudi.card_printer'),
                $academic,
                $this->getCurrentAcademicYear()
            );

            return new ViewModel(
                array(
                    'result' => array('status' => 'success'),
                )
            );
        }
    }

    /**
     * @return \CommonBundle\Entity\General\Organization|null
     */
    private function getOrganizationEntity()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findOneById($this->getParam('organization', 0));
    }

    /**
     * @param Registration $registration
     */
    private function cancelRegistration(Registration $registration)
    {
        $academic = $registration->getAcademic();
        $organizationStatus = $academic->getOrganizationStatus($registration->getAcademicYear());

        $metaData = $this->getEntityManager()
            ->getRepository('SecretaryBundle\Entity\Organization\MetaData')
            ->findOneByAcademicAndAcademicYear($registration->getAcademic(), $registration->getAcademicYear());

        if ($metaData !== null) {
            $metaData->setBecomeMember(false)
                ->setIrreeelAtCudi(false)
                ->setTshirtSize(null);
        }

        if ($organizationStatus !== null) {
            $organizationStatus->setStatus('non_member');
        } else {
            $academic->addOrganizationStatus(
                new OrganizationStatus(
                    $academic,
                    'non_member',
                    $registration->getAcademicYear()
                )
            );
        }

        $registration->setPayed(false)
            ->setCancelled(true);

        RegistrationArticles::cancel($this->getEntityManager(), $academic, $registration->getAcademicYear());
    }

    /**
     * @param  string  $type
     * @param  Person  $person
     * @param  integer $barcode
     * @return \CommonBundle\Entity\User\Barcode
     */
    private function createBarcode($type, Person $person, $barcode)
    {
        switch ($type) {
            case 'ean12':
                $validator = new Ean12Validator();
                if (!$validator->hasValidChecksum($barcode)) {
                    throw new InvalidArgumentException('The given barcode was not a valid EAN-12 code');
                }

                return new Ean12($person, $barcode);
            case 'qr':
                return new Qr($person, $barcode);
            default:
                throw new InvalidArgumentException('No barcode could be created');
        }
    }
}
