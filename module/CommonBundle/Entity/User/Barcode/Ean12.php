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
    InvalidArgumentException;

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

        if (strlen($barcode) != 12) {
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
    public static function generateUnusedBarcode($entityManager){
        do{
            $ean12 = mt_rand(0, pow(10, 12) - 1); // Random 12 digit random number

            $match = $entityManager
                ->getRepository('CommonBundle\Entity\User\Barcode\Ean12')
                ->findOneByBarcode($ean12);
        }while($match != null);
        return $ean12;
    }

    /**
     * @return integer
     */
    public function getPrintableCode(){
        $ean12 = $this->barcode;
        $splitted = str_split($ean12);

        $weight1 = 0;
        $weight3 = 0;
        for($i = 0; $i < 6; $i++){
            $weight3 += (int) $splitted[$i*2];
            $weight1 += (int) $splitted[$i*2+1];
        } 
        $sum = 1*$weight1 + 3*$weight3;
        $checkdigit = $sum%10;

        $ean13 = 10*$ean12 + $checkdigit;

        return $ean13;
    }
}
