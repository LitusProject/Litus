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

namespace BrBundle\Entity\Match\Profile;

use BrBundle\Entity\Match\Feature;
use BrBundle\Entity\Match\Profile;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is a map between a feature and a profile.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Match\Profile\ProfileFeatureMap")
 * @ORM\Table(
 *     name="br_match_profile_feature_map",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="profile_feature_map_feature_profile", columns={"feature", "profile"})}
 * )
 */
class ProfileFeatureMap
{
    /**
     * @var integer The map's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var Feature
     *
     * @ORM\ManyToOne(targetEntity="\BrBundle\Entity\Match\Feature")
     * @ORM\JoinColumn(name="feature", referencedColumnName="id", onDelete="CASCADE")
     */
    private $feature;

    /**
     * @var Profile
     *
     * @ORM\ManyToOne(targetEntity="\BrBundle\Entity\Match\Profile")
     * @ORM\JoinColumn(name="profile", referencedColumnName="id", onDelete="CASCADE")
     */
    private $profile;

    /**
     * @var integer The importance number
     *
     * @ORM\Column(type="integer")
     */
    private $importance;

    /**
     * @static
     * @var array All the possible importances allowed
     */
    public static $POSSIBLE_VISIBILITIES = array(
        0   => 'Not important',
        100 => 'Important',
        200 => 'Very important',
    );

    /**
     * @param Feature $feature
     * @param Profile $profile
     * @param integer $importance
     */
    public function __construct(Feature $feature, Profile $profile, $importance = 100)
    {
        $this->feature = $feature;
        $this->profile = $profile;
        $this->importance = $importance;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Feature
     */
    public function getFeature()
    {
        return $this->feature;
    }

    /**
     * @param Feature $feature
     */
    public function setFeature($feature)
    {
        $this->feature = $feature;
    }

    /**
     * @return Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param Profile $profile
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;
    }

    /**
     * @return integer
     */
    public function getImportance()
    {
        return $this->importance;
    }

    /**
     * @return string
     */
    public function getImportanceName()
    {
        if ($this->feature->isSector()) {
            return $this->getImportance();
        }
        $possible = ProfileFeatureMap::$POSSIBLE_VISIBILITIES;
        if (in_array($this->importance, array_keys($possible))) {
            return $possible[$this->importance];
        }
        return $this->getImportance();
    }

    /**
     * @param integer $importance
     */
    public function setImportance($importance)
    {
        $this->importance = $importance;
    }

    public function getImportanceWorth()
    {
        if ($this->getFeature()->isSector() == true) {
            return $this->importance * 100;
        }
        return $this->importance;
    }
}
