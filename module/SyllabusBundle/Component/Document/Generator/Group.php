<?php

namespace SyllabusBundle\Component\Document\Generator;

use CommonBundle\Entity\General\AcademicYear;
use Doctrine\ORM\EntityManager;
use SyllabusBundle\Entity\Group as GroupEntity;

/**
 * Group
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Group extends \CommonBundle\Component\Document\Generator\Csv
{
    /**
     * @param EntityManager $entityManager
     * @param GroupEntity   $group
     * @param AcademicYear  $academicYear
     */
    public function __construct(EntityManager $entityManager, GroupEntity $group, AcademicYear $academicYear)
    {
        $headers = array(
            'First name',
            'Last name',
            'Email',
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
            'Study',
        );

        $mappings = $entityManager
            ->getRepository('SyllabusBundle\Entity\Group\StudyMap')
            ->findAllByGroupAndAcademicYear($group, $academicYear);

        $result = array();
        foreach ($mappings as $mapping) {
            $study = $mapping->getStudy();
            $enrollments = $entityManager
                ->getRepository('SecretaryBundle\Entity\Syllabus\Enrollment\Study')
                ->findAllByStudy($study);

            foreach ($enrollments as $enrollment) {
                $ac = $enrollment->getAcademic();

                $primaryAddress = $ac->getPrimaryAddress();
                $secondaryAddress = $ac->getSecondaryAddress();

                $result[$ac->getId()] = array(
                    'academicFirstName' => $ac->getFirstName(),
                    'academicLastName'  => $ac->getLastName(),
                    'academicEmail'     => $ac->getEmail(),
                );

                if ($primaryAddress !== null) {
                    $result[$ac->getId()]['academicPrimaryAddressStreet'] = $primaryAddress->getStreet();
                    $result[$ac->getId()]['academicPrimaryAddressNumber'] = $primaryAddress->getNumber();
                    $result[$ac->getId()]['academicPrimaryAddressMailbox'] = $primaryAddress->getMailbox();
                    $result[$ac->getId()]['academicPrimaryAddressPostal'] = $primaryAddress->getPostal();
                    $result[$ac->getId()]['academicPrimaryAddressCity'] = $primaryAddress->getCity();
                    $result[$ac->getId()]['academicPrimaryAddressCountry'] = $primaryAddress->getCountry();
                } else {
                    $result[$ac->getId()]['academicPrimaryAddressStreet'] = '';
                    $result[$ac->getId()]['academicPrimaryAddressNumber'] = '';
                    $result[$ac->getId()]['academicPrimaryAddressMailbox'] = '';
                    $result[$ac->getId()]['academicPrimaryAddressPostal'] = '';
                    $result[$ac->getId()]['academicPrimaryAddressCity'] = '';
                    $result[$ac->getId()]['academicPrimaryAddressCountry'] = '';
                }

                if ($secondaryAddress !== null) {
                    $result[$ac->getId()]['academicSecondaryAddressStreet'] = $secondaryAddress->getStreet();
                    $result[$ac->getId()]['academicSecondaryAddressNumber'] = $secondaryAddress->getNumber();
                    $result[$ac->getId()]['academicSecondaryAddressMailbox'] = $secondaryAddress->getMailbox();
                    $result[$ac->getId()]['academicSecondaryAddressPostal'] = $secondaryAddress->getPostal();
                    $result[$ac->getId()]['academicSecondaryAddressCity'] = $secondaryAddress->getCity();
                    $result[$ac->getId()]['academicSecondaryAddressCountry'] = $secondaryAddress->getCountry();
                } else {
                    $result[$ac->getId()]['academicSecondaryAddressStreet'] = '';
                    $result[$ac->getId()]['academicSecondaryAddressNumber'] = '';
                    $result[$ac->getId()]['academicSecondaryAddressMailbox'] = '';
                    $result[$ac->getId()]['academicSecondaryAddressPostal'] = '';
                    $result[$ac->getId()]['academicSecondaryAddressCity'] = '';
                    $result[$ac->getId()]['academicSecondaryAddressCountry'] = '';
                }

                $result[$ac->getId()]['study'] = $study->getTitle();
            }
        }

        parent::__construct($headers, $result);
    }
}
