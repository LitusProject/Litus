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

namespace SportBundle\Controller\Run;

use SportBundle\Entity\Group,
    Zend\View\Model\ViewModel;

/**
 * GroupController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class GroupController extends \SportBundle\Component\Controller\RunController
{
    /**
	 * @param integer $startTime
	 * @return array
	 */
    private function generateHappyHours($startTime)
    {
        $optionsArray = array();
        for ($i = 0; $i < 6; $i++) {
            $startInterval = ($startTime + 2 * $i) % 24;
            if ($startInterval < 10) {
                $startInterval = 0 . $startInterval;
            }

            $endInterval = ($startTime + 2 * ($i + 1)) % 24;
            if ($endInterval < 10) {
                $endInterval = 0 . $endInterval;
            }

            $optionKey = $startInterval . $endInterval;
            $optionValue = $startInterval . ':00 - ' . $endInterval . ':00';

            $optionsArray[$optionKey] = $optionValue;
        }

        $groups = $this->getEntityManager()
            ->getRepository('SportBundle\Entity\Group')
            ->findLast();

        return $this->cleanHappyHoursArray($optionsArray, $groups);
    }

    private function cleanHappyHoursArray(array $optionsArray, array $groups)
    {
        $countArray = array();
        foreach ($optionsArray as $key => $value) {
            $countArray[$key] = 0;
        }
        foreach ($groups as $group) {
            $happyHours = $group->getHappyHours();
            if (isset($countArray[$happyHours[0]])) {
                $countArray[$happyHours[0]]++;
            }
            if (isset($countArray[$happyHours[1]])) {
                $countArray[$happyHours[1]]++;
            }
        }
        $returnArray = array();
        foreach ($optionsArray as $key => $value) {
            if ($countArray[$key] < 3) {
                $returnArray[$key] = $value;
            }
        }

        return $returnArray;
    }

    public function addAction()
    {
        $happyHours1 = $this->generateHappyHours(20);
        $happyHours2 = $this->generateHappyHours(8);
        if (count($happyHours1) == 0 || count($happyHours2) == 0) {
            return new ViewModel(array(
                'full' => true,
            ));
        } else {
            $form = $this->getForm('sport_group_add', array(
                'happyHours1' => $happyHours1,
                'happyHours2' => $happyHours2,
            ));

            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                $alreadyInGroup = false;

                foreach (Group::$allMembers as $memberNb) {
                    $memberData = $formData['user_' . $memberNb];

                    $runner = $this->getEntityManager()
                        ->getRepository('SportBundle\Entity\Runner')
                        ->findOneByUniversityIdentification($memberData['university_identification']);

                    if (null === $runner) {
                        $runner = $this->getEntityManager()
                            ->getRepository('SportBundle\Entity\Runner')
                            ->findOneByRunnerIdentification($memberData['university_identification']);
                    }

                    if (!null == $runner && !$runner->getGroup() == null) {
                        $alreadyInGroup = true;
                    }

                    if (
                        '' != $memberData['university_identification']
                        && !isset($memberData['first_name'])
                        && !isset($memberData['last_name'])
                    ) {
                        $academic = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\User\Person\Academic')
                            ->findOneByUniversityIdentification($memberData['university_identification']);

                        $memberData['first_name'] = $academic->getFirstName();
                        $memberData['last_name'] = $academic->getLastName();
                    }
                }

                if (!$alreadyInGroup) {
                    $form->setData($formData);

                    if ($form->isValid()) {
                        $group = $form->hydrateObject(
                            new Group($this->getCurrentAcademicYear())
                        );

                        if (null !== $group) {
                            $this->getEntityManager()->persist($group);

                            $this->getEntityManager()->flush();

                            $this->flashMessenger()->success(
                                'Success',
                                'The group was successfully created!'
                            );

                            $this->redirect()->toRoute(
                                'sport_run_index',
                                array(
                                    'action' => 'index',
                                )
                            );
                        }
                    }
                } else {
                    $this->flashMessenger()->error(
                        'Alert',
                        'One of the group members is already in a group!'
                    );

                    $this->redirect()->toRoute(
                        'sport_run_group',
                        array(
                            'action' => 'add',
                        )
                    );
                }
            }

            return new ViewModel(
                array(
                    'form' => $form,
                )
            );
        }
    }

    public function getNameAction()
    {
        $this->initAjax();

        if (8 == strlen($this->getParam('university_identification'))) {
            $person = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneByUniversityIdentification($this->getParam('university_identification'));

            if ($person === null) {
                $person = $this->getEntityManager()
                    ->getRepository('SportBundle\Entity\Runner')
                    ->findOneByRunnerIdentification($this->getParam('university_identification'));
            }

            if (null !== $person) {
                return new ViewModel(
                    array(
                        'result' => (object) array(
                            'status' => 'success',
                            'firstName' => $person->getFirstName(),
                            'lastName' => $person->getLastName(),
                        ),
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'result' => (object) array(),
            )
        );
    }
}
