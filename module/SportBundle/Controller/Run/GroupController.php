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
    SportBundle\Entity\Runner,
    Zend\View\Model\ViewModel;

/**
 * GroupController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class GroupController extends \SportBundle\Component\Controller\RunController
{
    public function addAction()
    {
        $allMembers = array(
            'one', 'two', 'three', 'four', 'five'
        );

        $form = $this->getForm('sport_group_add', array('all_members' => $allMembers));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            foreach ($allMembers as $memberNb) {
                $memberData = $formData['user_' . $memberNb];

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
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                $groupData = $formData['group_information'];

                $newGroup = new Group(
                    $this->getCurrentAcademicYear(),
                    $groupData['name'],
                    array(
                        $groupData['happy_hour_one'],
                        $groupData['happy_hour_two']
                    )
                );

                $groupMembers = array();
                foreach ($allMembers as $memberNb) {
                    $memberData = $formData['user_' . $memberNb];

                    $repositoryCheck = $this->getEntityManager()
                        ->getRepository('SportBundle\Entity\Runner')
                        ->findOneByUniversityIdentification($memberData['university_identification']);

                    if (null === $repositoryCheck) {
                        $academic = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\User\Person\Academic')
                            ->findOneByUniversityIdentification($memberData['university_identification']);

                        $department = $this->getEntityManager()
                            ->getRepository('SportBundle\Entity\Department')
                            ->findOneById($memberData['department']);

                        $newRunner = new Runner(
                            $memberData['first_name'],
                            $memberData['last_name'],
                            $academic,
                            $newGroup,
                            $department
                        );

                        $this->getEntityManager()->persist($newRunner);

                        $groupMembers[] = $newRunner;
                    } else {
                        if (null === $repositoryCheck->getGroup()) {
                            $repositoryCheck->setGroup($newGroup);
                            $groupMembers[] = $repositoryCheck;
                        }
                    }
                }

                if (0 != count($groupMembers)) {
                    $this->getEntityManager()->persist($newGroup);

                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'Success',
                        'The group was successfully created!'
                    );

                    $this->redirect()->toRoute(
                        'sport_run_index',
                        array(
                            'action' => 'index'
                        )
                    );
                }
            }
        }

        return new ViewModel(
            array(
                'form' => $form
            )
        );
    }

    public function getNameAction()
    {
        $this->initAjax();

        if (8 == strlen($this->getParam('university_identification'))) {
            $academic = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneByUniversityIdentification($this->getParam('university_identification'));

            if (null !== $academic) {
                return new ViewModel(
                    array(
                        'result' => (object) array(
                            'status' => 'success',
                            'firstName' => $academic->getFirstName(),
                            'lastName' => $academic->getLastName()
                        )
                    )
                );
            }
        }

        return new ViewModel();
    }
}
