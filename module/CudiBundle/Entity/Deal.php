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

namespace CudiBundle\Entity;

use CommonBundle\Entity\User\Person\Academic;
use CudiBundle\Entity\Retail;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Deal")
 * @ORM\Table(name="cudi_deal")
 */
class Deal
{
    /**
     * @var integer The ID of this deal article
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Retail The retail of the mapping
     *
     * @ORM\ManyToOne(targetEntity="CudiBundle\Entity\Retail")
     * @ORM\JoinColumn(name="retail", referencedColumnName="id")
     */
    private $retail;

    /**
     * @var Academic The buyer
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic")
     * @ORM\JoinColumn(name="buyer", referencedColumnName="id")
     *
     */
    private $buyer;

    public function __construct(Retail $retail, Academic $buyer)
    {
        $this->buyer = $buyer;
        $this->retail = $retail;
    }

    /**
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return \CudiBundle\Entity\Retail
     */
    public function getRetail()
    {
        return $this->retail;
    }

    /**
     * @return Academic
     */
    public function getOwner()
    {
        return $this->retail->getOwner();
    }

    /**
     * @return Academic
     */
    public function getBuyer()
    {
        return $this->buyer;
    }
}
