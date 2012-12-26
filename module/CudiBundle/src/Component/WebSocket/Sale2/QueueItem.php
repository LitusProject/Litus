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
    Doctrine\ORM\EntityManager;

/**
 * QueueItem Object
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class QueueItem extends \CommonBundle\Component\WebSocket\Server
{
    /**
     * @var \CudiBundle\Entity\Sales\Session The sale session
     */
    private $_id;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $_entityManager;

    /**
     * @var \CommonBundle\Component\WebSocket\User
     */
    private $_user;

    /**
     * @var array
     */
    private $_articles;

    /**
     * @param Doctrine\ORM\EntityManager $entityManager
     * @param integer $id The id of the queue item
     */
    public function __construct(EntityManager $entityManager, User $user, $id)
    {
        $this->_entityManager = $entityManager;
        $this->_id = $id;
        $this->_user = $user;
        $this->_articles = array();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @return \CommonBundle\Component\WebSocket\User
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * @return boolean
     */
    public function isLocked()
    {
        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneById($this->_id);

        return ($item->getStatus() == 'collecting' || $item->getStatus() == 'selling');
    }

    /**
     * @return string
     */
    public function getCollectInfo()
    {
        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneById($this->_id);

        return json_encode(
            array(
                'collect' => array(
                    'id' => $item->getId(),
                    'comment' => $item->getComment(),
                    'person' => array(
                        'id' => $item->getPerson()->getId(),
                        'name' => $item->getPerson()->getFullName(),
                        'universityIdentification' => $item->getPerson()->getUniversityIdentification(),
                        'member' => $item->getPerson()->isMember($this->_getCurrentAcademicYear()),
                    ),
                    'articles' => $this->_getArticles(),
                )
            )
        );
    }

    /**
     * @return string
     */
    public function getSaleInfo()
    {
        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneById($this->_id);

        return json_encode(
            array(
                'collect' => array(
                    'id' => $item->getId(),
                    'comment' => $item->getComment(),
                    'person' => array(
                        'id' => $item->getPerson()->getId(),
                        'name' => $item->getPerson()->getFullName(),
                        'universityIdentification' => $item->getPerson()->getUniversityIdentification(),
                        'member' => $item->getPerson()->isMember($this->_getCurrentAcademicYear()),
                    ),
                    'articles' => $this->_getArticles(),
                )
            )
        );
    }

    private function _getArticles()
    {
        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneById($this->_id);

        $bookings = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\Booking')
            ->findAllOpenByPerson($item->getPerson());

        $results = array();
        foreach($bookings as $booking) {
            $barcodes = array($booking->getArticle()->getBarcode());
            foreach($booking->getArticle()->getAdditionalBarcodes() as $barcode)
                $barcodes[] = $barcode->getBarcode();

            $result = array(
                'id' => $booking->getId(),
                'price' => $booking->getArticle()->getSellPrice(),
                'title' => $booking->getArticle()->getMainArticle()->getTitle(),
                'barcode' => $booking->getArticle()->getBarcode(),
                'barcodes' => $barcodes,
                'author' => $booking->getArticle()->getMainArticle()->getAuthors(),
                'number' => $booking->getNumber(),
                'status' => $booking->getStatus(),
                'collected' => 0,
                'discounts' => array(),
            );

            foreach($booking->getArticle()->getDiscounts() as $discount)
                $result->discounts[$discount->getType()] = $discount->apply($booking->getArticle()->getSellPrice());
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
