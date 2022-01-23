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

namespace BrBundle\Entity\Match;

use BrBundle\Entity\Match\Feature\FeatureRelationMap;
use CommonBundle\Entity\User\Person;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is a profile for a student or company.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Match\Feature")
 * @ORM\Table(name="br_match_feature")
 */
class Feature
{
    /**
     * @var integer The profile's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string The feature's name.
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var ArrayCollection The bonusses of this feature
     *
     * @ORM\ManyToMany(targetEntity="BrBundle\Entity\Match\Feature", inversedBy="theirBonus", indexBy="id")
     * @ORM\JoinTable(
     *      name="br_match_feature_bonus_map",
     *      joinColumns={@ORM\JoinColumn(name="bonus1", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="bonus2", referencedColumnName="id")}
     * )
     */
    private $myBonus;

    /**
     * The features that have this as bonus.
     * @ORM\ManyToMany(targetEntity="BrBundle\Entity\Match\Feature", mappedBy="myBonus")
     */
    private $theirBonus;

    /**
     * @var ArrayCollection The malusses of this feature
     *
     * @ORM\ManyToMany(targetEntity="BrBundle\Entity\Match\Feature", inversedBy="theirMalus", indexBy="id")
     * @ORM\JoinTable(
     *      name="br_match_feature_malus_map",
     *      joinColumns={@ORM\JoinColumn(name="malus1", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="malus2", referencedColumnName="id")}
     * )
     */
    private $myMalus;

    /**
     * The features that have this as malus.
     * @ORM\ManyToMany(targetEntity="BrBundle\Entity\Match\Feature", mappedBy="myMalus")
     */
    private $theirMalus;

    /**
     * @var string The feature's type.
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $type;

    /**
     * @static
     * @var array All the possible types allowed
     */
    public static $possibleTypes = array(
        'company' => 'Company',
        'student' => 'Student'
    );

    /**
     */
    public function __construct()
    {
        $this->myBonus = new ArrayCollection();
        $this->theirBonus = new ArrayCollection();
        $this->theirMalus = new ArrayCollection();
        $this->myMalus = new ArrayCollection();
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    // BONUS FUNCTIONS

    /**
     * @return array
     */
    public function getMyBonus()
    {
        return $this->myBonus->toArray();
    }

    /**
     * @return array
     */
    public function getTheirBonus()
    {
        return $this->theirBonus->toArray();
    }

    /**
     * @return array
     */
    public function getBonus()
    {
        return array_merge($this->getMyBonus(), $this->getTheirBonus());
    }

    /**
     * @param array $bonus
     * @param bool $recursive
     */
    public function setBonus($bonus, $recursive=true)
    {
        $this->myBonus = new ArrayCollection($bonus);
        if ($recursive == true)
            foreach ($bonus as $feature){
                $feature->addTheirBonus($this);
            }
    }

    /**
     * @param Feature $bonus
     * @param bool $recursive
     */
    public function addMyBonus(Feature $bonus, $recursive=true)
    {
        $this->myBonus->add($bonus);
        if ($recursive == true)
            $bonus->addTheirBonus($this);
    }

    /**
     * @param Feature $bonus
     */
    public function addTheirBonus(Feature $bonus)
    {
        $this->theirBonus->add($bonus);
    }

    /**
     * @param Feature $bonus
     * @param bool $recursive
     */
    public function removeMyBonus(Feature $bonus, $recursive=true)
    {
        $this->myBonus->removeElement($bonus); // You need to call $em->flush() to make persist these changes in the database permanently.
        if ($recursive == true)
            $bonus->removeTheirBonus($this);
    }

    /**
     * @param Feature $bonus
     */
    public function removeTheirBonus(Feature $bonus)
    {
        $this->theirBonus->removeElement($bonus);
    }

    // MALUS FUNCTIONS

    /**
     * @return array
     */
    public function getMyMalus()
    {
        return $this->myMalus->toArray();
    }

    /**
     * @return array
     */
    public function getTheirMalus()
    {
        return $this->theirMalus->toArray();
    }

    /**
     * @return array
     */
    public function getMalus()
    {
        return array_merge($this->getMyMalus(), $this->getTheirMalus());
    }

    /**
     * @param array $malus
     * @param bool $recursive
     */
    public function setMalus($malus, $recursive=true)
    {
        $this->myMalus = new ArrayCollection($malus);
        if ($recursive == true)
            foreach ($malus as $feature){
                $feature->addTheirMalus($this);
            }
    }

    /**
     * @param Feature $malus
     * @param bool $recursive
     */
    public function addMyMalus(Feature $malus, $recursive=true)
    {
        $this->myMalus->add($malus);
        if ($recursive == true)
            $malus->addTheirMalus($this);
    }

    /**
     * @param Feature $malus
     */
    public function addTheirMalus(Feature $malus)
    {
        $this->theirMalus->add($malus);
    }

    /**
     * @param Feature $malus
     * @param bool $recursive
     */
    public function removeMyMalus(Feature $malus, $recursive=true)
    {
        $this->myMalus->removeElement($malus); // You need to call $em->flush() to make persist these changes in the database permanently.
        if ($recursive == true)
            $malus->removeTheirMalus($this);
    }

    /**
     * @param Feature $malus
     */
    public function removeTheirMalus(Feature $malus)
    {
        $this->theirMalus->removeElement($malus);
    }
}
