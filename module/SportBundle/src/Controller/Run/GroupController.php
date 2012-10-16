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

namespace SportBundle\Controller\Run;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\AcademicYear,
    DateTime,
    DateInterval,
    SportBundle\Entity\Group,
    SportBundle\Entity\Runner,
    SportBundle\Form\Group\Add as AddForm,
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

        $form = new AddForm($this->getEntityManager(), $allMembers);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            foreach ($allMembers as $memberNb) {
                if (
                    '' != $formData['university_identification_' . $memberNb]
                        && !isset($formData['first_name_' . $memberNb])
                        && !isset($formData['last_name_' . $memberNb])
                ) {
                    $academic = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\Users\People\Academic')
                        ->findOneByUniversityIdentification($formData['university_identification_' . $memberNb]);

                    $formData['first_name_' . $memberNb] = $academic->getFirstName();
                    $formData['last_name_' . $memberNb] = $academic->getLastName();
                }
            }
            $form->setData($formData);

            if ($form->isValid()) {
                $createGroup = true;
                foreach ($allMembers as $memberNb) {
                    if ('' != $formData['university_identification_' . $memberNb]) {
                        if ('' == $formData['first_name_' . $memberNb] && '' == $formData['last_name_' . $memberNb]) {
                            if (true === $createGroup)
                                $createGroup = false;
                        }
                    } else {
                        $memberNbKey = array_keys($allMembers, $memberNb);
                        unset(
                            $allMembers[$memberNbKey[0]]
                        );
                    }
                }

                if ($createGroup) {
                    $newGroup = new Group(
                        $this->_getAcademicYear(),
                        $formData['group_name'],
                        array(
                            $formData['happy_hour_one'],
                            $formData['happy_hour_two']
                        )
                    );

                    $groupMembers = array();
                    foreach ($allMembers as $memberNb) {
                        $repositoryCheck = $this->getEntityManager()
                            ->getRepository('SportBundle\Entity\Runner')
                            ->findOneByUniversityIdentification($formData['university_identification_' . $memberNb]);

                        if (null === $repositoryCheck) {
                            $academic = $this->getEntityManager()
                                ->getRepository('CommonBundle\Entity\Users\People\Academic')
                                ->findOneByUniversityIdentification($formData['university_identification_' . $memberNb]);

                            $newRunner = new Runner(
                                $this->_getAcademicYear(),
                                $formData['first_name_' . $memberNb],
                                $formData['last_name_' . $memberNb],
                                $newGroup,
                                $academic
                            );

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

                        $this->flashMessenger()->addMessage(
                            new FlashMessage(
                                FlashMessage::SUCCESS,
                                'Success',
                                'The group was successfully created!'
                            )
                        );

                        $this->redirect()->toRoute(
                            'run_index',
                            array(
                                'action' => 'index'
                            )
                        );
                    }
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
                ->getRepository('CommonBundle\Entity\Users\People\Academic')
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

    private function _getAcademicYear()
    {
        if (null === $this->getParam('academicyear')) {
            $startAcademicYear = AcademicYear::getStartOfAcademicYear();

            $start = new DateTime(
                str_replace(
                    '{{ year }}',
                    $startAcademicYear->format('Y'),
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('start_organization_year')
                )
            );

            $next = clone $start;
            $next->add(new DateInterval('P1Y'));
            if ($next <= new DateTime())
                $start = $next;
        } else {
            $startAcademicYear = AcademicYear::getDateTime($this->getParam('academicyear'));

            $start = new DateTime(
                str_replace(
                    '{{ year }}',
                    $startAcademicYear->format('Y'),
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('start_organization_year')
                )
            );
        }
        $startAcademicYear->setTime(0, 0);

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByStart($start);

        if (null === $academicYear) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No academic year was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_shift_counter',
                array(
                    'action' => 'index'
                )
            );

            return;
        }

        return $academicYear;
    }
}
