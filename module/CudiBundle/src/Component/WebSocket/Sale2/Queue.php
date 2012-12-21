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
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    CudiBundle\Entity\Sales\Session,
    CudiBundle\Entity\Sales\QueueItem,
    Doctrine\ORM\EntityManager;

/**
 * Queue Object
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Queue extends \CommonBundle\Component\WebSocket\Server
{
    /**
     * @var \CudiBundle\Entity\Sales\Session The sale session
     */
    private $_session;

    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $_entityManager;

    /**
     * @param \CudiBundle\Entity\Sales\Session $session The sale session
     * @param Doctrine\ORM\EntityManager $entityManager
     */
    public function __construct(Session $session, EntityManager $entityManager)
    {
        $this->_session = $session;
        $this->_entityManager = $entityManager;
    }

    /**
     * @return string
     */
    public function getJsonQueue()
    {
        $repository = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem');

        return json_encode(
            (object) array(
                'queue' => array(
                    'selling' => $this->_createJsonQueue(
                        $repository->findAllByStatus($this->_session, 'selling')
                    ),
                    'collected' => $this->_createJsonQueue(
                        $repository->findAllByStatus($this->_session, 'collected')
                    ),
                    'collecting' => $this->_createJsonQueue(
                        $repository->findAllByStatus($this->_session, 'collecting')
                    ),
                    'signed_in' => $this->_createJsonQueue(
                        $repository->findAllByStatus($this->_session, 'signed_in')
                    ),
                )
            )
        );
    }

    /**
     * @return string
     */
    public function getJsonQueueList()
    {
        return json_encode(
            (object) array(
                'queue' => $this->_createJsonQueue(
                    $this->_entityManager
                        ->getRepository('CudiBundle\Entity\Sales\QueueItem')
                        ->findAllBySession($this->_session)
                )
            )
        );
    }

    /**
     * @param string $universityIdentification
     *
     * @return string
     */
    public function addPerson($universityIdentification)
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
            ->findOneByPersonNotSold($this->_session, $person);

        if (null == $queueItem) {
            $queueItem = new QueueItem($this->_entityManager, $person, $this->_session);

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
            //$result->locked = isset($this->_lockedItems[$item->getId()]);

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