<?php

namespace SecretaryBundle\Component\Document\Generator;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\General\Organization;
use Doctrine\ORM\EntityManager;

/**
 * Registration
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Registration extends \CommonBundle\Component\Document\Generator\Csv
{
    /**
     * @param EntityManager $entityManager
     * @param Organization  $organization
     * @param AcademicYear  $academicYear
     */
    public function __construct(EntityManager $entityManager, Organization $organization, AcademicYear $academicYear)
    {
        $headers = array(
            'First Name',
            'Last Name',
            'Username',
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

        $mappings = $entityManager
            ->getRepository('CommonBundle\Entity\User\Person\Organization\AcademicYearMap')
            ->findAllByAcademicYearAndOrganization($academicYear, $organization);

        $result = array();
        foreach ($mappings as $mapping) {
            $academic = $mapping->getAcademic();

            $registration = $entityManager
                ->getRepository('SecretaryBundle\Entity\Registration')
                ->findOneByAcademicAndAcademicYear($academic, $academicYear);

            if ($registration === null || !$registration->hasPayed()) {
                continue;
            }

            $primaryAddress = $academic->getPrimaryAddress();
            $secondaryAddress = $academic->getSecondaryAddress();

            $result[$mapping->getAcademic()->getId()] = array(
                'academicFirstName'               => $academic->getFirstName(),
                'academicLastName'                => $academic->getLastName(),
                'username'                        => $academic->getUniversityIdentification(),
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

        parent::__construct($headers, $result);
    }
}
