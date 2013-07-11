<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace CommonBundle\Entity\General\Address;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a address entry that is saved in the database
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\General\Address\Street")
 * @ORM\Table(
 *     name="general.address_streets",
 *    indexes={@ORM\Index(name="street_name", columns={"name"})}
 * )
 */
class Street
{
    /**
     * @var integer The ID of the street
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var integer The register number
     *
     * @ORM\Column(name="register_number", type="smallint")
     */
    private $registerNumber;

    /**
     * @var string The street name
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collection\ArrayCollection The city
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Address\City", inversedBy="streets")
     * @ORM\JoinColumn(name="city", referencedColumnName="id")
     */
    private $city;

    /**
     * @param \CommonBundle\Entity\General\Address\City $city
     * @param integer $registerNumber
     * @param string $name
     */
    public function __construct(City $city, $registerNumber, $name)
    {
        $this->city = $city;
        $this->registerNumber = $registerNumber;
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
