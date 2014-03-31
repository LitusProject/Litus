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
    CudiBundle\Entity\Sale\Session,
    CudiBundle\Entity\Sale\QueueItem as EntityQueueItem,
    Doctrine\ORM\EntityManager;

/**
 * Queue Object
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Queue
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
     * @param EntityManager $entityManager
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
                ->getRepository('CudiBundle\Entity\Sale\QueueItem')
                ->findAllByStatus($session, 'signed_in')
        );
    }

    /**
     * @param \CudiBundle\Entity\Sale\Session $session The sale session
     *
     * @return string
     */
    public function getJsonQueue(Session $session)
    {
        $repository = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem');

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
     * @param string $id The queue item id
     *
     * @return string
     */
    public function getJsonQueueItem($id)
    {
        if (null == $id)
            return;

        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($id);

        $prefix = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.queue_item_barcode_prefix');

        $identification = $item->getPerson()->getUniversityIdentification() ? $item->getPerson()->getUniversityIdentification() : $item->getPerson()->getUserName();

        $result = (object) array();
        $result->id = $item->getId();
        $result->barcode = $prefix + $item->getId();
        $result->number = $item->getQueueNumber();
        $result->name = $item->getPerson() ? $item->getPerson()->getFullName() : '';
        $result->university_identification = $identification;
        $result->status = $item->getStatus();
        $result->locked = /*isset($this->_queueItems[$item->getId()]) ? $this->_queueItems[$item->getId()]->isLocked() :*/ false;
        $result->collectPrinted = $item->getCollectPrinted();

        if ($item->getPayDesk()) {
            $result->payDesk = $item->getPayDesk()->getName();
            $result->payDeskId = $item->getPayDesk()->getId();
        }

        return json_encode(
            (object) array(
                'item' => $result
            )
        );
    }

    /**
     * @param \CudiBundle\Entity\Sale\Session $session The sale session
     *
     * @return string
     */
    public function getJsonQueueList(Session $session)
    {
        $numItems = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.number_queue_items');

        return json_encode(
            (object) array(
                'queue' => array_slice(
                    $this->_createJsonQueue(
                        $this->_entityManager
                            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
                            ->findAllBySession($session)
                    ),
                    0,
                    $numItems
                )
            )
        );
    }

    /**
     * @param \CudiBundle\Entity\Sale\Session $session                  The sale session
     * @param string                          $universityIdentification
     *
     * @return string
     */
    public function addPerson(Session $session, $universityIdentification, $forced = false)
    {
        $person = $this->_entityManager
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
            if (!$session->canSignIn($this->_entityManager, $person)) {
                return json_encode(
                    (object) array(
                        'error' => 'rejected',
                    )
                );
            }
        }

        $bookings = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findAllAssignedByPerson($person);

        if (empty($bookings) && !$forced) {
            return json_encode(
                (object) array(
                    'error' => 'noBookings',
                )
            );
        }

        $queueItem = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
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
        foreach ($this->_queueItems as $item) {
            if ($item->getUser()->getSocket() == $user->getSocket()) {
                $item = $this->_entityManager
                    ->getRepository('CudiBundle\Entity\Sale\QueueItem')
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
     * @param integer                                $id
     */
    public function startCollecting(User $user, $id, $bulk = false)
    {
        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($id);

        $item->setStatus('collecting');
        if ($bulk)
            $item->setCollectPrinted(true);

        $this->_entityManager->flush();

        $enableCollectScanning = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.enable_collect_scanning');

        $lightVersion = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.sale_light_version');

        if ($enableCollectScanning && !$lightVersion && !$bulk) {
            $this->_queueItems[$id] = new QueueItem($this->_entityManager, $user, $id);

            return $this->_queueItems[$id]->getCollectInfo();
        }

        $this->_entityManager->flush();
    }

    /**
     * @param integer $id
     */
    public function stopCollecting($id, $articles = null)
    {
        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($id);

        $item->setStatus('collected');

        $this->_entityManager->flush();

        $enableCollectScanning = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.enable_collect_scanning');

        if (!$enableCollectScanning || !isset($this->_queueItems[$id]) || null == $articles)
            return;

        $this->_queueItems[$id]->setCollectedArticles($articles);
    }

    /**
     * @param integer $id
     */
    public function cancelCollecting($id)
    {
        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($id);

        $item->setStatus('signed_in')
            ->setCollectPrinted(false);
        $this->_entityManager->flush();
    }

    /**
     * @param  integer $id
     * @return string
     */
    public function startSale(User $user, $id)
    {
        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($id);

        $item->setStatus('selling');
        $paydesk = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\PayDesk')
            ->findOneByCode($user->getExtraData('paydesk'));
        if (null !== $paydesk)
            $item->setPayDesk($paydesk);

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
    public function cancelSale($id)
    {
        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($id);

        $item->setStatus('collected');
        $this->_entityManager->flush();
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
        if (!isset($this->_queueItems[$id]))
            return;

        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
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
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
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
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($id);

        $item->setStatus('signed_in');
        $this->_entityManager->flush();
    }

    /**
     * @param integer $id
     */
    public function addArticle($id, $articleId)
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

        if (!is_numeric($articleId)) {
            return json_encode(
                array(
                    'addArticle' => array(
                        'error' => 'no_article',
                    ),
                )
            );
        }

        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($id);

        $article = $this->_entityManager
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

        $period = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Stock\Period')
            ->findOneActive();

        $period->setEntityManager($this->_entityManager);

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
            'sellable' => $article->isSellable(),
            'collected' => 0,
            'discounts' => array(),
        );

        foreach ($article->getDiscounts() as $discount) {
            if (!$discount->alreadyApplied($article, $item->getPerson(), $this->_entityManager) &&
                    $discount->canBeApplied($item->getPerson(), $this->_getCurrentAcademicYear(), $this->_entityManager)) {
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
     * @param integer $id
     */
    public function undoSale($id)
    {
        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($id);

        $item->setPayMethod(null)
            ->setStatus('collected');

        $saleItems = $this->_entityManager
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
            $this->_entityManager->remove($saleItem);
        }

        foreach ($articles as $article) {
            while ($article['number'] > 0) {
                $booking = $this->_entityManager
                    ->getRepository('CudiBundle\Entity\Sale\Booking')
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
        foreach ($items as $item) {
            $result = (object) array();
            $result->id = $item->getId();
            $result->barcode = $prefix + $item->getId();
            $result->number = $item->getQueueNumber();
            $result->name = $item->getPerson() ? $item->getPerson()->getFullName() : '';
            $result->university_identification = $item->getPerson()->getUniversityIdentification();
            $result->status = $item->getStatus();
            $result->locked = /*isset($this->_queueItems[$item->getId()]) ? $this->_queueItems[$item->getId()]->isLocked() : */false;
            $result->collectPrinted = $item->getCollectPrinted();

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
        return AcademicYear::getUniversityYear($this->_entityManager);
    }
}
