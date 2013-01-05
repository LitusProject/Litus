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

namespace CudiBundle\Component\WebSocket\Sale;

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
     * @return integer
     */
    public function getNumberSignedIn(Session $session)
    {
        return count(
            $this->_entityManager
                ->getRepository('CudiBundle\Entity\Sales\QueueItem')
                ->findAllByStatus($session, 'signed_in')
        );
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

        if (empty($bookings)) {
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
        } elseif ($queueItem->getStatus() == 'hold') {
            $queueItem->setStatus('signed_in');
            $this->_entityManager->flush();
        }

        return $queueItem;
    }

    /**
     * @param \CommonBundle\Component\WebSocket\User $user
     */
    public function unlockByUser(User $user)
    {
        foreach($this->_queueItems as $item) {
            if ($item->getUser()->getSocket() == $user->getSocket()) {
                $item = $this->_entityManager
                    ->getRepository('CudiBundle\Entity\Sales\QueueItem')
                    ->findOneById($item->getId());

                if ($item->getStatus() == 'collecting') {
                    $item->setStatus('signed_in');
                } elseif ($item->getStatus() == 'selling') {
                    $item->setStatus('collected');
                }
                $this->_entityManager->flush();
            }
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

        if ($enableCollectScanning !== '1')
            return;

        $this->_queueItems[$id] = new QueueItem($this->_entityManager, $user, $id);

        return $this->_queueItems[$id]->getCollectInfo();
    }

    /**
     * @param integer $id
     */
    public function stopCollecting($id, $articles = null)
    {
        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneById($id);

        $item->setStatus('collected');
        $this->_entityManager->flush();

        $enableCollectScanning = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.enable_collect_scanning');

        if ($enableCollectScanning !== '1' || !isset($this->_queueItems[$id]) || null == $articles)
            return;

        $this->_queueItems[$id]->setCollectedArticles($articles);
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

        if (!isset($this->_queueItems[$id]))
            $this->_queueItems[$id] = new QueueItem($this->_entityManager, $user, $id);
        else
            $this->_queueItems[$id]->setUser($user);

        return $this->_queueItems[$id]->getSaleInfo();
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
     * @param array $articles
     * @param array $discounts
     * @param string $payMethod
     * @return array
     */
    public function concludeSelling($id, $articles, $discounts, $payMethod)
    {
        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneById($id);

        $saleItems = $this->_queueItems[$id]->conclude($articles, $discounts);

        if (isset($this->_queueItems[$id]))
            unset($this->_queueItems[$id]);

        $item->setStatus('sold')
            ->setPayMethod($payMethod);

        $this->_entityManager->flush();

        return $saleItems;
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
     * @param integer $id
     * @param integer $barcode
     */
    public function addArticle($id, $barcode)
    {
        if (!isset($this->_queueItems[$id])) {
            return json_encode(
                array(
                    'addArticle' => array(
                        'error' => 'no_queue_item',
                    ),
                )
            );
        }

        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneById($id);

        $article = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\Article')
            ->findOneByBarcode($barcode);

        if (!isset($article)) {
            return json_encode(
                array(
                    'addArticle' => array(
                        'error' => 'no_article',
                    ),
                )
            );
        }

        $barcodes = array();
        foreach($article->getBarcodes() as $barcode)
            $barcodes[] = $barcode->getBarcode();

        $result = array(
            'id' => 0,
            'articleId' => $article->getId(),
            'price' => $article->getSellPrice(),
            'title' => $article->getMainArticle()->getTitle(),
            'barcode' => $article->getBarcode(),
            'barcodes' => $barcodes,
            'author' => $article->getMainArticle()->getAuthors(),
            'number' => 1,
            'status' => 'assigned',
            'collected' => 0,
            'discounts' => array(),
        );

        foreach($article->getDiscounts() as $discount) {
            if (!$discount->alreadyApplied($article, $item->getPerson(), $this->_entityManager))
                $result['discounts'][] = array('type' => $discount->getRawType(), 'value' => $discount->apply($article->getSellPrice()));
        }

        return json_encode(
            array(
                'addArticle' => $result,
            )
        );
    }

    /**
     * @param integer $id
     */
    public function undoSelling($id)
    {
        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\QueueItem')
            ->findOneById($id);

        $item->setPayMethod(null)
            ->setStatus('collected');

        $saleItems = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sales\SaleItem')
            ->findByQueueItem($item);

        $articles = array();
        foreach($saleItems as $saleItem) {
            if (!isset($articles[$saleItem->getArticle()->getId()])) {
                $articles[$saleItem->getArticle()->getId()] = array(
                    'article' => $saleItem->getArticle(),
                    'number' => $saleItem->getNumber(),
                );
            } else {
                $articles[$saleItem->getArticle()->getId()]['number'] += $saleItem->getNumber();
            }
        }

        foreach($articles as $article) {
            while($article['number'] > 0) {
                $booking = $this->_entityManager
                    ->getRepository('CudiBundle\Entity\Sales\Booking')
                    ->findOneSoldByPersonAndArticle($item->getPerson(), $article['article']);

                if (!isset($booking))
                    break;

                if ($booking->getNumber() > $article['number']) {
                    $remainder = new Booking(
                        $this->_entityManager,
                        $booking->getPerson(),
                        $booking->getArticle(),
                        'assigned',
                        $article['number']
                    );
                    $this->_entityManager->persist($remainder);
                    $booking->setNumber($booking->getNumber() - $article['number']);
                } else {
                    $booking->setStatus('assigned', $this->_entityManager);
                    $article['number'] -= $booking->getNumber();
                }
            }

            $saleItem->getArticle()->setStockValue($saleItem->getArticle()->getStockValue() + $saleItem->getNumber());
        }

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
        $prefix = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.queue_item_barcode_prefix');

        $results = array();
        foreach($items as $item) {
            $result = (object) array();
            $result->id = $item->getId();
            $result->barcode = $prefix + $item->getId();
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
