<?php

namespace TicketBundle\Entity;

use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Laminas\Mail\Message;
use Laminas\Mime\Mime;
use Laminas\Mime\Part;
use TicketBundle\Entity\Event\Option;

/**
 * @ORM\Entity(repositoryClass="TicketBundle\Repository\Ticket")
 * @ORM\Table(
 *     name="ticket_tickets",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="ticket_tickets_event_number", columns={"event", "number"}),
 *     @ORM\UniqueConstraint(name="ticket_tickets_invoice_id", columns={"invoice_id"})}
 * )
 */
class Ticket
{
    /**
     * @var array The possible states of a ticket
     */
    public static $possibleStatuses = array(
        'empty'  => 'Empty',
        'booked' => 'Booked',
        'sold'   => 'Sold',
    );

    /**
     * @var integer The ID of the ticket
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Event The event of the ticket
     *
     * @ORM\ManyToOne(targetEntity="TicketBundle\Entity\Event", inversedBy="tickets")
     * @ORM\JoinColumn(name="event", referencedColumnName="id")
     */
    private $event;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $status;

    /**
     * @var Person|null The person who bought/reserved the ticket
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var GuestInfo|null The guest info of who bought/reserved the ticket
     *
     * @ORM\ManyToOne(targetEntity="TicketBundle\Entity\GuestInfo")
     * @ORM\JoinColumn(name="guest_info", referencedColumnName="id")
     */
    private $guestInfo;

    /**
     * @var DateTime|null The date the ticket was booked
     *
     * @ORM\Column(name="book_date", type="datetime", nullable=true)
     */
    private $bookDate;

    /**
     * @var DateTime|null The date the ticket was sold
     *
     * @ORM\Column(name="sold_date", type="datetime", nullable=true)
     */
    private $soldDate;

    /**
     * @var integer|null The number of the ticket (unique for an event)
     *
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $number;

    /**
     * @var Option|null The option of the ticket
     *
     * @ORM\ManyToOne(targetEntity="TicketBundle\Entity\Event\Option")
     * @ORM\JoinColumn(name="option", referencedColumnName="id")
     */
    private $option;

    /**
     * @var boolean|null Flag whether the ticket was sold to a member
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $member;

    /**
     * @var integer|null The number of the ticket (unique for an event)
     *
     * @ORM\Column(name="invoice_id", type="string", nullable=true)
     */
    private $invoiceId;

    /**
     * @var integer|null The number of the ticket (unique for an event)
     *
     * @ORM\Column(name="order_id", type="string", nullable=true, length=11)
     */
    private $orderId;

    /**
     * @var string|null Paypage Betaalreferentie
     *
     * @ORM\Column(name="pay_id", type="string", nullable=true)
     */
    private $payId;

    /**
     * @var string Unique identifier for QR code of the ticket
     *
     * @ORM\Column(name="qr_code", type="text", unique=true, nullable=true)
     */
    private $qrCode;

    /**
     * @var string The amount of the ticket
     *
     * @ORM\Column(name="amount", type="text", nullable=true)
     */
    private $amount;

    /**
     * @var string the university mail. This is needed for the uniflow printer
     *
     * @ORM\Column(name="university_mail", type="text", nullable=true)
     */
    private $universityMail;

    /**
     * @param EntityManager  $em
     * @param Event          $event
     * @param string         $status
     * @param Person|null    $person
     * @param GuestInfo|null $guestInfo
     * @param DateTime|null  $bookDate
     * @param DateTime|null  $soldDate
     * @param integer|null   $number
     */
    public function __construct(EntityManager $em, Event $event, $status, Person $person = null, GuestInfo $guestInfo = null, DateTime $bookDate = null, DateTime $soldDate = null, $number = null)
    {
        if (!self::isValidTicketStatus($status)) {
            throw new InvalidArgumentException('The TicketStatus is not valid.');
        }

        $this->event = $event;
        if ($event->isOnlinePayment()) {
            $nb = $event->findNextInvoiceNb($em);
            $this->invoiceId = $event->getInvoiceIdBase() . $nb;
            $this->orderId = $event->getOrderIdBase() . $nb;
        }
        $this->status = $status;
        $this->person = $person;
        $this->guestInfo = $guestInfo;
        $this->bookDate = $bookDate;
        $this->soldDate = $soldDate;
        $this->number = $number;
    }

    /**
     * @param  string $status
     * @return boolean
     */
    public static function isValidTicketStatus($status)
    {
        return array_key_exists($status, self::$possibleStatuses);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return self::$possibleStatuses[$this->status];
    }

    /**
     * @return string
     */
    public function getStatusCode()
    {
        return $this->status;
    }

    /**
     * @param  string $status
     * @return self
     * @throws InvalidArgumentException
     */
    public function setStatus($status)
    {
        if (!self::isValidTicketStatus($status)) {
            throw new InvalidArgumentException('The TicketStatus is not valid.');
        }

        if ($status == 'empty') {
            $this->person = null;
            $this->guestInfo = null;
            $this->bookDate = null;
            $this->soldDate = null;
        } elseif ($status == 'sold') {
            if ($this->bookDate == null) {
                $this->bookDate = new DateTime();
            }
            $this->soldDate = new DateTime();
        } elseif ($status == 'booked') {
            $this->bookDate = new DateTime();
            $this->soldDate = null;
        }

        $this->status = $status;

        return $this;
    }

    /**
     * @return Person|null
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param  Person|null $person
     * @return self
     */
    public function setPerson(Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * @return GuestInfo|null
     */
    public function getGuestInfo()
    {
        return $this->guestInfo;
    }

    /**
     * @param  GuestInfo|null $guestInfo
     * @return self
     */
    public function setGuestInfo(GuestInfo $guestInfo = null)
    {
        $this->guestInfo = $guestInfo;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        if ($this->person !== null) {
            return $this->person->getFullName();
        }

        if ($this->guestInfo !== null) {
            return $this->guestInfo->getfullName();
        }

        return '';
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        if ($this->person !== null) {
            return $this->person->getEmail();
        }

        if ($this->guestInfo !== null) {
            return $this->guestInfo->getEmail();
        }

        return '';
    }

    /**
     * @return string
     */
    public function getOrganization()
    {
        if ($this->person !== null) {
            return 'ACCOUNT';
        }

        if ($this->guestInfo !== null) {
            return $this->guestInfo->getOrganization();
        }

        return '';
    }

    /**
     * @return string
     */
    public function getUniversityIdentification()
    {
        if ($this->person !== null) {
            return $this->person->getUniversityIdentification();
        }

        if ($this->guestInfo !== null) {
            return $this->guestInfo->getUniversityIdentification();
        }

        return '';
    }

    /**
     * @return DateTime|null
     */
    public function getBookDate()
    {
        return $this->bookDate;
    }

    /**
     * @param  DateTime $bookDate
     * @return self
     */
    public function setBookDate(DateTime $bookDate)
    {
        $this->bookDate = $bookDate;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getSoldDate()
    {
        return $this->soldDate;
    }

    /**
     * @param  DateTime $soldDate
     * @return self
     */
    public function setSoldDate(DateTime $soldDate)
    {
        $this->soldDate = $soldDate;

        return $this;
    }

    /**
     * @return integer|null
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param  integer $number
     * @return self
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return Option|null
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * @param  Option|null $option
     * @return self
     */
    public function setOption(Option $option = null)
    {
        $this->option = $option;

        return $this;
    }

    /**
     * @return boolean|null
     */
    public function isMember()
    {
        return $this->member;
    }

    /**
     * @param  boolean $member
     * @return self
     */
    public function setMember($member)
    {
        $this->member = $member;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getInvoiceId()
    {
        return $this->invoiceId;
    }

    /**
     * @param string $invoiceId
     */
    public function setInvoiceId(string $invoiceId)
    {
        $this->invoiceId = $invoiceId;
    }

    /**
     * @return string|null
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param string $orderId
     */
    public function setOrderId(string $orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @return string|null
     */
    public function getPayId()
    {
        return $this->payId;
    }

    /**
     * @param string|null $payId
     *
     * @return self
     */
    public function setPayId($payId)
    {
        $this->payId = $payId;

        return $this;
    }

    /**
     * @return integer
     */
    public function getPrice()
    {
        $option_or_event = $this->getOption() ?? $this->getEvent();
        if ($this->isMember() === true) {
            $price = $option_or_event->getPriceMembers();
        } else {
            $price = $option_or_event->getPriceNonMembers();
        }

        return number_format($price / 100, 2);
    }

    /**
     * @return string
     */
    public function getQrCode()
    {
        return $this->qrCode;
    }

    /**
     * @return self
     */
    public function setQrCode()
    {
        if ($this->status === 'sold' && $this->qrCode === null) {
            try {
                $this->qrCode = bin2hex(random_bytes(10));
            } catch (\Exception $e) {
                echo 'Something went wrong with setting the QR code';
            }
        }

//        return $this;
    }

    /**
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param $amount
     * @return self
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return string
     */
    public function getUniversityMail()
    {
        return $this->universityMail;
    }

    /**
     * @param $mail
     * @return self
     */
    public function setUniversityMail($mail)
    {
        $this->universityMail = $mail;
        return $this;
    }

    /**
     * @param $controller
     * @param $language
     * @return void
     */
    public function sendQrMail($controller, $language)
    {
        $event = $this->event;

        $entityManager = $controller->getEntityManager();
        if ($language === null) {
            $language = $entityManager->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev('en');
        }

        $mailData = unserialize(
            $entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('ticket.subscription_mail_data')
        );

        $message = $mailData[$language->getAbbrev()]['content'];
        $subject = str_replace('{{event}}', $event->getActivity()->getTitle($language), $mailData[$language->getAbbrev()]['subject']);

        $mailAddress = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('ticket.subscription_mail');

        $mailName = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('ticket.subscription_mail_name');

        $url = $controller->url()
            ->fromRoute(
                'ticket',
                array('action' => 'qr',
                    'id'       => $event->getRandId(),
                    'qr'     => $this->getQrCode()
                ),
                array('force_canonical' => true)
            );

        $url = str_replace('leia.', '', $url);
        $url = str_replace('liv.', '', $url);

        $qrSource = str_replace(
            '{{encodedUrl}}',
            urlencode($url),
            $controller->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('br.google_qr_api')
        );

        $message = str_replace('{{event}}', $event->getActivity()->getTitle($language), $message);
        $message = str_replace('{{eventDate}}', $event->getActivity()->getStartDate()->format('d/m/Y'), $message);
        $message = str_replace('{{qrSource}}', $qrSource, $message);
        $message = str_replace('{{qrLink}}', $url, $message);
        $message = str_replace('{{actiMail}}', $mailAddress, $message);
        $message = str_replace('{{ticketOption}}', $this->getOption() ? $this->getOption()->getName() : 'base', $message);

        $part = new Part($message);

        $part->type = Mime::TYPE_HTML;
        $part->charset = 'utf-8';
        $newMessage = new \Laminas\Mime\Message();
        $newMessage->addPart($part);
        $mail = new Message();
        $mail->setEncoding('UTF-8')
            ->setBody($newMessage)
            ->setFrom($mailAddress, $mailName)
            ->addTo($this->getEmail(), $this->getFullName())
            ->setSubject($subject)
            ->addBcc($entityManager
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('system_administrator_mail'),
                'System Administrator')
            ->addBcc($mailAddress);

        if (getenv('APPLICATION_ENV') != 'development') {
            $controller->getMailTransport()->send($mail);
        }
    }
}
