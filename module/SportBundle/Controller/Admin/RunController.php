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
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    DateInterval,
    DateTime,
    SportBundle\Entity\Group,
    SportBundle\Entity\Runner,
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
                ->findAllPreviousLapsQuery($this->getAcademicYearEntity()),
            $this->getParam('page')
        );

        foreach ($paginator as $lap) {
            $lap->setEntityManager($this->getEntityManager());
        }

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'academicYear' => $this->getAcademicYearEntity(),
            )
        );
    }

    public function groupsAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'SportBundle\Entity\Group',
            $this->getParam('page'),
            array(
                'academicYear' => $this->getAcademicYearEntity(),
            ),
            array(
                'name' => 'ASC',
            )
        );

        foreach ($paginator as $group) {
			$this->testFunction($group);
            $group->setEntityManager($this->getEntityManager());
            
        }
		
        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'academicYear' => $this->getAcademicYearEntity(),
            )
        );
    }
	
	public function editSpeedyGroupAction()
	{	
        if (!($group = $this->getGroupEntity())) {
            return new ViewModel();
        }
        echo($group->getName());
		
        $form = $this->getForm('sport_group_editspeedygroup', array('group' => $group));
        $form->setGroup($group);
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);
            if ($form->isValid()) {
                $formData = $form->getData();
				$group->setIsSpeedyGroup($formData['isSpeedyGroup']);
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
                'group' => $group,
            )
        );
        }
        
	
    public function editGroupAction()
    {	
        if (!($group = $this->getGroupEntity())) {
            return new ViewModel();
        }


        $form = $this->getForm('sport_group_edit');
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                $academic = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic')
                    ->findOneById($formData['person']['id']);
		
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
                        $group->getAcademicYear(),
                        $academic,
                        $group,
                        $department
                    );

                    $this->getEntityManager()->persist($newRunner);
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
                'group' => $group,
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
                'academicYear' => $this->getAcademicYearEntity(),
            )
        );
    }

    public function rewardAction()
    {
        $laps = $this->getEntityManager()
            ->getRepository('SportBundle\Entity\Lap')
            ->findByAcademicYear($this->getAcademicYearEntity());

        $rewardTimeLimit = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('sport.reward_time_limit');

        $rewardRunners = array();
        foreach ($laps as $lap) {
            if (
                    null !== $lap->getEndTime()
                    && $this->convertDateIntervalToSeconds($lap->getLapTime()) <= $rewardTimeLimit
                ) {
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
        $d2->add(new DateInterval('PT' . $rewardTimeLimit . 'S'));
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
            ->findAllWithoutIdentification($this->getAcademicYearEntity());

        return new ViewModel(
            array(
                'runners' => $runners,
            )
        );
    }

    public function editAction()
    {
        if (!($runner = $this->getRunnerEntity())) {
            return new ViewModel();
        }

        $form = $this->getForm('sport_runner_edit', $runner);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
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
                ->getRunnersAndCount($this->getAcademicYearEntity());

        $runnersList = array();
        foreach ($runners as $runner) {
            $runnerEntity = $this->getEntityManager()
                ->getRepository('SportBundle\Entity\Runner')
                ->findOneById($runner['runner']);

            $runnerEntity->setEntityManager($this->getEntityManager());

            $name = $runnerEntity->getFullName();
            $points = $runnerEntity->getPoints($this->getAcademicYearEntity());

            $totalTime = 0;
            $laps = $runnerEntity->getLaps($this->getAcademicYearEntity());
            foreach ($laps as $lap) {
                $lapTime = $lap->getLapTime();
                $totalTime = $totalTime + $lapTime->h * 3600 + $lapTime->i * 60 + $lapTime->s;
            }

            $d1 = new DateTime();
            $d2 = new DateTime();
            $d2->add(new DateInterval('PT' . round($totalTime / count($laps)) . 'S'));
            $avarage = $d2->diff($d1);

            array_push($runnersList,
                array(
                    'name' => $name,
                    'laps' => count($laps),
                    'points' => $points,
                    'avarage' => $avarage,
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
                'academicYear' => $this->getAcademicYearEntity(),
            )
        );
    }

    /**
     * @return Runner|null
     */
    private function getRunnerEntity()
    {
        $runner = $this->getEntityById('SportBundle\Entity\Runner');

        if (!($runner instanceof Runner)) {
            $this->flashMessenger()->error(
                'Error',
                'No runner was found!'
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

    /**
     * @return AcademicYearEntity|null
     */
    private function getAcademicYearEntity()
    {
        if (null === $this->getParam('academicyear')) {
            return $this->getCurrentAcademicYear();
        }

        $start = AcademicYear::getDateTime($this->getParam('academicyear'));
        $start->setTime(0, 0);

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByUniversityStart($start);

        if (!($academicYear instanceof AcademicYearEntity)) {
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

    /**
     * @param  DateInterval $interval
     * @return int
     */
    private function convertDateIntervalToSeconds(DateInterval $interval)
    {
        return $interval->h * 3600 + $interval->i * 60 + $interval->s;
    }

    /**
     * @return Group|null
     */
    private function getGroupEntity()
    {
        $group = $this->getEntityById('SportBundle\Entity\Group');

        if (!($group instanceof Group)) {
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
    public function testFunction($group){
		 echo('testing group: ' .$group->getName());
         $happyHours = $group->getHappyHours();
                for ($i = 0; isset($happyHours[$i]); $i++) {
                         if ($group -> getIsSpeedyGroup() && $group->isNightShift($happyHours[$i])){
								echo('JA');
						}
						else{
								echo('NEE');
						}		 
                    }
                   
    }
}
