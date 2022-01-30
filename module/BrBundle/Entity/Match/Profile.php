<?php

namespace BrBundle\Entity\Match;

use BrBundle\Entity\Match\Profile\ProfileFeatureMap;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is a profile for a student or company.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Match\Profile")
 * @ORM\Table(name="br_match_profile")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *     "company_profile"="BrBundle\Entity\Match\Profile\CompanyProfile",
 *     "student_profile"="BrBundle\Entity\Match\Profile\StudentProfile"
 * })
 */
abstract class Profile
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
     * @var array The possible types of a profile
     */
    const POSSIBLE_TYPES = array(
        'student' => 'Student',
        'company' => 'Company',
    );

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
     * @param array $features
     */
    public function setFeatures($features)
    {
        $this->features = new ArrayCollection($features);
    }

    /**
     * @param ProfileFeatureMap $feature
     */
    public function addFeature($feature)
    {
        $this->features->add($feature);
    }

    /**
     * @return string
     */
    public function getUserName(EntityManager $em)
    {
        $profile = $em->getRepository('BrBundle\Entity\Match\Profile\ProfileStudentMap')
            ->findOneByProfile($this) ?? $em->getRepository('BrBundle\Entity\Match\Profile\ProfileCompanyMap')
            ->findOneByProfile($this);

        return $profile->getUserName();
    }

    abstract public function getProfileType();
}
