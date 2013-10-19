<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace SportBundle\Component\WebSocket\Run;

use CommonBundle\Component\Acl\Acl,
    CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Component\WebSocket\User,
    CommonBundle\Entity\User\Person,
    DateTime,
    DateInterval,
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
     * @var Doctrine\ORM\EntityManager
     */
    private $_entityManager;

    /**
     * @param Doctrine\ORM\EntityManager $entityManager
     * @param string $address The url for the websocket master socket
     * @param integer $port The port to listen on
     */
    public function __construct(EntityManager $entityManager)
    {
        $address = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('sport.queue_socket_host');
        $port = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('sport.queue_socket_port');

        parent::__construct($address, $port);

        $this->_entityManager = $entityManager;
    }

    /**
     * Do action when a new user has connected to this socket
     *
     * @param \CommonBundle\Component\WebSocket\User $user
     */
    protected function onConnect(User $user)
    {
        $this->sendQueue($user, $this->_getJsonQueue());
    }

    /**
     * Parse received text
     *
     * @param \CommonBundle\Component\WebSockets\Sale\User $user
     * @param string $data
     */
    protected function gotText(User $user, $data)
    {
        $this->_entityManager->clear();

        $key = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('sport.queue_socket_key');

        $command = json_decode($data);

        if (null == $command)
            return;

        switch($command->command) {
            case 'action':
                if ($this->isAuthenticated($user->getSocket()))
                    $this->_gotAction($user, $command);
                break;
            case 'initialize':
                if (!isset($command->key) || $command->key != $key) {
                    $this->removeUser($user);
                    $now = new DateTime();
                    echo '[' . $now->format('Y-m-d H:i:s') . '] WebSocket connection with invalid key.' . PHP_EOL;
                    return;
                }

                if (!isset($command->authSession)) {
                    $this->removeUser($user);
                    $now = new DateTime();
                    echo '[' . $now->format('Y-m-d H:i:s') . '] WebSocket connection with invalid auth session.' . PHP_EOL;
                    return;
                }

                $authSession = $this->_entityManager
                    ->getRepository('CommonBundle\Entity\User\Session')
                    ->findOneById($command->authSession);

                if ($authSession) {
                    $acl = new Acl($this->_entityManager);

                    $allowed = false;
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

                $this->addAuthenticated($user->getSocket());

                $this->sendQueue($user, $this->_getJsonQueue());
                break;
        }
    }

    /**
     * Parse action text
     *
     * @param \CommonBundle\Component\WebSockets\Sale\User $user
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
     * @param \CommonBundle\Component\WebSockets\Sale\User $user
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
        $queue= $this->_getJsonQueue();
        foreach($this->getUsers() as $user)
            $this->sendQueue($user, $queue);
    }

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

        if (null === $runner) {
            $academic = $this->_entityManager
                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                ->findOneByUniversityIdentification($data->universityIdentification);

            $department = $this->_entityManager
                ->getRepository('SportBundle\Entity\Department')
                ->findOneById($data->department);

            $runner = new Runner(
                $this->_getAcademicYear(),
                $data->firstName,
                $data->lastName,
                null,
                $department,
                $academic
            );

            $runner->setRunnerIdentification($data->universityIdentification);
        }

        $lap = new Lap($this->_getAcademicYear(), $runner);
        $this->_entityManager->persist($lap);

        $this->_entityManager->flush();
    }

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
        foreach($previousLaps as $lap)
            $laps[] = $this->_jsonLap($lap, 'previous');

        $laps[] = $this->_jsonLap($this->_getCurrentLap(), 'current');

        $nextLaps = $this->_entityManager
            ->getRepository('SportBundle\Entity\Lap')
            ->findNext($this->_getAcademicYear(), 15);
        foreach($nextLaps as $lap)
            $laps[] = $this->_jsonLap($lap, 'next');

        $data = (object) array(
            'laps' => (object) array(
                'number' => (object) array(
                    'own' => $nbLaps,
                    'uniqueRunners' => $uniqueRunners,
                ),
                'laps' => $laps,
                'officialResults' => $this->_getOfficialResults(),
                'groupsOfFriends' => $this->_getGroupsOfFriends(),
            ),
        );

        return json_encode($data);
    }

    private function _jsonLap(Lap $lap = null, $state)
    {
        if (null === $lap)
            return null;

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
        if (null !== $this->_getCurrentLap())
            $this->_getCurrentLap()->stop();

        if (null !== $this->_getNextLap())
            $this->_getNextLap()->start();

        $this->_entityManager->flush();
    }

    private function _getCurrentLap()
    {
        return $this->_entityManager
            ->getRepository('SportBundle\Entity\Lap')
            ->findCurrent($this->_getAcademicYear());
    }

    private function _getNextLap()
    {
        return $this->_entityManager
            ->getRepository('SportBundle\Entity\Lap')
            ->findNext($this->_getAcademicYear());
    }

    private function _getAcademicYear()
    {
        $startAcademicYear = AcademicYear::getStartOfAcademicYear();

        $start = new DateTime(
            str_replace(
                '{{ year }}',
                $startAcademicYear->format('Y'),
                $this->_entityManager
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('start_organization_year')
            )
        );

        $next = clone $start;
        $next->add(new DateInterval('P1Y'));
        if ($next <= new DateTime())
            $start = $next;

        $startAcademicYear->setTime(0, 0);

        $academicYear = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByStart($start);

        return $academicYear;
    }

    private function _getOfficialResults()
    {
        $fileContents = @file_get_contents('data/cache/' . md5('run_result_page'));

        $resultPage = null;
        if (false !== $fileContents)
            $resultPage = (array) json_decode($fileContents);

        $nbOfficialLaps = null;
        if (null !== $resultPage) {
            $teamId = $this->_entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('sport.run_team_id');

            $currentPlace = null;
            $teamData = null;
            foreach ($resultPage['teams'] as $place => $team) {
                if ($team[0] == $teamId) {
                    $currentPlace = $place;
                    $teamData = $team;
                }
            }

            if (null !== $teamData) {
                $behind = 0;
                if (null !== $currentPlace && $currentPlace > 0) {
                    $firstData = $resultPage['teams'][0];
                    $behind = round(($firstData[2] + $firstData[3]) - ($teamData[2] + $teamData[3]), 2);
                }

                $lapsPerSecond = 1/($resultPage['lap']/($teamData[4]/3.6));

                return array(
                    'lapLength' => $resultPage['lap'],

                    'nbLaps' => $teamData[2],
                    'position' => round($teamData[3] * 100),
                    'speed' => round($teamData[4], 2),
                    'lapsPerSecond' => round($lapsPerSecond, 4),

                    'behind' => $behind,
                );
            }
        }

        return;
    }

    private function _getGroupsOfFriends($number = 5)
    {
        $groups = $this->_entityManager
            ->getRepository('SportBundle\Entity\Group')
            ->findAll($this->_getAcademicYear());

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
}
