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

namespace BrBundle\Entity\Match;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is a wave consisting of many companyWaves.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Connection\Wave")
 * @ORM\Table(name="br_match_wave")
 */
class Wave
{
    /**
     * @var integer The wave's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string The wave's name.
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var ArrayCollection The company waves
     *
     * @ORM\OneToMany(targetEntity="\BrBundle\Entity\Connection\Wave\CompanyWave", mappedBy="wave")
     * @ORM\JoinColumn(name="company_waves", referencedColumnName="id")
     */
    private $companyWaves;

    /**
     * @var DateTime The time of creation of this node
     *
     * @ORM\Column(name="creation_time", type="datetime", nullable=true)
     */
    private $creationTime;

    public function __construct()
    {
        $this->companyWaves = new ArrayCollection();
        $this->creationTime = new DateTime();
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getCompanyWaves()
    {
        return $this->companyWaves->toArray();
    }

    /**
     * @return DateTime
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    /**
     * @return string
     */
    public function getCreationTimeString()
    {
        return $this->creationTime->format('Y/m/d H:i');
    }

    /**
     * @return boolean
     */
    public function isGenerated()
    {
        return $this->companyWaves->count() > 0;
    }
}
