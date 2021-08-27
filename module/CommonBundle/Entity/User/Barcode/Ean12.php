<?php

namespace CommonBundle\Entity\User\Barcode;

use CommonBundle\Entity\User\Person;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Laminas\Validator\Barcode as BarcodeValidator;

/**
 * This entity stores an EAN12 barcode.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\User\Barcode\Ean12")
 * @ORM\Table(name="users_barcodes_ean12")
 */
class Ean12 extends \CommonBundle\Entity\User\Barcode
{
    /**
     * @var integer The barcode
     *
     * @ORM\Column(type="bigint", length=12)
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

        if (strlen($barcode) == 13) {
            $barcode = (int) floor($barcode / 10);
        }

        $validator = new BarcodeValidator(
            array(
                'adapter'     => 'EAN12',
                'useChecksum' => false,
            )
        );

        if (!$validator->isValid(strval($barcode))) {
            throw new InvalidArgumentException('Invalid EAN12 barcode given: ' . $barcode);
        }

        $this->barcode = $barcode;
    }

    /**
     * @return integer
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * @return integer
     */
    public function getPrintableBarcode()
    {
        $ean12 = $this->barcode;
        $split = str_split($ean12);

        $weight1 = 0;
        $weight3 = 0;
        for ($i = 0; $i < 6; $i++) {
            $weight1 += (int) $split[$i * 2];
            $weight3 += (int) $split[$i * 2 + 1];
        }
        $sum = 1 * $weight1 + 3 * $weight3;
        $checkdigit = 10 - ($sum % 10) % 10;

        return 10 * $ean12 + $checkdigit;
    }

    /**
     * @return integer
     */
    public static function generate($entityManager)
    {
        $validator = new BarcodeValidator(
            array(
                'adapter'     => 'EAN12',
                'useChecksum' => false,
            )
        );

        do {
            $ean12 = rand(0, pow(10, 12) - 1);

            $barcode = $entityManager
                ->getRepository('CommonBundle\Entity\User\Barcode\Ean12')
                ->findOneByBarcode($ean12);

            $done = ($barcode === null) && $validator->isValid(strval($ean12));
        } while (!$done);

        return $ean12;
    }
}
