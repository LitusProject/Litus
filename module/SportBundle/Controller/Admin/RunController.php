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

namespace SportBundle\Controller\Admin;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Component\Util\WebSocket as WebSocketUtil,
    DateInterval,
    DateTime,
    SportBundle\Entity\Runner,
    SportBundle\Form\Admin\Group\Edit as EditGroupForm,
    SportBundle\Form\Admin\Runner\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * RunController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class RunController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function queueAction()
    {
        return new ViewModel(
            array(
                'socketUrl' => $this->getSocketUrl(),
                'authSession' => $this->getAuthentication()
                    ->getSessionObject(),
                'key' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('sport.queue_socket_key'),
            )
        );
    }

    public function lapsAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('SportBundle\Entity\Lap')
                ->findAllPreviousLapsQuery($this->_getAcademicYear()),
            $this->getParam('page')
        );

        foreach ($paginator as $lap) {
            $lap->setEntityManager($this->getEntityManager());
        }

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'academicYear' => $this->_getAcademicYear(),
            )
        );
    }

    public function groupsAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'SportBundle\Entity\Group',
            $this->getParam('page'),
            array(
                'academicYear' => $this->_getAcademicYear(),
            ),
            array(
                'name' => 'ASC',
            )
        );

        foreach ($paginator as $group) {
            $group->setEntityManager($this->getEntityManager());
        }

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'academicYear' => $this->_getAcademicYear(),
            )
        );
    }

    public function editGroupAction()
    {
        if (!($group = $this->_getGroup())) {
            return new ViewModel();
        }

        $form = new EditGroupForm($this->getEntityManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                if (!isset($formData['person_id']) || $formData['person_id'] == '') {
                    $academic = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Person\Academic')
                        ->findOneByUsername($formData['person_name']);
                } else {
                    $academic = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\User\Person\Academic')
                        ->findOneById($formData['person_id']);
                }

                $repositoryCheck = $this->getEntityManager()
                    ->getRepository('SportBundle\Entity\Runner')
                    ->findOneByUniversityIdentification($academic->getUniversityIdentification());

                if (null === $repositoryCheck) {
                    $department = $this->getEntityManager()
                        ->getRepository('SportBundle\Entity\Department')
                        ->findOneById($formData['department']);

                    $newRunner = new Runner(
                        $academic->getFirstName(),
                        $academic->getLastName(),
                        $academic,
                        $group,
                        $department
                    );

                    $this->getEntityManager()->persist($newRunner);

                    $groupMembers[] = $newRunner;
                } else {
                    $repositoryCheck->setGroup($group);
                }

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The group was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'sport_admin_run',
                    array(
                        'action' => 'groups',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
                'members' => $group->getMembers(),
            )
        );
    }

    public function departmentsAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'SportBundle\Entity\Department',
            $this->getParam('page'),
            array(),
            array(
                'name' => 'ASC',
            )
        );

        foreach ($paginator as $department) {
            $department->setEntityManager($this->getEntityManager());
        }

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'academicYear' => $this->_getAcademicYear(),
            )
        );
    }

    public function rewardAction()
    {
        $laps = $this->getEntityManager()
            ->getRepository('SportBundle\Entity\Lap')
            ->findByAcademicYear($this->_getAcademicYear());

        $rewardTimeLimit = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('sport.reward_time_limit');

        $rewardRunners = array();
        foreach ($laps as $lap) {
            if (
                    null !== $lap->getEndTime()
                    && $this->_convertDateIntervalToSeconds($lap->getLapTime()) <= $rewardTimeLimit
                )
            {
                $runner = $lap->getRunner();
                $runner->setEntityManager($this->getEntityManager());
                if (isset($rewardRunners[$runner->getId()])) {
                    $rewardRunners[$runner->getId()]['count']++;
                } else {
                    $rewardRunners[$runner->getId()] = array(
                        'name'  => $runner->getFullName(),
                        'count' => 1,
                    );
                }
            }
        }

        $paginator = $this->paginator()->createFromArray(
            $rewardRunners,
            $this->getParam('page')
        );

        $d1 = new DateTime();
        $d2 = new DateTime();
        $d2->add(new DateInterval('PT'.$rewardTimeLimit.'S'));
        $timeLimit = $d2->diff($d1);

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'timeLimit' => $timeLimit,
            )
        );
    }

    public function identificationAction()
    {
        $runners = $this->getEntityManager()
            ->getRepository('SportBundle\Entity\Runner')
            ->findAllWithoutIdentification($this->_getAcademicYear());

        return new ViewModel(
            array(
                'runners' => $runners,
            )
        );
    }

    public function editAction()
    {
        if (!($runner = $this->_getRunner())) {
            return new ViewModel();
        }

        $form = new EditForm();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $runner->setRunnerIdentification($formData['runner_identification']);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The runner was successfully updated!'
                );

                $this->redirect()->toRoute(
                    'sport_admin_run',
                    array(
                        'action' => 'identification',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'runner' => $runner,
                'form' => $form,
            )
        );
    }

    public function killSocketAction()
    {
        $this->initAjax();

        return new ViewModel(
            array(
                'result' => WebSocketUtil::kill($this->getEntityManager(), 'sport:run-queue'),
            )
        );
    }

    public function updateAction()
    {
        return new ViewModel(
            array(
                'socketUrl' => $this->getSocketUrl(),
                'authSession' => $this->getAuthentication()
                    ->getSessionObject(),
                'key' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('sport.queue_socket_key'),
            )
        );
    }

    public function runnersAction()
    {
        $runners = $this->getEntityManager()
                ->getRepository('SportBundle\Entity\Lap')
                ->getRunnersAndCount($this->_getAcademicYear());

        $runnersList = array();
        foreach ($runners as $runner)
        {
            $runnerEntity = $this->getEntityManager()
                ->getRepository('SportBundle\Entity\Runner')
                ->findOneById($runner['runner']);

            $runnerEntity->setEntityManager($this->getEntityManager());

            $name = $runnerEntity->getFullName();
            $points = $runnerEntity->getPoints($this->_getAcademicYear());

            $totalTime = 0;
            $laps = $runnerEntity->getLaps($this->_getAcademicYear());
            foreach ($laps as $lap)
            {
                $lapTime = $lap->getLapTime();
                $totalTime = $totalTime + $lapTime->h*3600 + $lapTime->i*60 + $lapTime->s;
            }

            $d1 = new DateTime();
            $d2 = new DateTime();
            $d2->add(new DateInterval('PT'.round($totalTime/count($laps)).'S'));
            $avarage = $d2->diff($d1);

            array_push($runnersList,
                array(
                    'name' => $name,
                    'laps' => count($laps),
                    'points' => $points,
                    'avarage' => $avarage
                )
            );
        }

        $paginator = $this->paginator()->createFromArray(
            $runnersList,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'academicYear' => $this->_getAcademicYear(),
            )
        );
    }

    public function _getRunner()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the runner!'
            );

            $this->redirect()->toRoute(
                'sport_admin_run',
                array(
                    'action' => 'identification',
                )
            );

            return;
        }

        $runner = $this->getEntityManager()
            ->getRepository('SportBundle\Entity\Runner')
            ->findOneById($this->getParam('id'));

        if (null === $runner) {
            $this->flashMessenger()->error(
                'Error',
                'No runner with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'sport_admin_run',
                array(
                    'action' => 'identification',
                )
            );

            return;
        }

        return $runner;
    }

    private function _getAcademicYear()
    {
        if (null === $this->getParam('academicyear')) {
            return $this->getCurrentAcademicYear();
        }

        $start = AcademicYear::getDateTime($this->getParam('academicyear'));
        $start->setTime(0, 0);

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByUniversityStart($start);

        if (null === $academicYear) {
            $this->flashMessenger()->error(
                'Error',
                'No academic year was found!'
            );

            $this->redirect()->toRoute(
                'sport_admin_run',
                array(
                    'action' => 'queue',
                )
            );

            return;
        }

        return $academicYear;
    }

    /**
     * Returns the WebSocket URL.
     *
     * @return string
     */
    protected function getSocketUrl()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('sport.queue_socket_public');
    }

    private function _convertDateIntervalToSeconds(DateInterval $interval)
    {
        return $interval->h*3600 + $interval->i*60 + $interval->s;
    }

    /**
     * @return Group
     */
    private function _getGroup()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the group!'
            );

            $this->redirect()->toRoute(
                'sport_admin_run',
                array(
                    'action' => 'groups',
                )
            );

            return;
        }

        $group = $this->getEntityManager()
            ->getRepository('SportBundle\Entity\Group')
            ->findOneById($this->getParam('id'));

        if (null === $group) {
            $this->flashMessenger()->error(
                'Error',
                'No group with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'sport_admin_run',
                array(
                    'action' => 'groups',
                )
            );

            return;
        }

        return $group;
    }
}
