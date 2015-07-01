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

namespace CudiBundle\Component\WebSocket\Sale;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Component\WebSocket\User,
    CudiBundle\Entity\Sale\QueueItem as EntityQueueItem,
    CudiBundle\Entity\Sale\Session,
    Doctrine\ORM\EntityManager;

/**
 * Queue Object
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Queue
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var array Array with active queue items (selling or collecting)
     */
    private $queueItems;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->queueItems = array();
    }

    /**
     * @return integer
     */
    public function getNumberSignedIn(Session $session)
    {
        return count(
            $this->entityManager
                ->getRepository('CudiBundle\Entity\Sale\QueueItem')
                ->findAllByStatus($session, 'signed_in')
        );
    }

    /**
     * @param Session $session The sale session
     *
     * @return string
     */
    public function getJsonQueue(Session $session)
    {
        $repository = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem');

        $selling = $this->createJsonQueue(
            $repository->findAllByStatus($session, 'selling')
        );

        $collected = $this->createJsonQueue(
            $repository->findAllByStatus($session, 'collected')
        );

        $collecting = $this->createJsonQueue(
            $repository->findAllByStatus($session, 'collecting')
        );

        $signed_in = $this->createJsonQueue(
            $repository->findAllByStatus($session, 'signed_in')
        );

        $json = json_encode(
            (object) array(
                'queue' => array(
                    'selling' => $selling,
                    'collected' => $collected,
                    'collecting' => $collecting,
                    'signed_in' => $signed_in,
                ),
            )
        );

        return $json;
    }

    /**
     * @param  string      $id The queue item id
     * @return string|null
     */
    public function getJsonQueueItem($id)
    {
        if (null == $id) {
            return;
        }

        $item = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($id);

        $prefix = $this->entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.queue_item_barcode_prefix');

        $enableCollectScanning = $this->entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.enable_collect_scanning');

        $identification = $item->getPerson()->getUniversityIdentification() ? $item->getPerson()->getUniversityIdentification() : $item->getPerson()->getUserName();

        $result = (object) array();
        $result->id = $item->getId();
        $result->barcode = $prefix + $item->getId();
        $result->number = $item->getQueueNumber();
        $result->name = $item->getPerson() ? $item->getPerson()->getFullName() : '';
        $result->university_identification = $identification;
        $result->status = $item->getStatus();
        $result->locked = false;
        $result->collectPrinted = $item->getCollectPrinted();
        $result->displayScanButton = $item->getCollectPrinted() && $enableCollectScanning;

        if ($item->getPayDesk()) {
            $result->payDesk = $item->getPayDesk()->getName();
            $result->payDeskId = $item->getPayDesk()->getId();
        }

        return json_encode(
            (object) array(
                'item' => $result,
            )
        );
    }

    /**
     * @param  Session $session The sale session
     * @return string
     */
    public function getJsonQueueList(Session $session)
    {
        $numItems = $this->entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.number_queue_items');

        return json_encode(
            (object) array(
                'queue' => array_slice(
                    $this->createJsonQueue(
                        $this->entityManager
                            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
                            ->findAllBySession($session)
                    ),
                    0,
                    $numItems
                ),
            )
        );
    }

    /**
     * @param  Session $session                  The sale session
     * @param  string  $universityIdentification
     * @return string
     */
    public function addPerson(Session $session, $universityIdentification, $forced = false)
    {
        $person = $this->entityManager
            ->getRepository('CommonBundle\Entity\User\Person\Academic')
            ->findOneByUsername($universityIdentification);

        if (null == $person) {
            return json_encode(
                (object) array(
                    'error' => 'person',
                )
            );
        }

        if (!$forced) {
            if (!$session->canSignIn($this->entityManager, $person)) {
                return json_encode(
                    (object) array(
                        'error' => 'rejected',
                    )
                );
            }
        }

        $bookings = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findAllAssignedByPerson($person);

        if (empty($bookings) && !$forced) {
            return json_encode(
                (object) array(
                    'error' => 'noBookings',
                )
            );
        }

        $queueItem = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneByPersonNotSold($session, $person);

        if (null == $queueItem) {
            $queueItem = new EntityQueueItem($this->entityManager, $person, $session);

            $this->entityManager->persist($queueItem);
            $this->entityManager->flush();
        } elseif ($queueItem->getStatus() == 'hold') {
            $queueItem->setStatus('signed_in');
            $this->entityManager->flush();
        }

        return $queueItem;
    }

    /**
     * @param  User $user
     * @return null
     */
    public function unlockByUser(User $user)
    {
        foreach ($this->queueItems as $item) {
            if ($item->getUser()->getSocket() == $user->getSocket()) {
                $item = $this->entityManager
                    ->getRepository('CudiBundle\Entity\Sale\QueueItem')
                    ->findOneById($item->getId());

                if ($item->getStatus() == 'collecting') {
                    $item->setStatus('signed_in');
                } elseif ($item->getStatus() == 'selling') {
                    $item->setStatus('collected');
                }
                $this->entityManager->flush();
            }
        }
    }

    /**
     * @param  User        $user
     * @param  integer     $id
     * @param  boolean     $bulk
     * @return string|null
     */
    public function startCollecting(User $user, $id, $bulk = false)
    {
        $item = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($id);

        $item->setStatus('collecting');
        $item->setCollectPrinted(true);

        $this->entityManager->flush();

        $enableCollectScanning = $this->entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.enable_collect_scanning');

        $lightVersion = $this->entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.sale_light_version');

        if ($enableCollectScanning && !$lightVersion && !$bulk) {
            $this->queueItems[$id] = new QueueItem($this->entityManager, $user, $id);

            return $this->queueItems[$id]->getCollectInfo();
        }

        $this->entityManager->flush();
    }

    /**
     * @param  integer    $id
     * @param  array|null $articles
     * @return null
     */
    public function stopCollecting($id, $articles = null)
    {
        $item = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($id);

        $item->setStatus('collected');

        $this->entityManager->flush();

        $enableCollectScanning = $this->entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.enable_collect_scanning');

        if (!$enableCollectScanning || !isset($this->queueItems[$id]) || null == $articles) {
            return;
        }

        $this->queueItems[$id]->setCollectedArticles($articles);
    }

    /**
     * @param  integer $id
     * @return null
     */
    public function cancelCollecting($id)
    {
        $item = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($id);

        $item->setStatus('signed_in')
            ->setCollectPrinted(false);
        $this->entityManager->flush();
    }

    /**
     * @param  User    $user
     * @param  integer $id
     * @return string
     */
    public function startSale(User $user, $id)
    {
        $item = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($id);

        $item->setStatus('selling');
        $paydesk = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\PayDesk')
            ->findOneByCode($user->getExtraData('paydesk'));
        if (null !== $paydesk) {
            $item->setPayDesk($paydesk);
        }

        $this->entityManager->flush();

        if (!isset($this->queueItems[$id])) {
            $this->queueItems[$id] = new QueueItem($this->entityManager, $user, $id);
        } else {
            $this->queueItems[$id]->setUser($user);
        }

        return $this->queueItems[$id]->getSaleInfo();
    }

    /**
     * @param  int  $id
     * @return null
     */
    public function cancelSale($id)
    {
        $item = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($id);

        $item->setStatus('collected');
        $this->entityManager->flush();
    }

    /**
     * @param  integer $id
     * @param  array   $articles
     * @param  array   $discounts
     * @param  string  $payMethod
     * @return array
     */
    public function concludeSale($id, $articles, $discounts, $payMethod)
    {
        if (!isset($this->queueItems[$id])) {
            return;
        }

        $item = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($id);

        $saleItems = $this->queueItems[$id]->conclude($articles, $discounts);

        if (isset($this->queueItems[$id])) {
            unset($this->queueItems[$id]);
        }

        $item->setStatus('sold')
            ->setPayMethod($payMethod);

        $this->entityManager->flush();

        return $saleItems;
    }

    /**
     * @param  int  $id
     * @return null
     */
    public function setHold($id)
    {
        if (isset($this->queueItems[$id])) {
            unset($this->queueItems[$id]);
        }

        $item = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($id);

        $item->setStatus('hold');
        $this->entityManager->flush();
    }

    /**
     * @param  int  $id
     * @return null
     */
    public function setUnhold($id)
    {
        if (isset($this->queueItems[$id])) {
            unset($this->queueItems[$id]);
        }

        $item = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($id);

        $item->setStatus('signed_in');
        $this->entityManager->flush();
    }

    /**
     * @param  int    $id
     * @param  int    $articleId
     * @return string
     */
    public function addArticle($id, $articleId)
    {
        if (!isset($this->queueItems[$id])) {
            return json_encode(
                array(
                    'addArticle' => array(
                        'error' => 'no_queue_item',
                    ),
                )
            );
        }

        if (!is_numeric($articleId)) {
            return json_encode(
                array(
                    'addArticle' => array(
                        'error' => 'no_article',
                    ),
                )
            );
        }

        $item = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($id);

        $article = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\Article')
            ->findOneById($articleId);

        if (!isset($article)) {
            return json_encode(
                array(
                    'addArticle' => array(
                        'error' => 'no_article',
                    ),
                )
            );
        }

        $period = $this->entityManager
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();

        $period->setEntityManager($this->entityManager);

        if ($article->getStockValue() - $period->getNbAssigned($article) <= 0) {
            return json_encode(
                array(
                    'addArticle' => array(
                        'error' => 'occupied',
                    ),
                )
            );
        }

        $barcodes = array();
        foreach ($article->getBarcodes() as $barcode) {
            $barcodes[] = $barcode->getBarcode();
        }

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
            'sellable' => $article->isSellable(),
            'collected' => 0,
            'discounts' => array(),
        );

        foreach ($article->getDiscounts() as $discount) {
            if (!$discount->alreadyApplied($article, $item->getPerson(), $this->entityManager, $this->getCurrentAcademicYear()) &&
                    $discount->canBeApplied($item->getPerson(), $this->getCurrentAcademicYear(), $this->entityManager)) {
                $result['discounts'][] = array(
                    'type' => $discount->getRawType(),
                    'value' => $discount->apply($article->getSellPrice()),
                    'applyOnce' => $discount->applyOnce(),
                );
            }
        }

        return json_encode(
            array(
                'addArticle' => $result,
            )
        );
    }

    /**
     * @param  int  $id
     * @return null
     */
    public function undoSale($id)
    {
        $item = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($id);

        $item->setPayMethod(null)
            ->setStatus('collected');

        $saleItems = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\SaleItem')
            ->findByQueueItem($item);

        $articles = array();
        foreach ($saleItems as $saleItem) {
            if (!isset($articles[$saleItem->getArticle()->getId()])) {
                $articles[$saleItem->getArticle()->getId()] = array(
                    'article' => $saleItem->getArticle(),
                    'number' => $saleItem->getNumber(),
                );
            } else {
                $articles[$saleItem->getArticle()->getId()]['number'] += $saleItem->getNumber();
            }
            $this->entityManager->remove($saleItem);
        }

        foreach ($articles as $article) {
            $article['article']->setStockValue($article['article']->getStockValue() + $article['number']);

            while ($article['number'] > 0) {
                $booking = $this->entityManager
                    ->getRepository('CudiBundle\Entity\Sale\Booking')
                    ->findOneSoldByPersonAndArticle($item->getPerson(), $article['article']);

                if (!isset($booking)) {
                    break;
                }

                if ($booking->getNumber() > $article['number']) {
                    $remainder = new Booking(
                        $this->entityManager,
                        $booking->getPerson(),
                        $booking->getArticle(),
                        'assigned',
                        $article['number']
                    );
                    $this->entityManager->persist($remainder);
                    $booking->setNumber($booking->getNumber() - $article['number']);
                    $article['number'] = 0;
                } else {
                    $booking->setStatus('assigned', $this->entityManager);
                    $article['number'] -= $booking->getNumber();
                }
            }
        }

        $this->entityManager->flush();
    }

    /**
     * Return an array with the queue items in object
     *
     * @param  array $items
     * @return array
     */
    private function createJsonQueue($items)
    {
        $prefix = $this->entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.queue_item_barcode_prefix');

        $enableCollectScanning = $this->entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.enable_collect_scanning');

        $results = array();
        foreach ($items as $item) {
            $result = (object) array();
            $result->id = $item->getId();
            $result->barcode = $prefix + $item->getId();
            $result->number = $item->getQueueNumber();
            $result->name = $item->getPerson() ? $item->getPerson()->getFullName() : '';
            $result->university_identification = $item->getPerson()->getUniversityIdentification();
            $result->status = $item->getStatus();
            $result->locked = false;
            $result->collectPrinted = $item->getCollectPrinted();
            $result->displayScanButton = $item->getCollectPrinted() && $enableCollectScanning;

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
    private function getCurrentAcademicYear()
    {
        return AcademicYear::getUniversityYear($this->entityManager);
    }
}
