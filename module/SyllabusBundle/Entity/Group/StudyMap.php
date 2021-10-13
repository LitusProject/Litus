<?php

namespace SyllabusBundle\Entity\Group;

use Doctrine\ORM\Mapping as ORM;
use SyllabusBundle\Entity\Group;
use SyllabusBundle\Entity\Study;

/**
 * @ORM\Entity(repositoryClass="SyllabusBundle\Repository\Group\StudyMap")
 * @ORM\Table(name="syllabus_studies_groups_map")
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
