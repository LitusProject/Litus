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

namespace CudiBundle\Component\WebSocket\Sale2;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Component\WebSocket\User,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    CudiBundle\Entity\Sales\Session,
    CudiBundle\Entity\Sales\QueueItem as EntityQueueItem,
    Doctrine\ORM\EntityManager;

/**
 * Queue Object
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
     * @var array Array with active queue items (selling or collecting)
     */
    private $_queueItems;

    /**
     * @param Doctrine\ORM\EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->_entityManager = $entityManager;
        $this->_queueItems = array();
    }

    /**
     * @param \CudiBundle\Entity\Sales\Session $session The sale session
     *
     * @return string
     */
    public function getJsonQueue(Session $session)
    {
        $repository = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem');

        return json_encode(
            (object) array(
                'queue' => array(
                    'selling' => $this->_createJsonQueue(
                        $repository->findAllByStatus($session, 'selling')
                    ),
                    'collected' => $this->_createJsonQueue(
                        $repository->findAllByStatus($session, 'collected')
                    ),
                    'collecting' => $this->_createJsonQueue(
                        $repository->findAllByStatus($session, 'collecting')
                    ),
                    'signed_in' => $this->_createJsonQueue(
                        $repository->findAllByStatus($session, 'signed_in')
                    ),
                )
            )
        );
    }

    /**
     * @param \CudiBundle\Entity\Sales\Session $session The sale session
     *
     * @return string
     */
    public function getJsonQueueList(Session $session)
    {
        return json_encode(
            (object) array(
                'queue' => $this->_createJsonQueue(
                    $this->_entityManager
                        ->getRepository('CudiBundle\Entity\Sales\QueueItem')
                        ->findAllBySession($session)
                )
            )
        );
    }

    /**
     * @param \CudiBundle\Entity\Sales\Session $session The sale session
     * @param string $universityIdentification
     *
     * @return string
     */
    public function addPerson(Session $session, $universityIdentification)
    {
        $person = $this->_entityManager
            ->getRepository('CommonBundle\Entity\Users\People\Academic')
            ->findOneByUsername($universityIdentification);

        if (null == $person) {
            return json_encode(
                (object) array(
                    'error' => 'person',
                )
            );
        }

        $bookings = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\Booking')
            ->findAllAssignedByPerson($person);

        $registration = $this->_entityManager
            ->getRepository('SecretaryBundle\Entity\Registration')
            ->findOneByAcademicAndAcademicYear($person, $this->_getCurrentAcademicYear());

        $metaData = $this->_entityManager
            ->getRepository('SecretaryBundle\Entity\Organization\MetaData')
            ->findOneByAcademicAndAcademicYear($person, $this->_getCurrentAcademicYear());

        if (empty($bookings) && !(null !== $registration && !$registration->hasPayed() && $metaData->becomeMember())) {
            return json_encode(
                (object) array(
                    'error' => 'noBookings',
                )
            );
        }

        $queueItem = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneByPersonNotSold($session, $person);

        if (null == $queueItem) {
            $queueItem = new EntityQueueItem($this->_entityManager, $person, $session);

            $this->_entityManager->persist($queueItem);
            $this->_entityManager->flush();
        }

        return json_encode(
            (object) array(
                'queueNumber' => $queueItem->getQueueNumber(),
            )
        );
    }

    /**
     * @param \CommonBundle\Component\WebSocket\User $user
     */
    public function unlockByUser(User $user)
    {
        foreach($this->_queueItems as $item) {
            if ($item->getUser() == $user)
                unset($this->_queueItems[$item->getId()]);
        }
    }

    /**
     * @param \CommonBundle\Component\WebSocket\User $user
     * @param integer $id
     */
    public function startCollecting(User $user, $id)
    {
        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneById($id);

        $item->setStatus('collecting');
        $this->_entityManager->flush();

        $enableCollectScanning = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.enable_collect_scanning');

        //if ($enableCollectScanning !== '1')
        //    return;

        $this->_queueItems[$id] = new QueueItem($this->_entityManager, $user, $id);

        return $this->_queueItems[$id]->getCollectInfo();
    }

    /**
     * @param integer $id
     */
    public function stopCollecting($id)
    {
        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneById($id);

        $item->setStatus('collected');
        $this->_entityManager->flush();
    }

    /**
     * @param integer $id
     */
    public function cancelCollecting($id)
    {
        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneById($id);

        $item->setStatus('signed_in');
        $this->_entityManager->flush();
    }

    /**
     * @param integer $id
     */
    public function startSelling(User $user, $id)
    {
        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneById($id);

        $item->setStatus('selling');
        $this->_entityManager->flush();

        $this->_queueItems[$id] = new QueueItem($this->_entityManager, $user, $id);

        // TODO: return sale info
    }

    /**
     * @param integer $id
     */
    public function cancelSelling($id)
    {
        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneById($id);

        $item->setStatus('collected');
        $this->_entityManager->flush();
    }

    /**
     * @param integer $id
     */
    public function setHold($id)
    {
        if (isset($this->_queueItems[$id]))
            unset($this->_queueItems[$id]);

        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneById($id);

        $item->setStatus('hold');
        $this->_entityManager->flush();
    }

    /**
     * @param integer $id
     */
    public function setUnhold($id)
    {
        if (isset($this->_queueItems[$id]))
            unset($this->_queueItems[$id]);

        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneById($id);

        $item->setStatus('signed_in');
        $this->_entityManager->flush();
    }

    /**
     * Return an array with the queue items in object
     *
     * @param array $items
     *
     * @return array
     */
    private function _createJsonQueue($items)
    {
        $results = array();
        foreach($items as $item) {
            $result = (object) array();
            $result->id = $item->getId();
            $result->number = $item->getQueueNumber();
            $result->name = $item->getPerson() ? $item->getPerson()->getFullName() : '';
            $result->university_identification = $item->getPerson()->getUniversityIdentification();
            $result->status = $item->getStatus();
            $result->locked = isset($this->_queueItems[$item->getId()]) ? $this->_queueItems[$item->getId()]->isLocked() : false;

            if ($item->getPayDesk()) {
                $result->payDesk = $item->getPayDesk()->getName();
                $result->payDeskId = $item->getPayDesk()->getId();
            }
            $results[] = $result;
        }
        return $results;
    }

    /**
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    private function _getCurrentAcademicYear()
    {
        $startAcademicYear = AcademicYear::getStartOfAcademicYear();
        $startAcademicYear->setTime(0, 0);

        $academicYear = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByUniversityStart($startAcademicYear);

        if (null === $academicYear) {
            $organizationStart = str_replace(
                '{{ year }}',
                $startAcademicYear->format('Y'),
                $this->_entityManager
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('start_organization_year')
            );
            $organizationStart = new DateTime($organizationStart);
            $academicYear = new AcademicYearEntity($organizationStart, $startAcademicYear);
            $this->_entityManager->persist($academicYear);
            $this->_entityManager->flush();
        }

        return $academicYear;
    }
}
