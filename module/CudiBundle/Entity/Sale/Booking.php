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

namespace CudiBundle\Entity\Sale;

use CommonBundle\Entity\User\Person,
    CudiBundle\Entity\Sale\Article,
    DateInterval,
    DateTime,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\Booking")
 * @ORM\Table(name="cudi.sales_bookings", indexes={@ORM\Index(name="sales_booking_time", columns={"bookDate"})})
 */
class Booking
{
    /**
     * @var integer The ID of the booking
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \CommonBundle\Entity\User\Person The person of the booking
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var \CudiBundle\Entity\Sale\Article The booked article
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Sale\Article")
     * @ORM\JoinColumn(name="article", referencedColumnName="id")
     */
    private $article;

    /**
     * @var integer The number of articles booked
     *
     * @ORM\Column(type="smallint")
     */
    private $number;

    /**
     * @var string The status of the booking
     *
     * @ORM\Column(type="string", length=50)
     */
    private $status;

    /**
     * @var \DateTime The time the booking will expire
     *
     * @ORM\Column(name="expirationdate", type="datetime", nullable=true)
     */
    private $expirationDate;

    /**
     * @var \DateTime The time the booking was assigned
     *
     * @ORM\Column(name="assignmentdate", type="datetime", nullable=true)
     */
    private $assignmentDate;

    /**
     * @var \DateTime The time the booking was made
     *
     * @ORM\Column(name="bookdate", type="datetime")
     */
    private $bookDate;

    /**
     * @var \DateTime The time the booking was sold
     *
     * @ORM\Column(name="saledate", type="datetime", nullable=true)
     */
    private $saleDate;

    /**
     * @var \DateTime The time the booking was canceled
     *
     * @ORM\Column(name="cancelationdate", type="datetime", nullable=true)
     */
    private $cancelationDate;

    /**
     * @var \DateTime The time the booking was returned
     *
     * @ORM\Column(name="returndate", type="datetime", nullable=true)
     */
    private $returnDate;

    /**
     * @var array The possible states of a booking
     */
    private static $POSSIBLE_STATUSES = array(
        'booked', 'assigned', 'sold', 'expired', 'canceled', 'returned'
    );

    /**
     * @throws \InvalidArgumentException
     *
     * @param \Doctrine\ORM\EntityManager      $entityManager
     * @param \CommonBundle\Entity\User\Person $person        The person of the booking
     * @param \CudiBundle\Entity\Sale\Article  $article       The booked article
     * @param string                           $status        The status of the booking
     * @param integer                          $number        The number of articles booked
     * @param boolean                          $force         Force the booking
     */
    public function __construct(EntityManager $entityManager, Person $person, Article $article, $status, $number = 1, $force = false)
    {
        if (!$article->isBookable() && !$force)
            throw new \InvalidArgumentException('The Stock Article cannot be booked.');

        $this->person = $person;
        $this->bookDate = new DateTime();
        $this->setArticle($article)
            ->setNumber($number)
            ->setStatus($status, $entityManager);
    }

    /**
     * @return boolean
     */
    public static function isValidBookingStatus($status)
    {
        return in_array($status, self::$POSSIBLE_STATUSES);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \CommonBundle\Entity\User\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @return \CudiBundle\Entity\Sale\Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @param \CudiBundle\Entity\Sale\Article $article
     *
     * @return \CudiBundle\Entity\Sale\Booking
     */
    public function setArticle($article)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param integer $number
     *
     * @return \CudiBundle\Entity\Sale\Booking
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @param \DateTime $bookDate
     *
     * @return \CudiBundle\Entity\Sale\Booking
     */
    public function setBookDate(DateTime $bookDate)
    {
        $this->bookDate = $bookDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getBookDate()
    {
        return $this->bookDate;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param \DateTime $expirationDate
     *
     * @return \CudiBundle\Entity\Sale\Booking
     */
    public function setExpirationDate(DateTime $expirationDate)
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @param \DateTime $saleDate
     *
     * @return \CudiBundle\Entity\Sale\Booking
     */
    public function setSaleDate(DateTime $saleDate)
    {
        $this->saleDate = $saleDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getSaleDate()
    {
        return $this->saleDate;
    }

    /**
     * @return \DateTime
     */
    public function getCancelationDate()
    {
        return $this->cancelationdate;
    }

    /**
     * @return \DateTime
     */
    public function getReturnDate()
    {
        return $this->returnDate;
    }

    /**
     * @param string $status
     *
     * @return \CudiBundle\Entity\Sale\Booking
     */
    public function setStatus($status, $entityManager)
    {
        switch ($status) {
            case 'booked':
                if ($this->status != 'assigned')
                    $this->bookDate = new DateTime();
                $this->assignmentDate = null;
                $this->saleDate = null;
                $this->cancelationDate = null;
                $this->expirationDate = null;
                break;
            case 'assigned':
                $this->assignmentDate = new DateTime();
                $this->saleDate = null;
                $this->cancelationDate = null;

                if ($this->article->canExpire()) {
                    $expireTime = $entityManager
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('cudi.reservation_expire_time');

                    $now = new DateTime();
                    $this->expirationDate = $now->add(new DateInterval($expireTime));
                }
                break;
            case 'sold':
                $this->saleDate = new DateTime();
                $this->cancelationDate = null;
                break;
            case 'expired':
                $this->saleDate = null;
                $this->cancelationDate = null;
                break;
            case 'canceled':
                $this->saleDate = null;
                $this->cancelationDate = new DateTime();
                break;
            case 'returned':
                $this->returnDate = new DateTime();
                $this->cancelationDate = null;
                break;
            default:
                throw new \InvalidArgumentException('The BookingStatus is not valid.');
        }

        $this->status = $status;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isExpired()
    {
        return $this->expirationDate < new DateTime();
    }
}
