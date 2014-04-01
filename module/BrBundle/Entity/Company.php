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

namespace BrBundle\Entity;

use BrBundle\Entity\User\Person\Corporate,
    CommonBundle\Component\Util\Url,
    CommonBundle\Entity\General\Address,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a company.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Company")
 * @ORM\Table(name="br.companies")
 */
class Company
{
    /**
     * @var string The company's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string The company's name
     *
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @var string The company's URL
     *
     * @ORM\Column(type="string", length=50);
     */
    private $slug;

    /**
     * @var string The company's VAT number
     *
     * @ORM\Column(type="string", name="vat_number")
     */
    private $vatNumber;

    /**
     * @var \CommonBundle\Entity\General\Address The address of the company
     *
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\General\Address", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="address", referencedColumnName="id")
     */
    private $address;

    /**
     * @var string The company's telephone number
     *
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $phoneNumber;

    /**
     * @var string The company's website
     *
     * @ORM\Column(type="text")
     */
    private $website;

    /**
     * @var string The sector of the company
     *
     * @ORM\Column(type="string")
     */
    private $sector;

    /**
     * @var string The logo of the company
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $logo;

    /**
     * @var bool Whether or not this is an active company
     *
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @var \CommonBundle\Entity\General\AcademicYear The years of which this company has access to the CV Book.
     *
     * @ORM\ManyToMany(targetEntity="CommonBundle\Entity\General\AcademicYear", cascade={"persist"})
     * @ORM\JoinTable(name="br.companies_cvbooks",
     *      joinColumns={@ORM\JoinColumn(name="company", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="year", referencedColumnName="id")}
     * )
     */
    private $cvBookYears;

    /**
     * @var string The archive years of which this company has access to the CV Book.
     *
     * @ORM\Column(name="cv_book_archive_years", type="string", nullable=true)
     */
    private $cvBookArchiveYears;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The company's contacts
     *
     * @ORM\OneToMany(targetEntity="BrBundle\Entity\User\Person\Corporate", mappedBy="company")
     */
    private $contacts;

    /**
     * @var \BrBundle\Entity\Company\Page The company's page
     *
     * @ORM\OneToOne(targetEntity="BrBundle\Entity\Company\Page", mappedBy="company")
     */
    private $page;

    /**
     * @var array The possible sectors of a company
     */
    public static $possibleSectors = array(
        'architecture' => 'Architecture & Construction',
        'audit' => 'Audit',
        'automobile' => 'Automobile',
        'biomedical' => 'Biomedical & Pharmaceutical',
        'chemistry' => 'Chemistry',
        'consultancy' => 'Consultancy',
        'consumer' => 'Consumer Goods & Services',
        'distribution' => 'Distribution, Logistics & Transportation',
        'electronics' => 'Electronics',
        'energy' => 'Energy',
        'financial' => 'Financial',
        'it' => 'IT',
        'metal' => 'Metal',
        'telecom' => 'Telecom',
    );

    /**
     * @param string                               $name        The company's name
     * @param string                               $vatNumber   The company's VAT number
     * @param \CommonBundle\Entity\General\Address $address     The company's address
     * @param string                               $phoneNumber The company's telephone number
     * @param string                               $website     The company's website
     * @param string                               $sector      The company's sector
     */
    public function __construct($name, $vatNumber, Address $address, $phoneNumber, $website, $sector)
    {
        $this->setName($name);
        $this->vatNumber = $vatNumber;
        $this->address = $address;
        $this->phoneNumber = $phoneNumber;
        $this->website = $website;
        $this->setSector($sector);
        $this->active = true;

        $this->contacts = new ArrayCollection();
        $this->cvBookYears = new ArrayCollection();
    }

    /**
     * @param  string  $sector
     * @return boolean
     */
    public static function isValidSector($sector)
    {
        return array_key_exists($sector, self::$possibleSectors);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \BrBundle\Entity\Company\Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  string                   $name
     * @return \BrBundle\Entity\Company
     */
    public function setName($name)
    {
        $this->name = $name;
        $this->slug = Url::createSlug($name);

        return $this;
    }

    /**
     * @return string
     */
    public function getVatNumber()
    {
        return $this->vatNumber;
    }

    /**
     * @param  string                   $vatNumber
     * @return \BrBundle\Entity\Company
     */
    public function setVatNumber($vatNumber)
    {
        $this->vatNumber = $vatNumber;

        return $this;
    }

    /**
     * @return \CommonBundle\Entity\General\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param  \CommonBundle\Entity\General\Address $address
     * @return \BrBundle\Entity\Company
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param  string                   $phoneNumber
     * @return \BrBundle\Entity\Company
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param  string                   $website
     * @return \BrBundle\Entity\Company
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Performs some extra formatting on the stored URL.
     *
     * @return string
     */
    public function getFullWebsite()
    {
        $result =  $this->getWebsite();
        if (false === strpos($result, 'http://'))
            $result = 'http://' . $result;

        return $result;
    }

    /**
     * @return string
     */
    public function getSector()
    {
        return self::$possibleSectors[$this->sector];
    }

    /**
     * @param  string                   $sector
     * @return \BrBundle\Entity\Company
     */
    public function setSector($sector)
    {
        if (!self::isValidSector($sector))
            throw new \InvalidArgumentException('The sector is not valid');
        $this->sector = $sector;

        return $this;
    }

    /**
     * Returns the code of the company's sector.
     *
     * @return string
     */
    public function getSectorCode()
    {
        return $this->sector;
    }

    /**
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param  string                   $logo
     * @return \BrBundle\Entity\Company
     */
    public function setLogo($logo)
    {
        $this->logo = trim($logo, '/');

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Deactivates the given company.
     *
     * @return \BrBundle\Entity\Company
     */
    public function deactivate()
    {
        $this->active = false;

        return $this;
    }

    /**
     * @return array
     */
    public function getContacts()
    {
        return $this->contacts->toArray();
    }

    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return array
     */
    public function getCvBookYears()
    {
        return $this->cvBookYears->toArray();
    }

    /**
     * @param array $years
     *
     * @return \BrBundle\Entity\Company
     */
    public function setCvBookYears(array $years)
    {
        $this->cvBookYears = new ArrayCollection($years);

        return $this;
    }

    /**
     * @return array
     */
    public function getCvBookArchiveYears()
    {
        if (null === $this->cvBookArchiveYears || '' == $this->cvBookArchiveYears)
            return array();

        try {
            return unserialize($this->cvBookArchiveYears);
        } catch (\Exception $e) {
            return array();
        }
    }

    /**
     *
     * @return \BrBundle\Entity\Company
     */
    public function setCvBookArchiveYears(array $archiveYears)
    {
        $this->cvBookArchiveYears = serialize($archiveYears);

        return $this;
    }
}
