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

namespace SportBundle\Component\WebSocket\Run;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Component\WebSocket\User,
    CommonBundle\Entity\Users\Person,
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

        if (strpos($data, 'action: ') === 0) {
            $this->_gotAction($user, $data);
        } elseif ($data == 'reloadQueue') {
            $this->sendQueueToAll();
        }
    }

    /**
     * Parse action text
     *
     * @param \CommonBundle\Component\WebSockets\Sale\User $user
     * @param string $data
     */
    private function _gotAction(User $user, $data)
    {
        $data .= ' ';
        $action = substr($data, strlen('action: '), strpos($data, ' ', strlen('action: ')) - strlen('action: '));
        $params = trim(substr($data, strpos($data, ' ', strlen('action: ')) + 1));

        switch ($action) {
            case 'addToQueue':
                $this->_addToQueue(json_decode($params));
                break;
            case 'deleteLap':
                $this->_deleteLap($params);
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
        foreach($this->getUsers() as $user)
            $this->sendQueue($user, $this->_getJsonQueue());
    }

    private function _addToQueue($data)
    {
        if ('' != $data->universityIdentification
            && '' == $data->firstName
            && '' == $data->lastName
        ) {
            $academic = $this->_entityManager
                ->getRepository('CommonBundle\Entity\Users\People\Academic')
                ->findOneByUniversityIdentification($data->universityIdentification);

            $data->firstName = $academic->getFirstName();
            $data->lastName = $academic->getLastName();
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
                ->getRepository('CommonBundle\Entity\Users\People\Academic')
                ->findOneByUniversityIdentification($data->universityIdentification);

            $runner = new Runner(
                $this->_getAcademicYear(),
                $data->firstName,
                $data->lastName,
                null,
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
        $previousLaps = $this->_entityManager
            ->getRepository('SportBundle\Entity\Lap')
            ->findPrevious($this->_getAcademicYear(), 5);
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

        return (object) array(
            'id' => $lap->getId(),
            'fullName' => $lap->getRunner()->getFullName(),
            'firstName' => $lap->getRunner()->getFirstName(),
            'lastName' => $lap->getRunner()->getLastName(),
            'registrationTime' => $lap->getRegistrationTime()->format('d/m/Y H:i:s'),
            'lapTime' => $lap->getStartTime() ? $lap->getLapTime()->format('%i:%S') : '',
            'state' => $state,
        );
    }

    private function _deleteLap($data)
    {
        $lap = $this->_entityManager
            ->getRepository('SportBundle\Entity\Lap')
            ->findOneById($data);

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
        $url = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('sport.run_result_page');
            echo 'official';
        $opts = array('http' =>
            array(
                'timeout' => 0.5,
            )
        );
        $fileContents = @file_get_contents($url, false, stream_context_create($opts));

        $resultPage = null;
        if (false !== $fileContents)
            $resultPage = simplexml_load_string($fileContents);

        $nbOfficialLaps = null;
        if (null !== $resultPage) {
            echo 'done' . PHP_EOL;
            $teamId = $this->_entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('sport.run_team_id');

            $teamData = $resultPage->xpath('//team[@id=\'' . $teamId . '\']');

            return array(
                'nbLaps' => $teamData[0]->rounds->__toString(),
                'position' => round($teamData[0]->position->__toString() * 100),
                'speed' => $teamData[0]->speed_kmh->__toString(),
                'behind' => $teamData[0]->behind->__toString()
            );
        }
echo 'timeout' . PHP_EOL;
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
            $array = (object) array(
                'name' => $group->getName(),
                'points' => 0,
            );

            $happyHours = $group->getHappyHours();

            foreach ($group->getMembers() as $member) {
                foreach ($member->getLaps($this->_entityManager, $this->_getAcademicYear()) as $lap) {
                    if (null === $lap->getEndTime())
                        continue;

                    $startTime = $lap->getStartTime()->format('H');
                    $endTime = $lap->getEndTime()->format('H');

                    $array->points += 1;

                    for ($i = 0; isset($happyHours[$i]); $i++) {
                        if ($startTime >= substr($happyHours[$i], 0, 2) && $endTime <= substr($happyHours[$i], 2)) {
                            if ($lap->getLapTime() <= new DateInterval('PT90S'))
                                $array->points += 1;
                        }
                    }
                }
            }

            $returnArray[] = $array;
            $sort[] = $array->points;
        }

        array_multisort($sort, $returnArray);
        $returnArray = array_reverse($returnArray);
        $returnArray = array_splice($returnArray, 0, $number);

        return $returnArray;
    }
}
