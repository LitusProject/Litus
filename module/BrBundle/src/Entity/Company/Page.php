<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Entity\Company;

use BrBundle\Entity\Users\People\Corporate,
    CommonBundle\Component\Util\Url,
    CommonBundle\Entity\General\Address,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a company's page.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Company\Page")
 * @ORM\Table(name="br.companies_page")
 */
class Page
{
    /**
     * @var string The page's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string The summary of the company
     *
     * @ORM\Column(type="text")
     */
    private $summary;

    /**
     * @var string The description of the company
     *
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var BrBundle\Entity\Company
     *
     * @ORM\OneToOne(targetEntity="BrBundle\Entity\Company", inversedBy="page")
     */
    private $company;

    /**
     * @param string $company The company
     * @param string $summary The page's summary
     * @param string $description The page's description
     */
    public function __construct($company, $summary, $description)
    {
        $this->setSummary($summary);
        $this->setDescription($description);
        $this->company = $company;
    }

    /**
     * @return boolean
     */
    public static function isValidSector($sector)
    {
        return array_key_exists($sector, self::$POSSIBLE_SECTORS);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param string $summary
     * @return \BrBundle\Entity\Company\Page
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param string $description
     * @return \BrBundle\Entity\Company\Page
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
}
