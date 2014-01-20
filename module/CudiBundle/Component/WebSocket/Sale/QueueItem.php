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
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    CommonBundle\Entity\User\Status\Organization as OrganizationStatus,
    CudiBundle\Entity\Sale\Booking,
    CudiBundle\Entity\Sale\SaleItem,
    CudiBundle\Entity\User\Person\Sale\Acco as AccoCard,
    DateInterval,
    DateTime,
    Doctrine\ORM\EntityManager,
    SecretaryBundle\Entity\Registration;

/**
 * QueueItem Object
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class QueueItem
{
    /**
     * @var \CudiBundle\Entity\Sale\Session The sale session
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
     * @param \CommonBundle\Component\WebSocket\User $user
     */
    public function setUser(User $user)
    {
        $this->_user = $user;
        return $this;
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
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($this->_id);

        return ($item->getStatus() == 'collecting' || $item->getStatus() == 'selling');
    }

    /**
     * @return \CudiBundle\Entity\Sale\QueueItem
     */
    public function getItem()
    {
        return $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($this->_id);
    }

    /**
     * @return string
     */
    public function getCollectInfo()
    {
        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($this->_id);

        $acco = $this->_entityManager
            ->getRepository('CudiBundle\Entity\User\Person\Sale\Acco')
            ->findOneByPerson($item->getPerson());

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
                        'acco' => isset($acco) ? $acco->hasAccoCard() : false,
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
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($this->_id);

        $acco = $this->_entityManager
            ->getRepository('CudiBundle\Entity\User\Person\Sale\Acco')
            ->findOneByPerson($item->getPerson());

        return json_encode(
            array(
                'sale' => array(
                    'id' => $item->getId(),
                    'comment' => $item->getComment(),
                    'person' => array(
                        'id' => $item->getPerson()->getId(),
                        'name' => $item->getPerson()->getFullName(),
                        'universityIdentification' => $item->getPerson()->getUniversityIdentification(),
                        'member' => $item->getPerson()->isMember($this->_getCurrentAcademicYear()),
                        'acco' => isset($acco) ? $acco->hasAccoCard() : false,
                    ),
                    'articles' => $this->_getArticles(),
                )
            )
        );
    }

    /**
     * @param array
     */
    public function setCollectedArticles($articles)
    {
        $this->_articles = $articles;
    }

    /**
     * @param array $articles
     * @param array $discounts
     * @return array
     */
    public function conclude($articles, $discounts)
    {
        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($this->_id);

        $bookings = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findAllOpenByPerson($item->getPerson());

        $memberShipArticles = unserialize(
            $this->_entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.membership_article')
        );

        $soldArticles = array();

        foreach($bookings as $booking) {
            if (!isset($articles->{$booking->getArticle()->getId()}) || $articles->{$booking->getArticle()->getId()} == 0 || !$booking->getArticle()->isSellable())
                continue;

            if ($articles->{$booking->getArticle()->getId()} < $booking->getNumber()) {
                $remainder = new Booking(
                    $this->_entityManager,
                    $booking->getPerson(),
                    $booking->getArticle(),
                    'assigned',
                    $booking->getNumber() - $articles->{$booking->getArticle()->getId()}
                );
                $this->_entityManager->persist($remainder);
                $booking->setNumber($articles->{$booking->getArticle()->getId()})
                    ->setStatus('sold', $this->_entityManager);
            } else {
                $articles->{$booking->getArticle()->getId()} -= $booking->getNumber();
                $booking->setStatus('sold', $this->_entityManager);
            }

            if (isset($soldArticles[$booking->getArticle()->getId()])) {
                $soldArticles[$booking->getArticle()->getId()]['number'] += $booking->getNumber();
            } else {
                $soldArticles[$booking->getArticle()->getId()] = array(
                    'article' => $booking->getArticle(),
                    'number' => $booking->getNumber(),
                );
            }

            if (in_array($booking->getArticle()->getId(), $memberShipArticles)) {
                try {
                    $booking->getPerson()
                        ->addOrganizationStatus(
                            new OrganizationStatus(
                                $booking->getPerson(),
                                'member',
                                $this->_getCurrentAcademicYear()
                            )
                        );

                    $registration = $this->_entityManager
                        ->getRepository('SecretaryBundle\Entity\Registration')
                        ->findOneByAcademicAndAcademicYear($booking->getPerson(), $this->_getCurrentAcademicYear());

                    if (null === $registration) {
                        $registration = new Registration(
                            $booking->getPerson(),
                            $this->_getCurrentAcademicYear()
                        );
                        $this->_entityManager->persist($registration);
                    }
                    $registration->setPayed();
                } catch(\Exception $e) {}
            }
        }

        foreach($articles as $id => $number) {
            if ($number <= 0)
                continue;

            $article = $this->_entityManager
                ->getRepository('CudiBundle\Entity\Sale\Article')
                ->findOneById($id);

            if (!$article->isSellable())
                continue;

            $booking = new Booking(
                $this->_entityManager,
                $item->getPerson(),
                $article,
                'sold',
                $number,
                true
            );
            $this->_entityManager->persist($booking);

            if (isset($soldArticles[$booking->getArticle()->getId()])) {
                $soldArticles[$booking->getArticle()->getId()]['number'] += $booking->getNumber();
            } else {
                $soldArticles[$booking->getArticle()->getId()] = array(
                    'article' => $booking->getArticle(),
                    'number' => $booking->getNumber(),
                );
            }

            if (in_array($booking->getArticle()->getId(), $memberShipArticles)) {
                try {
                    $booking->getPerson()
                        ->addOrganizationStatus(
                            new OrganizationStatus(
                                $booking->getPerson(),
                                'member',
                                $this->_getCurrentAcademicYear()
                            )
                        );

                    $registration = $this->_entityManager
                        ->getRepository('SecretaryBundle\Entity\Registration')
                        ->findOneByAcademicAndAcademicYear($booking->getPerson(), $this->_getCurrentAcademicYear());
                    $registration->setPayed();
                } catch(\Exception $e) {}
            }
        }

        $saleItems = array();
        foreach($soldArticles as $soldArticle) {
            while ($soldArticle['number'] > 0) {
                $price = $soldArticle['article']->getSellPrice();
                $bestDiscount = null;
                foreach($soldArticle['article']->getDiscounts() as $discount) {
                    if (in_array($discount->getRawType(), $discounts)) {
                        if (!$discount->canBeApplied($item->getPerson(), $this->_getCurrentAcademicYear(), $this->_entityManager))
                            continue;
                        if ($discount->alreadyApplied($soldArticle['article'], $item->getPerson(), $this->_entityManager))
                            continue;
                        $newPrice = $discount->apply($soldArticle['article']->getSellPrice());
                        if ($newPrice < $price) {
                            $price = $newPrice;
                            $bestDiscount = $discount;
                        }
                    }
                }

                $number = (isset($bestDiscount) && $bestDiscount->applyOnce()) ? 1 : $soldArticle['number'];
                $saleItem = new SaleItem(
                    $soldArticle['article'],
                    $number,
                    $price * $number / 100,
                    $item,
                    isset($bestDiscount) ? $bestDiscount->getRawType() : null
                );
                $this->_entityManager->persist($saleItem);
                $saleItems[] = $saleItem;

                $soldArticle['number'] -= $number;

                $soldArticle['article']->setStockValue($soldArticle['article']->getStockValue() - $number);
            }
        }

        $hasAccoCard = false;
        foreach($discounts as $discount) {
            $hasAccoCard = ($discount == 'acco');
            if ($hasAccoCard)
                break;
        }
        $acco = $this->_entityManager
            ->getRepository('CudiBundle\Entity\User\Person\Sale\Acco')
            ->findOneByPerson($item->getPerson());

        if (isset($acco)) {
            $acco->setHasAccoCard($hasAccoCard);
        } else {
            $this->_entityManager->persist(new AccoCard($item->getPerson(), $hasAccoCard));
        }

        $this->_entityManager->flush();

        return $saleItems;
    }

    private function _getArticles()
    {
        $item = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($this->_id);

        $bookings = $this->_entityManager
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findAllOpenByPerson($item->getPerson());

        $results = array();
        $bookedArticles = array();
        foreach($bookings as $booking) {
            $barcodes = array();
            foreach($booking->getArticle()->getBarcodes() as $barcode)
                $barcodes[] = $barcode->getBarcode();

            $bookedArticles[] = $booking->getArticle()->getId();

            if (isset($results[$booking->getStatus() . '_' . $booking->getArticle()->getId()])) {
                $results[$booking->getStatus() . '_' . $booking->getArticle()->getId()]['number'] += $booking->getNumber();
            } else {
                $result = array(
                    'id' => $booking->getId(),
                    'articleId' => $booking->getArticle()->getId(),
                    'price' => (int) $booking->getArticle()->getSellPrice(),
                    'title' => $booking->getArticle()->getMainArticle()->getTitle(),
                    'barcode' => $booking->getArticle()->getBarcode(),
                    'barcodes' => $barcodes,
                    'author' => $booking->getArticle()->getMainArticle()->getAuthors(),
                    'number' => $booking->getNumber(),
                    'status' => $booking->getStatus(),
                    'sellable' => $booking->getArticle()->isSellable(),
                    'collected' => isset($this->_articles->{$booking->getArticle()->getId()}) ? $this->_articles->{$booking->getArticle()->getId()} : 0,
                    'discounts' => array(),
                );

                foreach($booking->getArticle()->getDiscounts() as $discount) {
                    if (!$discount->alreadyApplied($booking->getArticle(), $item->getPerson(), $this->_entityManager) &&
                            $discount->canBeApplied($item->getPerson(), $this->_getCurrentAcademicYear(), $this->_entityManager)) {
                        $result['discounts'][] = array(
                            'type' => $discount->getRawType(),
                            'value' => $discount->apply($booking->getArticle()->getSellPrice()),
                            'applyOnce' => $discount->applyOnce(),
                        );
                    }
                }

                $results[$booking->getStatus() . '_' . $booking->getArticle()->getId()] = $result;
            }
        }

        foreach($this->_articles as $id => $number) {
            if (!in_array($id, $bookedArticles) && $number > 0) {
                $article = $this->_entityManager
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findOneById($id);

                $barcodes = array();
                foreach($article->getBarcodes() as $barcode)
                    $barcodes[] = $barcode->getBarcode();

                $result = array(
                    'id' => $booking->getId(),
                    'articleId' => $article->getId(),
                    'price' => $article->getSellPrice(),
                    'title' => $article->getMainArticle()->getTitle(),
                    'barcode' => $article->getBarcode(),
                    'barcodes' => $barcodes,
                    'author' => $article->getMainArticle()->getAuthors(),
                    'number' => 1,
                    'status' => 'assigned',
                    'sellable' => $booking->getArticle()->isSellable(),
                    'collected' => $number,
                    'discounts' => array(),
                );

                foreach($article->getDiscounts() as $discount) {
                    if (!$discount->alreadyApplied($article, $item->getPerson(), $this->_entityManager) &&
                            $discount->canBeApplied($item->getPerson(), $this->_getCurrentAcademicYear(), $this->_entityManager)) {
                        $result['discounts'][] = array(
                            'type' => $discount->getRawType(),
                            'value' => $discount->apply($article->getSellPrice()),
                            'applyOnce' => $discount->applyOnce(),
                        );
                    }
                }
                $results['assigned_' . $article->getId()] = $result;
            }
        }

        return array_values($results);
    }

    /**
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    private function _getCurrentAcademicYear()
    {
        return AcademicYear::getUniversityYear($this->_entityManager);
    }
}
