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

namespace SportBundle\Hydrator;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException,
    SportBundle\Entity\Group as GroupEntity,
    SportBundle\Entity\Runner as RunnerEntity;

class Group extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doHydrate(array $data, $object = null)
    {
        if (null === $object) {
            throw new InvalidObjectException();
        }

        $object->setName($data['group_information']['name'])
            ->setHappyHours(
            array(
                $data['group_information']['happy_hour_one'],
                $data['group_information']['happy_hour_two'],
            )
        );

        $groupMembers = array();
        foreach (GroupEntity::$allMembers as $memberNb) {
            $memberData = $data['user_' . $memberNb];
            if ($memberData['university_identification'] != '') {
                $repositoryCheck = $this->getEntityManager()
                    ->getRepository('SportBundle\Entity\Runner')
                    ->findOneByUniversityIdentification($memberData['university_identification']);

                if ($repositoryCheck === null) {
                    $repositoryCheck = $this->getEntityManager()
                        ->getRepository('SportBundle\Entity\Runner')
                        ->findOneByRunnerIdentification($memberData['university_identification']);
                }

                if (null === $repositoryCheck) {
                    $academic = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Person\Academic')
                        ->findOneByUniversityIdentification($memberData['university_identification']);

                    $department = $this->getEntityManager()
                        ->getRepository('SportBundle\Entity\Department')
                        ->findOneById($memberData['department']);

                    $newRunner = new RunnerEntity(
                        $memberData['first_name'],
                        $memberData['last_name'],
                        $object->getAcademicYear(),
                        $academic,
                        $object,
                        $department
                    );

                    $newRunner->setGroup($object);

                    if ('' != $memberData['university_identification']) {
                        $newRunner->setRunnerIdentification($memberData['university_identification']);
                    }

                    $this->getEntityManager()->persist($newRunner);

                    $groupMembers[] = $newRunner;
                } else {
                    if (null === $repositoryCheck->getGroup()) {
                        $repositoryCheck->setGroup($object);
                        $groupMembers[] = $repositoryCheck;
                    }
                }
            }
        }

        return $object;
    }

    protected function doExtract($object = null)
    {
        if (null === $object) {
            return array();
        }

        $data = array();
        $data['isSpeedyGroup'] = $object->getIsSpeedyGroup();

        return $data;
    }
}
