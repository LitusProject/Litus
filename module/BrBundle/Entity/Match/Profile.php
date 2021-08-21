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

use BrBundle\Entity\Match\Profile\ProfileFeatureMap;
use CommonBundle\Entity\User\Person;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is a profile for a student or company.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Match\Profile")
 * @ORM\Table(name="br_match_profile")
 */
class Profile
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
     * @var ArrayCollection The members of this group
     *
     * @ORM\OneToMany(targetEntity="BrBundle\Entity\Match\Profile\ProfileFeatureMap", mappedBy="profile")
     */
    private $features;

    /**
     * @var array The possible types for a profile
     */
    const POSSIBLE_TYPES = array(
        'student'         => 'Student Profile',
        'company'        => 'Company Profile',
    );

    /**
     */
    public function __construct()
    {
        $this->features = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ArrayCollection
     */
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     * @param ArrayCollection $features
     */
    public function setFeatures($features)
    {
        $this->features = $features;
    }
}
