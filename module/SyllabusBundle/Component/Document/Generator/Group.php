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

namespace SyllabusBundle\Component\Document\Generator;

use CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\EntityManager,
    SyllabusBundle\Entity\Group as GroupEntity;

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
            ->getRepository('SyllabusBundle\Entity\StudyGroupMap')
            ->findAllByGroupAndAcademicYear($group, $academicYear);

        $result = array();
        foreach ($mappings as $mapping) {
            $study = $mapping->getStudy();
            $enrollments = $entityManager
                ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
                ->findAllByStudyAndAcademicYear($study, $academicYear);

            foreach ($enrollments as $enrollment) {
                $ac = $enrollment->getAcademic();

                $primaryAddress = $ac->getPrimaryAddress();
                $secondaryAddress = $ac->getSecondaryAddress();

                $result[$ac->getId()] = array(
                    'academicFirstName'               => $ac->getFirstName(),
                    'academicLastName'                => $ac->getLastName(),
                    'academicEmail'                   => $ac->getEmail(),
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
                    'study'                           => $study->getFullTitle(),
                );
            }
        }

        parent::__construct($headers, $result);
    }
}
