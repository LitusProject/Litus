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

namespace LogisticsBundle\Entity;

use CommonBundle\Component\Util\StringUtil;
use CommonBundle\Entity\General\Location;
use CommonBundle\Entity\General\Organization\Unit;
use CommonBundle\Entity\User\Person;
use CommonBundle\Entity\User\Person\Academic;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use LogisticsBundle\Entity\Order\OrderArticleMap;
use Parsedown;
use function Functional\push;

/**
 * This is the entity for an order.
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Entity\Order")
 * @ORM\Table(name="logistics_order")
 */
class Order
{
    /**
     * @var integer The order's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string The order's name
     *
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @var string The description of the order
     *
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var string The mail-address for the contact for this order
     *
     * @ORM\Column(name="email", type="text", nullable=true)
     */
    private $email;

    /**
     * @var Location the location of the order
     *
     * @ORM\ManytoOne(targetEntity="\CommonBundle\Entity\General\Location")
     * @ORM\JoinColumn(name="location", referencedColumnName="id")
     */
    private $location;

    /**
     * @var Academic The contact used in this order
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic")
     * @ORM\JoinColumn(name="contact", referencedColumnName="id")
     */
    private $contact;

    /**
     * @var Unit The unit of the order
     *
     * @ORM\ManyToOne(targetEntity="\CommonBundle\Entity\General\Organization\Unit")
     * @ORM\JoinColumn(name="unit", referencedColumnName="id")
     */
    private $unit;

    /**
     * @var DateTime The last time this order was updated.
     *
     * @ORM\Column(type="date")
     */
    private $dateUpdated;

    /**
     * @var DateTime The start date and time of this order.
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var DateTime The end date and time of this order.
     *
     * @ORM\Column(name="end_date", type="datetime")
     */
    private $endDate;

    /**
     * @var boolean If this order has been approved by our Logistics team
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $approved;

    /**
     * @var boolean If this order has been removed.
     *
     * @ORM\Column(type="boolean", options={"default" = false})
     */
    private $removed;

    /**
     * @var ArrayCollection An array of \LogisticsBundle\Entity\Order\OrderArticleMap indicating which articles (and how many) are ordered (reserved).
     *
     * @ORM\OneToMany(targetEntity="LogisticsBundle\Entity\Order\OrderArticleMap", mappedBy="order_id")
     */
    private $articles;

    /**
     * @param Person $contact The order's contact
     */
    public function __construct(Person $contact)
    {
        $this->contact = $contact;
        $this->dateUpdated = new DateTime();
        $this->removed = false;
    }

    /**
     * @return array
     */
    public function getArticles()
    {
        return $this->Articles;
    }

    /**
     * @param orderArticleMap $article
     */
    public function addArticles($article)
    {
        array_push($this->Articles, $article);
    }

    /**
     * @return self
     */
    public function approve()
    {
        $this->approved = true;

        return $this;
    }

    /**
     * @return self
     */
    public function pending()
    {
        $this->approved = false;

        return $this;
    }

    /**
     * @return self
     */
    public function remove()
    {
        $this->removed = true;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isRemoved()
    {
        return $this->removed;
    }

    /**
     * @return boolean
     */
    public function isApproved()
    {
        if ($this->approved === null) {
            return true;
        }

        return $this->approved;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  string $name
     * @return Order
     */
    public function setName($name)
    {
        if ($name === null || !is_string($name)) {
            throw new InvalidArgumentException('Invalid name');
        }

        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  string $description
     * @return Order
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param  string $email
     * @return Order
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }


    /**
     * @return Unit
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @return Order
     */
    public function updateDate()
    {
        $this->dateUpdated = new DateTime();

        return $this;
    }

    /**
     * @return DateTime The last time this order was updated.
     */
    public function getLastUpdateDate()
    {
        return $this->dateUpdated;
    }

    /**
     * @param  DateTime $startDate
     * @return Order
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param  DateTime $endDate
     * @return Order
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param Location $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return Person
     */
    public function getContact()
    {
        return $this->contact;
    }
}
