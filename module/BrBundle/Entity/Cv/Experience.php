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

namespace BrBundle\Entity\Cv;

use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for an experience specification on a cv.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Cv\Experience")
 * @ORM\Table(name="br_cv_experiences")
 */
class Experience
{
    /**
     * @var integer The language entry's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var Entry The cv entry where this language belongs to.
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Cv\Entry", cascade={"persist"})
     * @ORM\JoinColumn(name="entry", referencedColumnName="id", onDelete="CASCADE")
     */
    private $entry;

    /**
     * @var string The experience type.
     *
     * @ORM\Column(type="string", nullable = true)
     */
    private $type;

    /**
     * @var string The experience function.
     *
     * @ORM\Column(type="text")
     */
    private $function;

    /**
     * @var string The experience start year.
     *
     * @ORM\Column(type="integer", nullable = true)
     */
    private $startYear;

    /**
     * @var string The experience end year.
     *
     * @ORM\Column(type="integer", nullable = true)
     */
    private $endYear;

    /**
     * @param Entry  $entry
     * @param string $type
     * @param string $function
     * @param string $startYear
     * @param string $startYear
     */
    public function __construct(Entry $entry, $function, $type = null, $startYear = null, $endYear = null)
    {
        $this->entry = $entry;
        $this->type = $type;
        $this->function = $function;
        $this->startYear = $startYear;
        $this->endYear = $endYear;
    }

    /**
     * @return integer id.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Entry The cv entry.
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * @return string The experience function.
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * @return string The experience type.
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string The experience start year.
     */
    public function getStartYear()
    {
        return $this->startYear;
    }

    /**
     * @return string The experience end year.
     */
    public function getEndYear()
    {
        return $this->endYear;
    }
}
