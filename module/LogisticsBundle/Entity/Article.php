<?php

namespace LogisticsBundle\Entity;

use CommonBundle\Entity\General\Organization\Unit;
use DateTime;
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
     * @var string Additional information about the article
     *
     * @ORM\Column(name="additional_info", type="text")
     */
    private $additionalInfo;

    /**
     * @var string Additional information about the article (internal)
     *
     * @ORM\Column(name="internal_comment", type="text", nullable=true)
     */
    private $internalComment;

    /**
     * @var integer The amount of owned articles
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
     * @var string The status of this article
     *
     * @ORM\Column(name="status", type="text")
     */
    private $status;

    /**
     * @var string The location of storage of this article
     *
     * @ORM\Column(name="location", type="text")
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
     * @var DateTime The last time this article was updated.
     *
     * @ORM\Column(name="date_updated", type="date")
     */
    private $dateUpdated;

    /**
     * @var string The path to the article's photo
     *
     * @ORM\Column(name="photo_path", type="string", nullable=true)
     */
    private $photoPath;

    /**
     * @var string The mailing address to alert when this article gets booked
     *
     * @ORM\Column(name="alertMail", type="text", nullable=true)
     */
    private $alertMail;

    /**
     * @var Unit The unit which has to be mailed when the article gets booked
     *
    * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Organization\Unit")
    * @ORM\JoinColumn(name="unit", referencedColumnName="id", nullable=true)
    */
    private $unit;

    /**
     * @static
     * @var array All the possible categories allowed
     */
    public static $POSSIBLE_CATEGORIES = array(
        ''                        => '',
        'allerlei'                => 'Allerlei',
        'archief'                 => 'Archief',
        'banners&vlaggen'         => 'Banners & vlaggen',
        'BR&Dev'                  => 'BR & Dev',
        'cantus'                  => 'Cantus',
        'decoratie'               => 'Decoratie',
        'elektriciteit'           => 'Elektriciteit',
        'licht&geluid'            => 'Licht & geluid',
        'fak'                     => 'Fak',
        'keuken'                  => 'Keuken',
        'kledij'                  => 'Kledij',
        'kuisproducten'           => 'Kuisproducten',
        'secrimateriaal'          => 'Secrimateriaal',
        'the game'                => 'The game',
        'touw&tape'               => 'Touw & tape',
        'veiligheid&signalisatie' => 'Veiligheid & signalisatie',
        'verf'                    => 'Verf',
        'vuur'                    => 'Vuur',
        'werkmateriaal'           => 'Werkmateriaal',

        # Flesserke en EHBO: hier moet automatisch materiaal naar toe gebracht worden
        'flesserke'               => 'Flesserke',
        'ehbo'                    => 'EHBO',
    );

    /**
     * @static
     * @var array All the possible visibilities allowed
     */
    public static $POSSIBLE_VISIBILITIES = array(
        'post'       => 'Post',
        'praesidium' => 'Praesidium',
        'greatervtk' => 'Greater VTK',
        'members'    => 'Members',
    );

    /**
     * @static
     * @var array All the possible statuses allowed
     */
    public static $POSSIBLE_STATUSES = array(
        'ok'       => 'In orde',
        'vermist'  => 'Vermist',
        'weg'      => 'Weg',
        'kapot'    => 'Kapot',
        'vuil'     => 'Vuil',
        'aankopen' => 'Aankopen',
        'nakijken' => 'Nakijken',
    );

    public function __construct()
    {
        $this->dateUpdated = new DateTime();
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

        return $this;
    }

    /**
     * @return string
     */
    public function getAlertMail()
    {
        return $this->alertMail;
    }

    /**
     * @param string $alertMail
     */
    public function setAlertMail($alertMail)
    {
        $this->alertMail = $alertMail;

        return $this;
    }

    /**
     * @return Unit
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param Unit $unit
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;

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
     * @return string
     */
    public function getInternalComment()
    {
        return $this->internalComment;
    }

    /**
     * @param  string $internalComment
     * @return self
     */
    public function setInternalComment($internalComment)
    {
        $this->internalComment = $internalComment;

        return $this;
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

        return $this;
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

        return $this;
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
     * @return boolean
     */
    public function isPostVisibility()
    {
        return $this->visibility == 'post';
    }

    /**
     * @return boolean
     */
    public function isPraesidiumVisibility()
    {
        return $this->visibility == 'praesidium';
    }

    /**
     * @return boolean
     */
    public function isGreaterVtkVisibility()
    {
        return $this->visibility == 'greatervtk';
    }

    /**
     * @return boolean
     */
    public function isMemberVisibility()
    {
        return $this->visibility == 'members';
    }

    /**
     * @param string $visibility
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;

        return $this;
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
    public function getStatusKey()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
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

        return $this;
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

        return $this;
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

        return $this;
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

        return $this;
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

        return $this;
    }

    /**
     * @param  string $photoPath
     * @return self
     */
    public function setPhotoPath($photoPath)
    {
        $this->photoPath = $photoPath;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhotoPath()
    {
        return $this->photoPath;
    }
}
