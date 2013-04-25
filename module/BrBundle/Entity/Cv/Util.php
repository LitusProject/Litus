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

namespace BrBundle\Entity\Cv;

use CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\EntityManager;

/**
 * A Util class providing functions to retrieve the cv book data in a common way.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Util
{
    public static function getGrouped(EntityManager $entityManager, AcademicYear $academicYear) {

        $groups = $entityManager
            ->getRepository('SyllabusBundle\Entity\Group')
            ->findAllCvBook();

        $result = array();
        foreach ($groups as $group) {

            $entries = $entityManager
                ->getRepository('BrBundle\Entity\Cv\Entry')
                ->findAllByGroupAndAcademicYear($group, $academicYear);

            if (count($entries) > 0) {
                $result[] = array(
                    'id' => 'group-' . $group->getId(),
                    'name' => $group->getName(),
                    'entries' => $entries,
                );
            }
        }

        // Add all studies that are not in a cv book group.
        $cvStudies = $entityManager
            ->getRepository('BrBundle\Entity\Cv\Entry')
            ->findAllUngroupedStudies();

        foreach ($cvStudies as $study) {

            $entries = $entityManager
                ->getRepository('BrBundle\Entity\Cv\Entry')
                ->findAllByStudyAndAcademicYear($study, $academicYear);

            if (count($entries) > 0) {
                $result[] = array(
                    'id' => 'study-' . $study->getId(),
                    'name' => $study->getFullTitle(),
                    'entries' => $entries,
                );
            }

        }

        return $result;
    }
}
