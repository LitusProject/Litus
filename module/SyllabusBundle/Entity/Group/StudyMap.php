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

namespace SyllabusBundle\Entity\Group;

use CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\Mapping as ORM,
    SyllabusBundle\Entity\Group,
    SyllabusBundle\Entity\Study;

/**
 * @ORM\Entity(repositoryClass="SyllabusBundle\Repository\Group\StudyMap")
 * @ORM\Table(name="syllabus.studies_group_map")
 */
class StudyMap
{
    /**
     * @var integer The ID of the mapping
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Study The study of the mapping
     *
     * @ORM\ManyToOne(targetEntity="SyllabusBundle\Entity\Study")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $study;

    /**
     * @var Group The group of the mapping
     *
     * @ORM\ManyToOne(targetEntity="SyllabusBundle\Entity\Group")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $group;

    /**
     * @param Study $study
     * @param Group $group
     */
    public function __construct(Study $study, Group $group)
    {
        $this->study = $study;
        $this->group = $group;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Study
     */
    public function getStudy()
    {
        return $this->study;
    }
     /**
      * @return Group
      */
     public function getGroup()
     {
         return $this->group;
     }
}
