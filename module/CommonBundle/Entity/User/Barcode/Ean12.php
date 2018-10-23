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

namespace CommonBundle\Entity\User\Barcode;

use CommonBundle\Entity\User\Person,
    Doctrine\ORM\Mapping as ORM,
    InvalidArgumentException,
    Zend\Validator\Barcode as BarcodeValidator;

/**
 * This entity stores an EAN12 barcode.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\User\Barcode\Ean12")
 * @ORM\Table(name="users.barcodes_ean12")
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
        $checkdigit = (10 - ($sum % 10)) % 10;

        $ean13 = 10 * $ean12 + $checkdigit;

        return $ean13;
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
            $ean12 = rand(0, (pow(10, 12) - 1));

            $barcode = $entityManager
                ->getRepository('CommonBundle\Entity\User\Barcode\Ean12')
                ->findOneByBarcode($ean12);

            $done = (null === $barcode) && ($validator->isValid(strval($ean12)));
        } while (!$done);

        return $ean12;
    }
}
