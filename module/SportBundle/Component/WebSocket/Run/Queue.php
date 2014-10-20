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
    private $_entityManager;

    /**
     * @var int Minimum runtime required (in seconds)
     */
    private static $MIN_LAP_TIME = 60;

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

        $this->_entityManager = $entityManager;
    }

    /**
     * Do action when a new user has connected to this socket
     *
     * @param User $user
     */
    protected function onConnect(User $user)
    {
        $this->sendQueue($user, $this->_getJsonQueue());
    }

    /**
     * Parse received text
     *
     * @param User   $user
     * @param string $data
     */
    protected function gotText(User $user, $data)
    {
        $this->_entityManager->clear();

        $key = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('sport.queue_socket_key');

        $command = json_decode($data);

        if (null == $command) {
            return;
        }

        switch ($command->command) {
            case 'action':
                if ($this->isAuthenticated($user->getSocket())) {
                    $this->_gotAction($user, $command);
                }
                break;
            case 'initialize':
                if (!isset($command->key) || $command->key != $key) {
                    $this->removeUser($user);
                    $now = new DateTime();
                    echo '[' . $now->format('Y-m-d H:i:s') . '] WebSocket connection with invalid key.' . PHP_EOL;

                    return;
                }

                $authSession = $this->_entityManager
                    ->getRepository('CommonBundle\Entity\User\Session')
                    ->findOneById($command->authSession);

                $allowed = false;

                $this->addAuthenticated($user->getSocket());

                $this->sendQueue($user, $this->_getJsonQueue());
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
     * @param User   $user
     * @param string $command
     */
    private function _gotAction(User $user, $command)
    {
        switch ($command->action) {
            case 'reloadQueue':
                $this->sendQueueToAll();
                break;
            case 'addToQueue':
                $this->_addToQueue($command);
                break;
            case 'deleteLap':
                $this->_deleteLap($command);
                break;
            case 'startLap':
                $this->_startLap();
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
        $queue = $this->_getJsonQueue();
        foreach ($this->getUsers() as $user) {
            $this->sendQueue($user, $queue);
        }
    }

    /**
     * @param string $data
     */
    private function _addToQueue($data)
    {
        if ('' != $data->universityIdentification
            && '' == $data->firstName
            && '' == $data->lastName
        ) {
            $academic = $this->_entityManager
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneByUniversityIdentification($data->universityIdentification);
            if (null !== $academic) {
                $data->firstName = $academic->getFirstName();
                $data->lastName = $academic->getLastName();
            }
        }

        $runner = $this->_entityManager
            ->getRepository('SportBundle\Entity\Runner')
            ->findOneByUniversityIdentification($data->universityIdentification);

        if (null === $runner) {
            $runner = $this->_entityManager
                ->getRepository('SportBundle\Entity\Runner')
                ->findOneByRunnerIdentification($data->universityIdentification);
        }

        $department = $this->_entityManager
            ->getRepository('SportBundle\Entity\Department')
            ->findOneById($data->department);

        if (null === $runner) {
            $academic = $this->_entityManager
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
        } else {
            if (null === $runner->getDepartment()) {
                $runner->setDepartment($department);
            }
        }

        $lap = new Lap($this->_getAcademicYear(), $runner);
        $this->_entityManager->persist($lap);

        $this->_entityManager->flush();
    }

    /**
     * @return string
     */
    private function _getJsonQueue()
    {
        $nbLaps = $this->_entityManager
            ->getRepository('SportBundle\Entity\Lap')
            ->countAll($this->_getAcademicYear());

        $uniqueRunners = $this->_entityManager
            ->getRepository('SportBundle\Entity\Lap')
            ->countRunners($this->_getAcademicYear());

        $laps = array();
        $previousLaps = array_reverse(
            $this->_entityManager
                ->getRepository('SportBundle\Entity\Lap')
                ->findPrevious($this->_getAcademicYear(), 5)
        );
        foreach ($previousLaps as $lap) {
            $laps[] = $this->_jsonLap($lap, 'previous');
        }

        $laps[] = $this->_jsonLap($this->_getCurrentLap(), 'current');

        $nextLaps = $this->_entityManager
            ->getRepository('SportBundle\Entity\Lap')
            ->findNext($this->_getAcademicYear(), 15);
        foreach ($nextLaps as $lap) {
            $laps[] = $this->_jsonLap($lap, 'next');
        }

        $fastestLap = $this->_getFastestLap();

        $data = (object) array(
            'laps' => (object) array(
                'number' => (object) array(
                    'own' => $nbLaps,
                    'uniqueRunners' => $uniqueRunners,
                ),
                'fastestRunner' => $fastestLap['runner'],
                'fastestTime' => $fastestLap['time'],
                'laps' => $laps,
                'officialResults' => $this->_getOfficialResults(),
                'averageLapTime' => $this->_getAverageLapTime(),
                'groupsOfFriends' => $this->_getGroupsOfFriends(),
                'mostLaps' => $this->_getMostFrequentRunners(),
            ),
        );

        return json_encode($data);
    }

    /**
     * @param  Lap|null    $lap
     * @param  string      $state
     * @return object|null
     */
    private function _jsonLap(Lap $lap = null, $state)
    {
        if (null === $lap) {
            return null;
        }

        $lap->setEntityManager($this->_entityManager);

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
    private function _deleteLap($data)
    {
        $lap = $this->_entityManager
            ->getRepository('SportBundle\Entity\Lap')
            ->findOneById($data->id);

        $this->_entityManager->remove($lap);
        $this->_entityManager->flush();
    }

    private function _startLap()
    {
        if (null !== $this->_getCurrentLap()) {
            $this->_getCurrentLap()->stop();
        }

        if (null !== $this->_getNextLap()) {
            $this->_getNextLap()->start();
        }

        $this->_entityManager->flush();
    }

    /**
     * @return null|Lap
     */
    private function _getCurrentLap()
    {
        return $this->_entityManager
            ->getRepository('SportBundle\Entity\Lap')
            ->findCurrent($this->_getAcademicYear());
    }

    /**
     * @return null|Lap
     */
    private function _getNextLap()
    {
        return $this->_entityManager
            ->getRepository('SportBundle\Entity\Lap')
            ->findNext($this->_getAcademicYear());
    }

    private function _getFastestLap()
    {
        $previousLaps = array_reverse(
            $this->_entityManager
                ->getRepository('SportBundle\Entity\Lap')
                ->findAllPreviousLaps($this->_getAcademicYear())
        );

        $time = null;
        $fastestLap = null;

        foreach ($previousLaps as $lap) {
            if ($this->_isValidLapTime($lap->getLapTime())) {
                if ($fastestLap == null) {
                    $time = $lap->getLapTime();
                    $fastestLap = $lap;
                } elseif ($this->_convertDateIntervalToSeconds($lap->getLapTime()) < $this->_convertDateIntervalToSeconds($time)) {
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

    private function _getMostFrequentRunners($number = 3)
    {
        $runners = $this->_entityManager
                ->getRepository('SportBundle\Entity\Lap')
                ->getRunnersAndCount($this->_getAcademicYear());

        $nbResults = 0;
        $index = 0;
        $mostLaps = array();
        while (isset($runners[$index]) && $nbResults < $number) {
            $runner = $this->_entityManager
                ->getRepository('SportBundle\Entity\Runner')
                ->findOneById($runners[$index]['runner']);
            if (strpos(strtolower($runner->getAcademic()->getFullName()),'vtk gent') === false) {
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

    private function _getAcademicYear()
    {
        return AcademicYear::getUniversityYear($this->_entityManager);
    }

    private function _getOfficialResults()
    {
        $fileContents = @file_get_contents('data/cache/run-' . md5('run_result_page'));

        $resultPage = null;
        if (false !== $fileContents) {
            $resultPage = (array) json_decode($fileContents);
        }

        if ($resultPage) {
            $teamId = $this->_entityManager
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
                $behind = 0;
                if (null !== $currentPlace && $currentPlace > 0) {
                    $firstData = $resultPage['teams'][0];
                    $behind = round(($firstData->laps + $firstData->position) - ($teamData->laps + $teamData->position), 2);
                }

                $lapsPerSecond = 1/($resultPage['lap']/($teamData->speed/3.6));

                return array(
                    'lapLength' => $resultPage['lap'],
                    'nbLaps' => $teamData->laps,
                    'position' => round($teamData->position * 100),
                    'speed' => round($teamData->speed, 2),
                    'lapsPerSecond' => round($lapsPerSecond, 4),
                    'behind' => $behind,
                );
            }
        }

        return null;
    }

    private function _getGroupsOfFriends($number = 5)
    {
        $groups = $this->_entityManager
            ->getRepository('SportBundle\Entity\Group')
            ->findAllByAcademicYear($this->_getAcademicYear());

        $returnArray = array();
        $sort = array();
        foreach ($groups as $group) {
            $group->setEntityManager($this->_entityManager);

            $array = (object) array(
                'name' => $group->getName(),
                'points' => $group->getPoints($this->_getAcademicYear()),
            );

            $returnArray[] = $array;
            $sort[] = $array->points;
        }

        array_multisort($sort, $returnArray);
        $returnArray = array_reverse($returnArray);
        $returnArray = array_splice($returnArray, 0, $number);

        return $returnArray;
    }

    private function _getAverageLapTime()
    {
        $laps = $this->_entityManager
            ->getRepository('SportBundle\Entity\Lap')
            ->findAll();

        $total = 0;
        foreach ($laps as $lap) {
            $total += $this->_convertDateIntervalToSeconds($lap->getLapTime());
        }
        $average = $total / count($laps);

        return floor($average / 60) . ':' . ($average % 60 < 10 ? '0' . $average % 60 : $average % 60);
    }

    private function _convertDateIntervalToSeconds(DateInterval $interval)
    {
        return $interval->h*3600 + $interval->i*60 + $interval->s;
    }

    private function _isValidLapTime(DateInterval $interval)
    {
        if ($this->_convertDateIntervalToSeconds($interval) < self::$MIN_LAP_TIME) {
            return false;
        }

        return true;
    }
}
