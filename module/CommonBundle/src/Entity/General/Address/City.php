<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Entity\General\Address;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a address entry that is saved in the database
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\General\Address\City")
 * @ORM\Table(name="general.address_cities")
 */
class City
{
    /**
     * @var integer The ID of the city
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var integer The city postal
     *
     * @ORM\Column(type="smallint")
     */
    private $postal;

    /**
     * @var string The city name
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collection\ArrayCollection The streets in the city
     *
     * @ORM\OneToMany(targetEntity="CommonBundle\Entity\General\Address\Street", mappedBy="city")
     */
    private $streets;

    /**
     * @param integer $postal
     * @param string $name
     */
    public function __construct($postal, $name)
    {
        $this->postal = $postal;
        $this->name = $name;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return integer
     */
    public function getPostal()
    {
        return $this->postal;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getStreets()
    {
        return $this->streets;
    }
}
