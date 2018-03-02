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
            ->getRepository('SyllabusBundle\Entity\Group\StudyMap')
            ->findAllByGroupAndAcademicYear($group, $academicYear);

        $result = array();
        foreach ($mappings as $mapping) {
            $study = $mapping->getStudy();
            $enrollments = $entityManager
                ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
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

                if (null !== $primaryAddress) {
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

                if (null !== $secondaryAddress) {
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
