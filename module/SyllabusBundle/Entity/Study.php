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

namespace SyllabusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="SyllabusBundle\Repository\Study")
 * @ORM\Table(name="syllabus.studies")
 */
class Study
{
    /**
     * @var integer The ID of the study
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var integer The id of the KUL
     *
     * @ORM\Column(type="integer", name="kul_id", nullable=true)
     */
    private $kulId;

    /**
     * @var string The title of the study
     *
     * @ORM\Column(type="string", length=300)
     */
    private $title;

    /**
     * @var integer The phase number of the study
     *
     * @ORM\Column(type="smallint")
     */
    private $phase;

    /**
     * @var string The language of the study
     *
     * @ORM\Column(type="string", length=2)
     */
    private $language;

    /**
     * @var \SyllabusBundle\Entity\Study The parent study of the study
     *
     * @ORM\ManyToOne(targetEntity="SyllabusBundle\Entity\Study", inversedBy="children")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id")
     */
    private $parent;

    /**
     * @var \SyllabusBundle\Entity\Study The parent study of the study
     *
     * @ORM\OneToMany(targetEntity="SyllabusBundle\Entity\Study", mappedBy="parent")
     */
    private $children;

    /**
     * @param string $title
     * @param integer $kulId
     * @param integer $phase
     * @param string $language
     * @param \SyllabusBundle\Entity\Study $parent
     */
    public function __construct($title, $kulId, $phase, $language, Study $parent = null)
    {
        $this->title = $title;
        $this->kulId = $kulId;
        $this->phase = $phase;
        $this->language = $language;
        $this->parent = $parent;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return \SyllabusBundle\Entity\Study
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getFullTitle()
    {
        if (null == $this->parent) {
            return $this->title;
        } else {
            if (null == $this->parent->getParent()) {
                return $this->parent->getFullTitle() . ': ' . $this->title;
            } else {
                return $this->parent->getFullTitle() . ' - ' . $this->title;
            }
        }
    }

    /**
     * @return integer
     */
    public function getPhase()
    {
        return $this->phase;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return \SyllabusBundle\Entity\Study
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return array
     */
    public function getParents()
    {
        if ($this->parent)
            return array_merge(array($this->parent), $this->parent->getParents());
        return array();
    }

    /**
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function getAllChildren()
    {
        if ($this->getChildren())
            $directChildren = $this->getChildren()->toArray();
        else
            $directChildren = array();

        $result = array();
        foreach ($directChildren as $child) {
            $result = array_merge($result, $child->getAllChildren());
        }

        $result = array_merge($result, $directChildren);
        return $result;
    }
}
