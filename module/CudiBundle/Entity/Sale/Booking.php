<?php

namespace CudiBundle\Entity\Sale;

use CommonBundle\Entity\User\Person;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\Booking")
 * @ORM\Table(
 *     name="cudi_sale_bookings",
 *     indexes={@ORM\Index(name="cudi_sale_bookings_book_date", columns={"book_date"})}
 * )
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
     * @var Person The person of the booking
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var Article The booked article
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
     * @var DateTime|null The time the booking will expire
     *
     * @ORM\Column(name="expiration_date", type="datetime", nullable=true)
     */
    private $expirationDate;

    /**
     * @var DateTime|null The time the booking was assigned
     *
     * @ORM\Column(name="assignment_date", type="datetime", nullable=true)
     */
    private $assignmentDate;

    /**
     * @var DateTime The time the booking was made
     *
     * @ORM\Column(name="book_date", type="datetime")
     */
    private $bookDate;

    /**
     * @var DateTime|null The time the booking was sold
     *
     * @ORM\Column(name="sale_date", type="datetime", nullable=true)
     */
    private $saleDate;

    /**
     * @var DateTime|null The time the booking was canceled
     *
     * @ORM\Column(name="cancelation_date", type="datetime", nullable=true)
     */
    private $cancelationDate;

    /**
     * @var DateTime|null The time the booking was returned
     *
     * @ORM\Column(name="return_date", type="datetime", nullable=true)
     */
    private $returnDate;

    /**
     * @var string[] The possible states of a booking
     */
    private static $possibleStatuses = array(
        'booked',
        'assigned',
        'sold',
        'expired',
        'canceled',
        'returned',
    );

    /**
     * @throws InvalidArgumentException
     *
     * @param EntityManager $entityManager
     * @param Person        $person        The person of the booking
     * @param Article       $article       The booked article
     * @param string        $status        The status of the booking
     * @param integer       $number        The number of articles booked
     * @param boolean       $force         Force the booking
     */
    public function __construct(EntityManager $entityManager, Person $person, Article $article, $status, $number = 1, $force = false)
    {
        if (!$article->isBookable() && !$force) {
            throw new InvalidArgumentException('The Stock Article cannot be booked.');
        }

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
        return in_array($status, self::$possibleStatuses);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @param Article $article
     *
     * @return self
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
     * @return self
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @param DateTime $bookDate
     *
     * @return self
     */
    public function setBookDate(DateTime $bookDate)
    {
        $this->bookDate = $bookDate;

        return $this;
    }

    /**
     * @return DateTime
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
     * @param DateTime $expirationDate
     *
     * @return self
     */
    public function setExpirationDate(DateTime $expirationDate)
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @return DateTime|null
     */
    public function getAssignmentDate()
    {
        return $this->assignmentDate;
    }

    /**
     * @param DateTime $saleDate
     *
     * @return self
     */
    public function setSaleDate(DateTime $saleDate)
    {
        $this->saleDate = $saleDate;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getSaleDate()
    {
        return $this->saleDate;
    }

    /**
     * @return DateTime|null
     */
    public function getCancelationDate()
    {
        return $this->cancelationDate;
    }

    /**
     * @return DateTime|null
     */
    public function getReturnDate()
    {
        return $this->returnDate;
    }

    /**
     * @param  string        $status
     * @param  EntityManager $entityManager
     * @throws InvalidArgumentException
     * @return self
     */
    public function setStatus($status, EntityManager $entityManager)
    {
        switch ($status) {
            case 'booked':
                if ($this->status != 'assigned') {
                    $this->bookDate = new DateTime();
                }
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
                throw new InvalidArgumentException('The BookingStatus is not valid.');
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
