<?php

namespace SyllabusBundle\Entity\Study;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

 /**
  * @ORM\Entity(repositoryClass="SyllabusBundle\Repository\Study\Combination")
  * @ORM\Table(name="syllabus_studies_combinations")
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
     *     name="syllabus_combinations_module_groups_map",
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
