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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Component\WebSocket\Sale;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Component\WebSocket\User,
    CommonBundle\Entity\User\Person\Academic,
    CommonBundle\Entity\User\Status\Organization as OrganizationStatus,
    CommonBundle\Entity\User\Barcode\Ean12 as Ean12,
    CudiBundle\Component\WebSocket\Sale\Printer as Printer,
    CudiBundle\Entity\IsicCard,
    CudiBundle\Entity\Sale\Booking,
    CudiBundle\Entity\Sale\SaleItem,
    CudiBundle\Entity\User\Person\Sale\Acco as AccoCard,
    Doctrine\ORM\EntityManager,
    SecretaryBundle\Entity\Registration,
    Zend\Soap\Client as SoapClient;

/**
 * QueueItem Object
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class QueueItem
{
    /**
     * @var integer The id of the sale session
     */
    private $id;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var User
     */
    private $user;

    /**
     * @var array
     */
    private $articles;

    /**
     * @param EntityManager $entityManager
     * @param integer       $id            The id of the queue item
     */
    public function __construct(EntityManager $entityManager, User $user, $id)
    {
        $this->entityManager = $entityManager;
        $this->id = $id;
        $this->user = $user;
        $this->articles = array();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  User $user
     * @return self
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return boolean
     */
    public function isLocked()
    {
        $item = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($this->id);

        return ($item->getStatus() == 'collecting' || $item->getStatus() == 'selling');
    }

    /**
     * @return \CudiBundle\Entity\Sale\QueueItem
     */
    public function getItem()
    {
        return $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($this->id);
    }

    /**
     * @return string
     */
    public function getCollectInfo()
    {
        $item = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($this->id);

        $acco = $this->entityManager
            ->getRepository('CudiBundle\Entity\User\Person\Sale\Acco')
            ->findOneByPerson($item->getPerson());

        return json_encode(
            array(
                'collect' => array(
                    'id'      => $item->getId(),
                    'comment' => $item->getComment(),
                    'person'  => array(
                        'id'                       => $item->getPerson()->getId(),
                        'name'                     => $item->getPerson()->getFullName(),
                        'universityIdentification' => $item->getPerson()->getUniversityIdentification(),
                        'member'                   => $item->getPerson()->isMember($this->getCurrentAcademicYear()),
                        'acco'                     => isset($acco) ? $acco->hasAccoCard() : false,
                    ),
                    'articles' => $this->getArticles(),
                ),
            )
        );
    }

    /**
     * @return string
     */
    public function getSaleInfo()
    {
        $item = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($this->id);

        $acco = $this->entityManager
            ->getRepository('CudiBundle\Entity\User\Person\Sale\Acco')
            ->findOneByPerson($item->getPerson());

        return json_encode(
            array(
                'sale' => array(
                    'id'      => $item->getId(),
                    'comment' => $item->getComment(),
                    'person'  => array(
                        'id'                       => $item->getPerson()->getId(),
                        'name'                     => $item->getPerson()->getFullName(),
                        'universityIdentification' => $item->getPerson()->getUniversityIdentification(),
                        'member'                   => $item->getPerson()->isMember($this->getCurrentAcademicYear()),
                        'acco'                     => isset($acco) ? $acco->hasAccoCard() : false,
                    ),
                    'articles' => $this->getArticles(),
                ),
            )
        );
    }

    /**
     * @param array
     */
    public function setCollectedArticles($articles)
    {
        $this->articles = $articles;
    }

    /**
     * @param  array $articles
     * @param  array $discounts
     * @return array
     */
    public function conclude($articles, $discounts)
    {
        $item = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($this->id);

        $bookings = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findAllOpenByPerson($item->getPerson());

        $memberShipArticles = unserialize(
            $this->entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('secretary.membership_article')
        );

        $isicArticle = $this->entityManager
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('cudi.isic_sale_article');

        $soldArticles = array();

        foreach ($bookings as $booking) {
            if (!isset($articles->{$booking->getArticle()->getId()}) || $articles->{$booking->getArticle()->getId()} == 0 || !$booking->getArticle()->isSellable()) {
                continue;
            }

            if ($articles->{$booking->getArticle()->getId()} < $booking->getNumber()) {
                $remainder = new Booking(
                    $this->entityManager,
                    $booking->getPerson(),
                    $booking->getArticle(),
                    'assigned',
                    $booking->getNumber() - $articles->{$booking->getArticle()->getId()}
                );
                $this->entityManager->persist($remainder);
                $booking->setNumber($articles->{$booking->getArticle()->getId()})
                    ->setStatus('sold', $this->entityManager);
                $articles->{$booking->getArticle()->getId()} = 0;
            } else {
                $articles->{$booking->getArticle()->getId()} -= $booking->getNumber();
                $booking->setStatus('sold', $this->entityManager);
            }

            if ($booking->getArticle()->getId() == $isicArticle) {
                $isicCard = $this->entityManager
                    ->getRepository('CudiBundle\Entity\IsicCard')
                    ->findOneBy(array('booking' => $booking->getId()));

                if (!$isicCard->hasPaid()) {
                    $client = new SoapClient('http://isicregistrations.guido.be/service.asmx?WSDL');
                    $config = $this->entityManager
                    ->getRepository('CommonBundle\Entity\General\Config');

                    $arguments = array();
                    $arguments['username'] = $config->getConfigValue('cudi.isic_username');
                    $arguments['password'] = $config->getConfigValue('cudi.isic_password');
                    $arguments['userID'] = $isicCard->getCardNumber();

                    $client->hasPaid($arguments);
                    $isicCard->setPaid(true);
                }
            }

            if (isset($soldArticles[$booking->getArticle()->getId()])) {
                $soldArticles[$booking->getArticle()->getId()]['number'] += $booking->getNumber();
            } else {
                $soldArticles[$booking->getArticle()->getId()] = array(
                    'article' => $booking->getArticle(),
                    'number'  => $booking->getNumber(),
                );
            }

            if (in_array($booking->getArticle()->getId(), $memberShipArticles)) {
                $status = $booking->getPerson()
                    ->getOrganizationStatus($this->getCurrentAcademicYear());

                $ean12s = $this->entityManager
                    ->getRepository('CommonBundle\Entity\User\Barcode')
                    ->findValidEan12ByPerson($booking->getPerson());

                if($ean12s === null){
                    $barcode = new Ean12($booking->getPerson(), Ean12::generateUnusedBarcode($this->entityManager));
                    $this->entityManager->persist($barcode);
                }

                if (null === $status) {
                    $booking->getPerson()
                        ->addOrganizationStatus(
                            new OrganizationStatus(
                                $booking->getPerson(),
                                'member',
                                $this->getCurrentAcademicYear()
                            )
                        );
                } else {
                    if ('non_member' === $status->getStatus()) {
                        $status->setStatus('member');
                    }
                }

                Printer::membershipCard(
                    $this->entityManager, 
                    $this->entityManager
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('cudi.card_printer'), 
                    $booking->getPerson(),
                    $this->getCurrentAcademicYear()
                );

                $registration = $this->entityManager
                    ->getRepository('SecretaryBundle\Entity\Registration')
                    ->findOneByAcademicAndAcademicYear($booking->getPerson(), $this->getCurrentAcademicYear());

                if (null === $registration) {
                    $academic = $booking->getPerson();
                    if (!($academic instanceof Academic)) {
                        continue;
                    }

                    $registration = new Registration(
                        $academic,
                        $this->getCurrentAcademicYear()
                    );
                    $this->entityManager->persist($registration);
                }
                $registration->setPayed();
            }
        }

        foreach ($articles as $id => $number) {
            if ($number <= 0) {
                continue;
            }

            $article = $this->entityManager
                ->getRepository('CudiBundle\Entity\Sale\Article')
                ->findOneById($id);

            if (!$article->isSellable()) {
                continue;
            }

            $booking = new Booking(
                $this->entityManager,
                $item->getPerson(),
                $article,
                'sold',
                $number,
                true
            );
            $this->entityManager->persist($booking);

            if (isset($soldArticles[$booking->getArticle()->getId()])) {
                $soldArticles[$booking->getArticle()->getId()]['number'] += $booking->getNumber();
            } else {
                $soldArticles[$booking->getArticle()->getId()] = array(
                    'article' => $booking->getArticle(),
                    'number'  => $booking->getNumber(),
                );
            }

            if (in_array($booking->getArticle()->getId(), $memberShipArticles)) {
                $status = $booking->getPerson()
                    ->getOrganizationStatus($this->getCurrentAcademicYear());
                if (null === $status) {
                    $booking->getPerson()
                        ->addOrganizationStatus(
                            new OrganizationStatus(
                                $booking->getPerson(),
                                'member',
                                $this->getCurrentAcademicYear()
                            )
                        );
                } else {
                    if ('non_member' === $status->getStatus()) {
                        $status->setStatus('member');
                    }
                }

                $registration = $this->entityManager
                    ->getRepository('SecretaryBundle\Entity\Registration')
                    ->findOneByAcademicAndAcademicYear($booking->getPerson(), $this->getCurrentAcademicYear());

                if (null === $registration) {
                    $academic = $booking->getPerson();
                    if (!($academic instanceof Academic)) {
                        continue;
                    }

                    $registration = new Registration(
                        $academic,
                        $this->getCurrentAcademicYear()
                    );
                    $this->entityManager->persist($registration);
                }
                $registration->setPayed();
            }
        }

        $saleItems = array();
        foreach ($soldArticles as $soldArticle) {
            while ($soldArticle['number'] > 0) {
                $price = $soldArticle['article']->getSellPrice();
                $bestDiscount = null;
                foreach ($soldArticle['article']->getDiscounts() as $discount) {
                    if (in_array($discount->getRawType(), $discounts)) {
                        if (!$discount->canBeApplied($this->entityManager, $soldArticle['article'], $item->getPerson(), $this->getCurrentAcademicYear())) {
                            continue;
                        }
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
                $this->entityManager->persist($saleItem);
                $saleItems[] = $saleItem;

                $soldArticle['number'] -= $number;

                $soldArticle['article']->setStockValue($soldArticle['article']->getStockValue() - $number);
            }
        }

        $hasAccoCard = false;
        foreach ($discounts as $discount) {
            $hasAccoCard = ($discount == 'acco');
            if ($hasAccoCard) {
                break;
            }
        }
        $acco = $this->entityManager
            ->getRepository('CudiBundle\Entity\User\Person\Sale\Acco')
            ->findOneByPerson($item->getPerson());

        if (isset($acco)) {
            $acco->setHasAccoCard($hasAccoCard);
        } else {
            $this->entityManager->persist(new AccoCard($item->getPerson(), $hasAccoCard));
        }

        $this->entityManager->flush();

        return $saleItems;
    }

    private function getArticles()
    {
        $item = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\QueueItem')
            ->findOneById($this->id);

        $bookings = $this->entityManager
            ->getRepository('CudiBundle\Entity\Sale\Booking')
            ->findAllOpenByPerson($item->getPerson());

        $results = array();
        $bookedArticles = array();
        foreach ($bookings as $booking) {
            $barcodes = array();
            foreach ($booking->getArticle()->getBarcodes() as $barcode) {
                $barcodes[] = $barcode->getBarcode();
            }

            $bookedArticles[] = $booking->getArticle()->getId();

            if (isset($results[$booking->getStatus() . '_' . $booking->getArticle()->getId()])) {
                $results[$booking->getStatus() . '_' . $booking->getArticle()->getId()]['number'] += $booking->getNumber();
            } else {
                $result = array(
                    'id'        => $booking->getId(),
                    'articleId' => $booking->getArticle()->getId(),
                    'price'     => (int) $booking->getArticle()->getSellPrice(),
                    'title'     => $booking->getArticle()->getMainArticle()->getTitle(),
                    'barcode'   => $booking->getArticle()->getBarcode(),
                    'barcodes'  => $barcodes,
                    'author'    => $booking->getArticle()->getMainArticle()->getAuthors(),
                    'number'    => $booking->getNumber(),
                    'status'    => $booking->getStatus(),
                    'sellable'  => $booking->getArticle()->isSellable(),
                    'collected' => isset($this->articles->{$booking->getArticle()->getId()}) ? $this->articles->{$booking->getArticle()->getId()} : 0,
                    'discounts' => array(),
                );

                foreach ($booking->getArticle()->getDiscounts() as $discount) {
                    if ($discount->canBeApplied($this->entityManager, $booking->getArticle(), $item->getPerson(), $this->getCurrentAcademicYear())) {
                        $result['discounts'][] = array(
                            'type'      => $discount->getRawType(),
                            'value'     => $discount->apply($booking->getArticle()->getSellPrice()),
                            'applyOnce' => $discount->applyOnce(),
                        );
                    }
                }

                $results[$booking->getStatus() . '_' . $booking->getArticle()->getId()] = $result;
            }
        }

        foreach ($this->articles as $id => $number) {
            if (!in_array($id, $bookedArticles) && $number > 0) {
                $article = $this->entityManager
                    ->getRepository('CudiBundle\Entity\Sale\Article')
                    ->findOneById($id);

                $barcodes = array();
                foreach ($article->getBarcodes() as $barcode) {
                    $barcodes[] = $barcode->getBarcode();
                }

                $result = array(
                    'id'        => 0,
                    'articleId' => $article->getId(),
                    'price'     => $article->getSellPrice(),
                    'title'     => $article->getMainArticle()->getTitle(),
                    'barcode'   => $article->getBarcode(),
                    'barcodes'  => $barcodes,
                    'author'    => $article->getMainArticle()->getAuthors(),
                    'number'    => 1,
                    'status'    => 'assigned',
                    'sellable'  => $article->isSellable(),
                    'collected' => $number,
                    'discounts' => array(),
                );

                foreach ($article->getDiscounts() as $discount) {
                    if ($discount->canBeApplied($this->entityManager, $article, $item->getPerson(), $this->getCurrentAcademicYear())) {
                        $result['discounts'][] = array(
                            'type'      => $discount->getRawType(),
                            'value'     => $discount->apply($article->getSellPrice()),
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
    private function getCurrentAcademicYear()
    {
        return AcademicYear::getUniversityYear($this->entityManager);
    }
}
