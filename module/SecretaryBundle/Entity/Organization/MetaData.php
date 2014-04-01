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

namespace SecretaryBundle\Entity\Organization;

use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\User\Person\Academic,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="SecretaryBundle\Repository\Organization\MetaData")
 * @ORM\Table(name="users.organization_metadata")
 */
class MetaData
{
    /**
     * @var int The ID of the metadata
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Academic The person of the metadata
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic")
     * @ORM\JoinColumn(name="academic", referencedColumnName="id")
     */
    private $academic;

    /**
     * @var AcademicYear The academic year of the metadata
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * @var boolean Whether the academic wants to become a member or not
     *
     * @ORM\Column(name="become_member", type="boolean")
     */
    private $becomeMember;

    /**
     * @var boolean Whether the academic wants to receive it's Ir.Reëel at Cudi or not
     *
     * @ORM\Column(name="irreeel_at_cudi", type="boolean")
     */
    private $irreeelAtCudi;

    /**
     * @var boolean Whether the academic wants to receive 't Baske by email or not
     *
     * @ORM\Column(name="bakske_by_mail", type="boolean")
     */
    private $bakskeByMail;

    /**
     * @var string The size of the T-shirt
     *
     * @ORM\Column(name="tshirt_size", type="string", length=4, nullable=true)
     */
    private $tshirtSize;

    /**
     * @var array The possible T-shirt sizes
     */
    public static $possibleSizes = array(
        'M_S' => 'S - Male',
        'M_M' => 'M - Male',
        'M_L' => 'L - Male',
        'M_XL' => 'XL - Male',

        'F_S' => 'S - Female',
        'F_M' => 'M - Female',
        'F_L' => 'L - Female',
        'F_XL' => 'XL - Female',
    );

    /**
     * @param Academic     $academic
     * @param AcademicYear $academicYear
     * @param boolean      $becomeMember
     * @param boolean      $irreeelAtCudi
     * @param boolean      $bakskeByMail
     * @param string       $tshirtSize
     */
    public function __construct(Academic $academic, AcademicYear $academicYear, $becomeMember, $irreeelAtCudi, $bakskeByMail, $tshirtSize)
    {
        if (!self::isValidTshirtSize($tshirtSize))
            throw new \InvalidArgumentException('The T-shirt size is not valid');

        $this->academic = $academic;
        $this->academicYear = $academicYear;
        $this->becomeMember = $becomeMember;
        $this->irreeelAtCudi = $irreeelAtCudi;
        $this->bakskeByMail = $bakskeByMail;
        $this->tshirtSize = $tshirtSize;
    }

    /**
     * @param  string  $size
     * @return boolean
     */
    public static function isValidTshirtSize($size)
    {
        return $size == null || array_key_exists($size, self::$possibleSizes);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Academic
     */
    public function getAcademic()
    {
        return $this->academic;
    }

    /**
     * @return AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }

    /**
     * @return boolean
     */
    public function becomeMember()
    {
        return $this->becomeMember;
    }

    /**
     * @param  boolean $becomeMember
     * @return self
     */
    public function setBecomeMember($becomeMember)
    {
        $this->becomeMember = $becomeMember;

        return $this;
    }

    /**
     * @return boolean
     */
    public function receiveIrReeelAtCudi()
    {
        return $this->irreeelAtCudi;
    }

    /**
     * @param  boolean $irreeelAtCudi
     * @return self
     */
    public function setReceiveIrReeelAtCudi($irreeelAtCudi)
    {
        $this->irreeelAtCudi = $irreeelAtCudi;

        return $this;
    }

    /**
     * @return boolean
     */
    public function bakskeByMail()
    {
        return $this->bakskeByMail;
    }

    /**
     * @param  boolean $bakskeByMail
     * @return self
     */
    public function setBakskeByMail($bakskeByMail)
    {
        $this->bakskeByMail = $bakskeByMail;

        return $this;
    }

    /**
     * @return string
     */
    public function getTshirtSize()
    {
        return $this->tshirtSize;
    }

    /**
     * @return string
     */
    public function getTshirtSizeName()
    {
        if (isset(self::$possibleSizes[$this->tshirtSize]))
            return self::$possibleSizes[$this->tshirtSize];
        return '';
    }

    /**
     * @param  boolean $tshirtSize
     * @return self
     */
    public function setTshirtSize($tshirtSize)
    {
        $this->tshirtSize = $tshirtSize;

        return $this;
    }
}
