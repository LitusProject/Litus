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

use CommonBundle\Entity\General\Location;
use CommonBundle\Entity\User\Person\Academic;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * The entity for a stock article
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Article")
 * @ORM\Table(name="logistics_article")
 */
class Article
{
    /**
     * @var integer The item's ID
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The name of the article
     *
     * @ORM\Column(type="text")
     */
    private $name;

    /**
     * @var ArrayCollection An array of \LogisticsBundle\Entity\Order\OrderArticleMap indicating when this article is ordered (reserved).
     *
     * @ORM\OneToMany(targetEntity="LogisticsBundle\Entity\Order\OrderArticleMap", mappedBy="article_id")
     */
    private $orders;

    /**
     * @var string Additional information about the article
     *
     * @ORM\Column(name="additional_info", type="text")
     */
    private $additionalInfo;

    /**
     * @var int The amount of owned articles
     *
     * @ORM\Column(name="amount_owned", type="integer")
     */
    private $amountOwned;

    /**
     * @var integer The amount of available articles
     *
     * @ORM\Column(name="amount_available", type="integer")
     */
    private $amountAvailable;

    /**
     * @var string The visibility of this article
     *
     * @ORM\Column(name="visibility", type="text")
     */
    private $visibility;

    /**
     * @static
     * @var array All the possible visibilities allowed
     */
    public static $POSSIBLE_VISIBILITIES = array(
        'internal' => 'Internal',
        'external' => 'External',
        'private' => 'Private',
    );

    /**
     * @var string The status of this article
     *
     * @ORM\Column(name="status", type="text")
     */
    private $status;

    /**
     * @static
     * @var array All the possible statuses allowed
     */
    public static $POSSIBLE_STATUSES = array(
        'okay' => 'Okay',
        'lost' => 'Lost',
        'broken' => 'Broken',
        'dirty' => 'Dirty',
//        TODO: vragen aan Sietze
    );

    /**
     * @var Location the location of the article
     *
     * @ORM\ManyToOne(targetEntity="\CommonBundle\Entity\General\Location")
     * @ORM\JoinColumn(name="location", referencedColumnName="id")
     */
    private $location;

    /**
     * @var string The spot in the location of storage of this article
     *
     * @ORM\Column(name="spot", type="text")
     */
    private $spot;

    /**
     * @var integer The warranty of this article
     *
     * @ORM\Column(name="warranty", type="integer")
     */
    private $warranty;

    /**
     * @var integer The rent of this article
     *
     * @ORM\Column(name="rent", type="integer")
     */
    private $rent;

    /**
     * @var string The type of this article
     *
     * @ORM\Column(name="category", type="text")
     */
    private $category;

    /**
     * @static
     * @var array All the possible categories allowed
     */
    public static $POSSIBLE_CATEGORIES = array(
//        TODO: vragen aan Sietze
        'varia' => 'Varia',
        'sound' => 'Sound',
    );

    /**
     * @var DateTime The last time this article was updated.
     *
     * @ORM\Column(name="date_updated", type="date")
     */
    private $dateUpdated;

    /**
     */
    public function __construct()
    {
        $this->dateUpdated = new DateTime();
        $this->orders = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSpot()
    {
        return $this->spot;
    }

    /**
     * @param string $spot
     */
    public function setSpot($spot)
    {
        $this->spot = $spot;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalInfo()
    {
        return $this->additionalInfo;
    }

    /**
     * @param  string $additionalInfo
     * @return self
     */
    public function setAdditionalInfo($additionalInfo)
    {
        $this->additionalInfo = $additionalInfo;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param ArrayCollection $orders
     */
    public function addOrder($orders)
    {
        $this->orders->add($orders);
    }

    /**
     * @return integer
     */
    public function getAmountOwned()
    {
        return $this->amountOwned;
    }

    /**
     * @param integer $amountOwned
     */
    public function setAmountOwned($amountOwned)
    {
        $this->amountOwned = $amountOwned;
    }

    /**
     * @return integer
     */
    public function getAmountAvailable()
    {
        return $this->amountAvailable;
    }

    /**
     * @param integer $amountAvailable
     */
    public function setAmountAvailable($amountAvailable)
    {
        $this->amountAvailable = $amountAvailable;
    }

    /**
     * @return string
     */
    public function getVisibility()
    {
        return Article::$POSSIBLE_VISIBILITIES[$this->visibility];
    }

    /**
     * @return string
     */
    public function getVisibilityCode()
    {
        return $this->visibility;
    }

    /**
     * @param string $visibility
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return Article::$POSSIBLE_STATUSES[$this->status];
    }

    /**
     * @return string
     */
    public function getStatusCode()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }


    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return integer
     */
    public function getWarranty()
    {
        return $this->warranty;
    }

    /**
     * @param integer $warranty
     */
    public function setWarranty($warranty)
    {
        $this->warranty = $warranty;
    }

    /**
     * @return integer
     */
    public function getRent()
    {
        return $this->rent;
    }

    /**
     * @param integer $rent
     */
    public function setRent($rent)
    {
        $this->rent = $rent;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return Article::$POSSIBLE_CATEGORIES[$this->category];
    }

    /**
     * @return string
     */
    public function getCategoryCode()
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return DateTime
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * @param DateTime $dateUpdated
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;
    }

    /**
     * @param Order\OrderArticleMap $mapping
     */
    public function removeMapping(Order\OrderArticleMap $mapping)
    {
        $this->orders->remove($mapping);
    }


}
