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
  * @ORM\Entity(repositoryClass="SyllabusBundle\Repository\Study\ModuleGroup")
  * @ORM\Table(name="syllabus_study_module_group")
  */
class ModuleGroup
{
    /**
     * @var integer The ID of the module group
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
     * @var string The title of the module group
     *
     * @ORM\Column(type="string", length=400)
     */
    private $title;

    /**
     * @var integer The phase number of the module group
     *
     * @ORM\Column(type="smallint")
     */
    private $phase;

    /**
     * @var string The language of the module group
     *
     * @ORM\Column(type="string", length=2)
     */
    private $language;

    /**
     * @var boolean Flag indicating whether this module group is mandatory
     *
     * @ORM\Column(type="boolean")
     */
    private $mandatory;

    /**
     * @var ModuleGroup The parent module group of the module group
     *
     * @ORM\ManyToOne(targetEntity="SyllabusBundle\Entity\Study\ModuleGroup", inversedBy="children")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id")
     */
    private $parent;

    /**
     * @var ArrayCollection The children studies of the module group
     *
     * @ORM\OneToMany(targetEntity="SyllabusBundle\Entity\Study\ModuleGroup", mappedBy="parent")
     */
    private $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
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
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param  string $language
     * @return self
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isMandatory()
    {
        return $this->mandatory;
    }

    /**
     * @param  boolean $mandatory
     * @return self
     */
    public function setMandatory($mandatory)
    {
        $this->mandatory = $mandatory;

        return $this;
    }

    /**
     * @return ModuleGroup|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param  ModuleGroup|null $parent
     * @return self
     */
    public function setParent(ModuleGroup $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }
}
