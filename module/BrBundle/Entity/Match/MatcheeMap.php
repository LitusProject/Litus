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

use BrBundle\Entity\Connection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Connection\MatcheeMap")
 * @ORM\Table(name="br_match_matchee_map")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *     "student_matchee_map"="BrBundle\Entity\Connection\MatcheeMap\StudentMatcheeMap",
 *     "company_matchee_map"="BrBundle\Entity\Connection\MatcheeMap\CompanyMatcheeMap"
 * })
 */
abstract class MatcheeMap
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
     *@var Profile The company-profile of this matchee
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Connection\Profile")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $companyProfile;

    /**
     *@var Profile The student-profile of this matchee
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Connection\Profile")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $studentProfile;

    /**
     * @var Connection The match
     *
     * @ORM\ManyToOne(targetEntity="\BrBundle\Entity\Connection")
     * @ORM\JoinColumn(name="match", referencedColumnName="id", onDelete="cascade")
     */
    private $match;

    /**
     * @param Profile $companyProfile
     * @param Profile $studentProfile
     */
    public function __construct(Profile $companyProfile, Profile $studentProfile)
    {
        $this->companyProfile = $companyProfile;
        $this->studentProfile = $studentProfile;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Profile
     */
    public function getCompanyProfile()
    {
        return $this->companyProfile;
    }

    /**
     * @return Profile
     */
    public function getStudentProfile()
    {
        return $this->studentProfile;
    }

    /**
     * @return Connection
     */
    public function getMatch()
    {
        return $this->match;
    }
}
