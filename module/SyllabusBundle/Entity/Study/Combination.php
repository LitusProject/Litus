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

namespace SyllabusBundle\Entity\Study;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

 /**
  * @ORM\Entity(repositoryClass="SyllabusBundle\Repository\Study\Combination")
  * @ORM\Table(name="syllabus_study_combinations")
  */
class Combination
{
    /**
     * @var integer The ID of the study combination
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var integer The id of the external database
     *
     * @ORM\Column(type="bigint", name="external_id", nullable=true)
     */
    private $externalId;

    /**
     * @var string The title of the study combination
     *
     * @ORM\Column(type="string", length=400)
     */
    private $title;

    /**
     * @var integer The phase of the study combination
     *
     * @ORM\Column(type="smallint")
     */
    private $phase;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="SyllabusBundle\Entity\Study\ModuleGroup")
     * @ORM\JoinTable(
     *     name="syllabus_combination_module_group_map",
     *     joinColumns={@ORM\JoinColumn(name="combination", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="module_group", referencedColumnName="id")}
     * )
     */
    private $moduleGroups;

    public function __construct()
    {
        $this->moduleGroups = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return integer
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @param  integer $externalId
     * @return self
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param  string $title
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return integer
     */
    public function getPhase()
    {
        return $this->phase;
    }

    /**
     * @param  integer $phase
     * @return self
     */
    public function setPhase($phase)
    {
        $this->phase = $phase;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getModuleGroups()
    {
        return $this->moduleGroups;
    }

    /**
     * @param  array $moduleGroups
     * @return self
     */
    public function setModuleGroups(array $moduleGroups)
    {
        $this->moduleGroups = new ArrayCollection($moduleGroups);

        return $this;
    }

    /**
     * @param  ModuleGroup $moduleGroup The study that should be removed
     * @return self
     */
    public function removeModuleGroup(ModuleGroup $moduleGroup)
    {
        $this->moduleGroups->removeElement($moduleGroup);

        return $this;
    }

    /**
     * @param  ModuleGroup $moduleGroup The study that should be added
     * @return self
     */
    public function addModuleGroup(ModuleGroup $moduleGroup)
    {
        $this->moduleGroups->add($moduleGroup);

        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        foreach ($this->moduleGroups as $group) {
            return $group->getLanguage();
        }

        return '';
    }
}
