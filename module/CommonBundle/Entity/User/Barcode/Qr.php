<?php

namespace CommonBundle\Entity\User\Barcode;

use CommonBundle\Entity\User\Person;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores a QR code.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\User\Barcode\Qr")
 * @ORM\Table(name="users_barcodes_qr")
 */
class Qr extends \CommonBundle\Entity\User\Barcode
{
    /**
     * @var integer The barcode
     *
     * @ORM\Column(type="string")
     */
    private $barcode;

    /**
     * Constructs a new barcode
     *
     * @param Person  $person
     * @param integer $barcode
     */
    public function __construct(Person $person, $barcode)
    {
        parent::__construct($person);

        $this->barcode = $barcode;
    }

    /**
     * @return integer
     */
    public function getBarcode()
    {
        return $this->barcode;
    }
}
