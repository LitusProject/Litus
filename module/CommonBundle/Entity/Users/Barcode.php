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

namespace CommonBundle\Entity\Users;

use CommonBundle\Entity\Users\Person,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores a user's barcode.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\Users\Barcode")
 * @ORM\Table(name="users.barcodes")
 */
class Barcode
{
    /**
     * @var int The ID of this credential
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \CommonBundle\Entity\Users\Person The person associated with this barcode
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Users\Person", inversedBy="barcodes")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var string The barcode
     *
     * @ORM\Column(type="bigint")
     */
    private $barcode;

    /**
     * @var \DateTime The time of creation
     *
     * @ORM\Column(type="datetime")
     */
    private $time;

    /**
     * Constructs a new barcode
     *
     * @param \CommonBundle\Entity\Users\Person $person
     * @param integer $barcode
     */
    public function __construct(Person $person, $barcode)
    {
        $this->person = $person;
        $this->time = new DateTime();

        if (strlen($barcode) == 13)
            $barcode = floor($barcode / 10);

        if (strlen($barcode) != 12)
            throw new \InvalidArgumentException('Invalid barcode given: ' . $algorithm);

        $this->barcode = $barcode;
    }

    /**
     * @return \CommonBundle\Entity\Users\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @return integer
     */
    public function getBarcode()
    {
        return $this->barcode;
    }
}
