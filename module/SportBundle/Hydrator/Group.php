<?php

namespace SportBundle\Hydrator;

use CommonBundle\Component\Hydrator\Exception\InvalidObjectException;
use SportBundle\Entity\Group as GroupEntity;
use SportBundle\Entity\Runner as RunnerEntity;

class Group extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
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

                if ($repositoryCheck === null) {
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

                    if ($memberData['university_identification'] != '') {
                        $newRunner->setRunnerIdentification($memberData['university_identification']);
                    }

                    $this->getEntityManager()->persist($newRunner);

                    $groupMembers[] = $newRunner;
                } else {
                    if ($repositoryCheck->getGroup() === null) {
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
        if ($object === null) {
            return array();
        }

        $data = array();
        $data['isSpeedyGroup'] = $object->getIsSpeedyGroup();

        return $data;
    }
}
