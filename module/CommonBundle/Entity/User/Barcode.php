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

namespace CommonBundle\Entity\User;

use CommonBundle\Entity\User\Person,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores a user's barcode.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\User\Barcode")
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
     * @var Person The person associated with this barcode
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person", inversedBy="barcodes")
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
     * @var DateTime The time of creation
     *
     * @ORM\Column(type="datetime")
     */
    private $time;

    /**
     * Constructs a new barcode
     *
     * @param Person  $person
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
     * @return Person
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
