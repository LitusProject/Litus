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

namespace CudiBundle\Entity;

use CommonBundle\Entity\General\Address,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Supplier")
 * @ORM\Table(name="cudi.suppliers")
 */
class Supplier
{
    /**
     * @var integer The ID of the supplier
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The name of the supplier
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string The phone number of the supplier
     *
     * @ORM\Column(type="string", name="phone_number", nullable=true)
     */
    private $phoneNumber;

    /**
     * @var \CommonBundle\Entity\General\Address The address of the supplier
     *
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\General\Address", cascade={"persist"})
     * @ORM\JoinColumn(name="address", referencedColumnName="id")
     */
    private $address;

    /**
     * @var string The vat number of the supplier
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $vatNumber;

    /**
     * @param string $name
     * @param string $phoneNumber
     * @param \CommonBundle\Entity\General\Address $address
     * @param string $vatNumber
     */
    public function __construct($name, $phoneNumber, Address $address, $vatNumber)
    {
        $this->setName($name)
            ->setPhoneNumber($phoneNumber)
            ->setAddress($address)
            ->setVatNumber($vatNumber);
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
     *
     * @return \CudiBundle\Entity\Supplier
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * @param string $phoneNumber
     *
     * @return \CudiBundle\Entity\Supplier
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
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
     * @param \CommonBundle\Entity\General\Address $address
     *
     * @return \CudiBundle\Entity\Supplier
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;
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
     * @param string $vatNumber
     *
     * @return \CudiBundle\Entity\Supplier
     */
    public function setVatNumber($vatNumber)
    {
        $this->vatNumber = $vatNumber;
        return $this;
    }
}
