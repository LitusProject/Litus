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

namespace SecretaryBundle\Controller\Admin;

use CommonBundle\Component\Document\Generator\Csv as CsvGenerator,
    CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile,
    CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\General\Organization,
    SecretaryBundle\Form\Admin\Export\Export as ExportForm,
    Zend\View\Model\ViewModel;

/**
 * ExportController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ExportController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function exportAction()
    {
        $form = new ExportForm($this->getEntityManager());
        $form->setAttribute(
            'action',
            $this->url()->fromRoute(
                'secretary_admin_export', array('action' => 'download')
            )
        );

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function downloadAction()
    {
        $form = new ExportForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $academicYear = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\AcademicYear')
                    ->findOneById($formData['academic_year']);

                $organization = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Organization')
                    ->findOneById($formData['organization']);

                $this->getResponse()->getHeaders()
                    ->addHeaders(
                    array(
                        'Content-Disposition' => 'attachment; filename="members_' . strtolower($organization->getName()) . '_' . $academicYear->getCode() . '.csv"',
                        'Content-Type' => 'text/csv',
                    )
                );

                return new ViewModel(
                    array(
                        'result' => $this->_generateFile($organization, $academicYear),
                    )
                );
            }
        }

        return $this->notFoundAction();
    }

    private function _generateFile(Organization $organization, AcademicYear $academicYear)
    {
        $mappings = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person\Organization\AcademicYearMap')
            ->findAllByAcademicYearAndOrganization($academicYear, $organization);

        $members = array();
        foreach ($mappings as $mapping) {
            $academic = $mapping->getAcademic();

            $registration = $this->getEntityManager()
                ->getRepository('SecretaryBundle\Entity\Registration')
                ->findOneByAcademicAndAcademicYear($academic, $academicYear);

            if (null === $registration || !$registration->hasPayed())
                continue;

            $primaryAddress = $academic->getPrimaryAddress();
            $secondaryAddress = $academic->getSecondaryAddress();

            $members[$mapping->getAcademic()->getId()] = array(
                'academicFirstName'               => $academic->getFirstName(),
                'academicLastName'                => $academic->getLastName(),
                'academicEmail'                   => $academic->getEmail(),
                'academicPrimaryAddressStreet'    => $primaryAddress ? $primaryAddress->getStreet() : '',
                'academicPrimaryAddressNumber'    => $primaryAddress ? $primaryAddress->getNumber() : '',
                'academicPrimaryAddressMailbox'   => $primaryAddress ? $primaryAddress->getMailbox() : '',
                'academicPrimaryAddressPostal'    => $primaryAddress ? $primaryAddress->getPostal() : '',
                'academicPrimaryAddressCity'      => $primaryAddress ? $primaryAddress->getCity() : '',
                'academicPrimaryAddressCountry'   => $primaryAddress ? $primaryAddress->getCountry() : '',
                'academicSecondaryAddressStreet'  => $secondaryAddress ? $secondaryAddress->getStreet() : '',
                'academicSecondaryAddressNumber'  => $secondaryAddress ? $secondaryAddress->getNumber() : '',
                'academicSecondaryAddressMailbox' => $secondaryAddress ? $secondaryAddress->getMailbox() : '',
                'academicSecondaryAddressPostal'  => $secondaryAddress ? $secondaryAddress->getPostal() : '',
                'academicSecondaryAddressCity'    => $secondaryAddress ? $secondaryAddress->getCity() : '',
                'academicSecondaryAddressCountry' => $secondaryAddress ? $secondaryAddress->getCountry() : '',
            );
        }

        $header = array(
            'First Name',
            'Last Name',
            'E-mail',
            'Street (Primary Address)',
            'Number (Primary Address)',
            'Mailbox (Primary Address)',
            'Postal (Primary Address)',
            'City (Primary Address)',
            'Country (Primary Address)',
            'Street (Secondary Address)',
            'Number (Secondary Address)',
            'Mailbox (Secondary Address)',
            'Postal (Secondary Address)',
            'City (Secondary Address)',
            'Country (Secondary Address)',
        );

        $exportFile = new CsvFile();
        $csvGenerator = new CsvGenerator($header, $members);
        $csvGenerator->generateDocument($exportFile);

        return $exportFile->getContent();
    }
}
