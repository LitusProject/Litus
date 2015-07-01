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

namespace SportBundle\Component\WebSocket\Run;

use CommonBundle\Component\Acl\Acl,
    CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Component\WebSocket\User,
    CommonBundle\Entity\User\Person,
    DateInterval,
    DateTime,
    Doctrine\ORM\EntityManager,
    SportBundle\Entity\Lap,
    SportBundle\Entity\Runner;

/**
 * This is the server to handle all requests by the websocket protocol for the Queue.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Queue extends \CommonBundle\Component\WebSocket\Server
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var int Minimum runtime required (in seconds)
     */
    private static $minLapTime = 60;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct(
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('sport.queue_socket_file')
        );

        $this->entityManager = $entityManager;
    }

    /**
     * Do action when a new user has connected to this socket
     *
     * @param User $user
     */
    protected function onConnect(User $user)
    {
        $this->sendQueue($user, $this->getJsonQueue());
    }

    /**
     * Parse received text
     *
     * @param User   $user
     * @param string $data
     */
    protected function gotText(User $user, $data)
    {
        $this->entityManager->clear();

        $key = $this->entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('sport.queue_socket_key');

        $command = json_decode($data);

        if (null == $command) {
            return;
        }

        switch ($command->command) {
            case 'action':
                if ($this->isAuthenticated($user->getSocket())) {
                    $this->gotAction($command);
                }
                break;
            case 'initialize':
                if (!isset($command->key) || $command->key != $key) {
                    $this->removeUser($user);
                    $now = new DateTime();
                    echo '[' . $now->format('Y-m-d H:i:s') . '] WebSocket connection with invalid key.' . PHP_EOL;

                    return;
                }

                if ('development' != getenv('APPLICATION_ENV')) {
                    if (!isset($command->authSession)) {
                        $this->removeUser($user);
                        $now = new DateTime();
                        echo '[' . $now->format('Y-m-d H:i:s') . '] WebSocket connection with invalid auth session.' . PHP_EOL;

                        return;
                    }

                    $authSession = $this->entityManager
                        ->getRepository('CommonBundle\Entity\User\Session')
                        ->findOneById($command->authSession);

                    $allowed = false;
                    if ($authSession) {
                        $acl = new Acl($this->entityManager);

                        foreach ($authSession->getPerson()->getRoles() as $role) {
                            if (
                                $role->isAllowed(
                                    $acl, 'sport_run_screen', 'index'
                                )
                            ) {
                                $allowed = true;
                            }
                        }
                    }

                    if (null == $authSession || !$allowed) {
                        $this->removeUser($user);
                        $now = new DateTime();
                        echo '[' . $now->format('Y-m-d H:i:s') . '] WebSocket connection with invalid auth session.' . PHP_EOL;

                        return;
                    }
                }

                $this->addAuthenticated($user->getSocket());

                $this->sendQueue($user, $this->getJsonQueue());
                break;
        }
    }

    /**
     * Parse received binary
     *
     * @param User   $user
     * @param string $data
     */
    protected function gotBin(User $user, $data)
    {
    }

    /**
     * Parse action text
     *
     * @param string $command
     */
    private function gotAction($command)
    {
        switch ($command->action) {
            case 'reloadQueue':
                $this->sendQueueToAll();
                break;
            case 'addToQueue':
                $this->addToQueue($command);
                break;
            case 'deleteLap':
                $this->deleteLap($command);
                break;
            case 'startLap':
                $this->startLap();
                break;
        }

        $this->sendQueueToAll();
    }

    /**
     * Send queue to one user
     *
     * @param User   $user
     * @param string $json
     */
    private function sendQueue(User $user, $json)
    {
        $this->sendText($user, $json);
    }

    /**
     * Send queue to all users
     */
    private function sendQueueToAll()
    {
        $queue = $this->getJsonQueue();
        foreach ($this->getUsers() as $user) {
            $this->sendQueue($user, $queue);
        }
    }

    /**
     * @param string $data
     */
    private function addToQueue($data)
    {
        if ('' != $data->universityIdentification
            && '' == $data->firstName
            && '' == $data->lastName
        ) {
            $academic = $this->entityManager
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneByUniversityIdentification($data->universityIdentification);
            if (null !== $academic) {
                $data->firstName = $academic->getFirstName();
                $data->lastName = $academic->getLastName();
            }
        }

        $runner = $this->entityManager
            ->getRepository('SportBundle\Entity\Runner')
            ->findOneByUniversityIdentification($data->universityIdentification);

        if (null === $runner) {
            $runner = $this->entityManager
                ->getRepository('SportBundle\Entity\Runner')
                ->findOneByRunnerIdentification($data->universityIdentification);
        }

        if ($data->department !== '') {
            $department = $this->entityManager
            ->getRepository('SportBundle\Entity\Department')
            ->findOneById($data->department);
        } else {
            $department = null;
        }

        if (null === $runner) {
            $academic = $this->entityManager
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneByUniversityIdentification($data->universityIdentification);

            $runner = new Runner(
                $data->firstName,
                $data->lastName,
                $academic,
                null,
                $department
            );

            $runner->setRunnerIdentification($data->universityIdentification);
        }

        $lap = new Lap($this->getAcademicYear(), $runner);
        $this->entityManager->persist($lap);

        $this->entityManager->flush();
    }

    /**
     * @return string
     */
    private function getJsonQueue()
    {
        $nbLaps = $this->entityManager
            ->getRepository('SportBundle\Entity\Lap')
            ->countAll($this->getAcademicYear());

        $uniqueRunners = $this->entityManager
            ->getRepository('SportBundle\Entity\Lap')
            ->countRunners($this->getAcademicYear());

        $laps = array();
        $previousLaps = array_reverse(
            $this->entityManager
                ->getRepository('SportBundle\Entity\Lap')
                ->findPrevious($this->getAcademicYear(), 5)
        );
        foreach ($previousLaps as $lap) {
            $laps[] = $this->jsonLap($lap, 'previous');
        }

        $laps[] = $this->jsonLap($this->getCurrentLap(), 'current');

        $nextLaps = $this->entityManager
            ->getRepository('SportBundle\Entity\Lap')
            ->findNext($this->getAcademicYear(), 40);
        foreach ($nextLaps as $lap) {
            $laps[] = $this->jsonLap($lap, 'next');
        }

        $queueSize = sizeof($nextLaps);

        $fastestLap = $this->getFastestLap();
        $officialResults = $this->getOfficialResults();
        $averageLapTime = $this->getAverageLapTime();
        $groupsOfFriends = $this->getGroupsOfFriends();
        $mostLabs = $this->getMostFrequentRunners();

        $data = (object) array(
            'laps' => (object) array(
                'number' => (object) array(
                    'own' => $nbLaps,
                    'uniqueRunners' => $uniqueRunners,
                ),
                'queueSize' => $queueSize,
                'fastestRunner' => $fastestLap['runner'],
                'fastestTime' => $fastestLap['time'],
                'laps' => $laps,
                'officialResults' => $officialResults,
                'averageLapTime' => $averageLapTime,
                'groupsOfFriends' => $groupsOfFriends,
                'mostLaps' => $mostLabs,
            ),
        );

        return json_encode($data);
    }

    /**
     * @param  Lap|null    $lap
     * @param  string      $state
     * @return object|null
     */
    private function jsonLap(Lap $lap = null, $state = '')
    {
        if (null === $lap) {
            return null;
        }

        $lap->setEntityManager($this->entityManager);

        return (object) array(
            'id' => $lap->getId(),
            'fullName' => $lap->getRunner()->getFullName(),
            'firstName' => $lap->getRunner()->getFirstName(),
            'lastName' => $lap->getRunner()->getLastName(),
            'registrationTime' => $lap->getRegistrationTime()->format('d/m/Y H:i:s'),
            'lapTime' => (null !== $lap->getStartTime()) ? $lap->getLapTime()->format('%i:%S') : '',
            'points' => $lap->getPoints(),
            'state' => $state,
        );
    }

    /**
     * @param string $data
     */
    private function deleteLap($data)
    {
        $lap = $this->entityManager
            ->getRepository('SportBundle\Entity\Lap')
            ->findOneById($data->id);

        $this->entityManager->remove($lap);
        $this->entityManager->flush();
    }

    /**
     * @return null
     */
    private function startLap()
    {
        if (null !== $this->getCurrentLap()) {
            $this->getCurrentLap()->stop();
        }

        if (null !== $this->getNextLap()) {
            $this->getNextLap()->start();
        }

        $this->entityManager->flush();
    }

    /**
     * @return null|Lap
     */
    private function getCurrentLap()
    {
        return $this->entityManager
            ->getRepository('SportBundle\Entity\Lap')
            ->findCurrent($this->getAcademicYear());
    }

    /**
     * @return null|Lap
     */
    private function getNextLap()
    {
        return $this->entityManager
            ->getRepository('SportBundle\Entity\Lap')
            ->findNext($this->getAcademicYear());
    }

    /**
     * @return null|array
     */
    private function getFastestLap()
    {
        $previousLaps = array_reverse(
            $this->entityManager
                ->getRepository('SportBundle\Entity\Lap')
                ->findAllPreviousLaps($this->getAcademicYear())
        );

        $time = null;
        $fastestLap = null;

        foreach ($previousLaps as $lap) {
            if ($this->isValidLapTime($lap->getLapTime()) && strpos(strtolower($lap->getRunner()->getAcademic()->getFullName()),'vtk gent') === false) {
                if ($fastestLap == null) {
                    $time = $lap->getLapTime();
                    $fastestLap = $lap;
                } elseif ($this->convertDateIntervalToSeconds($lap->getLapTime()) < $this->convertDateIntervalToSeconds($time)) {
                    $time = $lap->getLapTime();
                    $fastestLap = $lap;
                }
            }
        }
        if ($fastestLap !== null) {
            return array(
                'time' => $fastestLap->getLapTime()->format('%i:%S'),
                'runner' => $fastestLap->getRunner()->getAcademic()->getFullName(),
            );
        }

        return null;
    }

    /**
     * @param  int   $number
     * @return array
     */
    private function getMostFrequentRunners($number = 3)
    {
        $runners = $this->entityManager
                ->getRepository('SportBundle\Entity\Lap')
                ->getRunnersAndCount($this->getAcademicYear());

        $nbResults = 0;
        $index = 0;
        $mostLaps = array();
        while (isset($runners[$index]) && $nbResults < $number) {
            $runner = $this->entityManager
                ->getRepository('SportBundle\Entity\Runner')
                ->findOneById($runners[$index]['runner']);
            if (strpos(strtolower($runner->getAcademic()->getFullName()), 'vtk gent') === false) {
                array_push($mostLaps, array(
                        'name' => $runner->getAcademic()->getFullName(),
                        'laps' => $runners[$index]['lapCount'],
                    )
                );
                $nbResults++;
            }
            $index++;
        }

        return $mostLaps;
    }

    /**
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    private function getAcademicYear()
    {
        return AcademicYear::getUniversityYear($this->entityManager);
    }

    /**
     * @return array|null
     */
    private function getOfficialResults()
    {
        $fileContents = @file_get_contents('data/cache/run-' . md5('run_result_page'));

        $resultPage = null;
        if (false !== $fileContents) {
            $resultPage = (array) json_decode($fileContents);
        }

        if ($resultPage) {
            $teamId = $this->entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('sport.run_team_id');

            $currentPlace = null;
            $teamData = null;

            foreach ($resultPage['teams'] as $place => $team) {
                if ($team->nb == $teamId) {
                    $currentPlace = $place;
                    $teamData = $team;
                }
            }

            if (null !== $teamData) {
                $difference = 0;
                if (null !== $currentPlace) {
                    if ($currentPlace != 0) {
                        $firstData = $resultPage['teams'][0];
                        $difference = round(($firstData->laps + $firstData->position) - ($teamData->laps + $teamData->position), 2);
                    } else {
                        $secondData = $resultPage['teams'][1];
                        $difference = round(($teamData->laps + $teamData->position) - ($secondData->laps + $teamData->position), 2);
                    }
                }

                $lapsPerSecond = 1/($resultPage['lap']/($teamData->speed/3.6));

                return array(
                    'lapLength' => $resultPage['lap'],
                    'nbLaps' => $teamData->laps,
                    'position' => round($teamData->position * 100),
                    'speed' => round($teamData->speed, 2),
                    'lapsPerSecond' => round($lapsPerSecond, 4),
                    'difference' => $difference,
                    'place' => $currentPlace,
                );
            }
        }

        return null;
    }

    /**
     * @param  int        $number
     * @return array|null
     */
    private function getGroupsOfFriends($number = 5)
    {
        $groups = $this->entityManager
            ->getRepository('SportBundle\Entity\Group')
            ->findAllByAcademicYear($this->getAcademicYear());

        $returnArray = array();
        $sort = array();
        foreach ($groups as $group) {
            $group->setEntityManager($this->entityManager);

            $array = (object) array(
                'name' => $group->getName(),
                'points' => $group->getPoints($this->getAcademicYear()),
            );

            $returnArray[] = $array;
            $sort[] = $array->points;
        }

        array_multisort($sort, $returnArray);
        $returnArray = array_reverse($returnArray);
        $returnArray = array_splice($returnArray, 0, $number);

        return $returnArray;
    }

    /**
     * @return string
     */
    private function getAverageLapTime()
    {
        $laps = $this->entityManager
            ->getRepository('SportBundle\Entity\Lap')
            ->findAllPreviousLaps($this->getAcademicYear());

        $total = 0;
        foreach ($laps as $lap) {
            $total += $this->convertDateIntervalToSeconds($lap->getLapTime());
        }
        if (count($laps) == 0) {
            $average = 0;
        } else {
            $average = $total / count($laps);
        }

        return floor($average / 60) . ':' . ($average % 60 < 10 ? '0' . $average % 60 : $average % 60);
    }

    /**
     * @return int
     */
    private function convertDateIntervalToSeconds(DateInterval $interval)
    {
        return $interval->h*3600 + $interval->i*60 + $interval->s;
    }

    /**
     * @return boolean
     */
    private function isValidLapTime(DateInterval $interval)
    {
        return $this->convertDateIntervalToSeconds($interval) >= self::$minLapTime;
    }
}
