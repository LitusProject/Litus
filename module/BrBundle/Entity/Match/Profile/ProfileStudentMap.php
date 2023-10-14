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

use BrBundle\Entity\Match\Profile;
use CommonBundle\Entity\User\Person;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is a profile for a student. The student will use this to save student traits,
 * the company will use this to indicate which traits they desire in future employees.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Connection\Profile\ProfileStudentMap")
 * @ORM\Table(name="br_match_profile_student_map")
 */
class ProfileStudentMap
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
     * @var Person The Person
     *
     * @ORM\ManyToOne(targetEntity="\CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="student", referencedColumnName="id", unique=false, onDelete="CASCADE")
     */
    private $student;

    /**
     * @var Profile The profile
     *
     * @ORM\ManyToOne(targetEntity="\BrBundle\Entity\Connection\Profile")
     * @ORM\JoinColumn(name="profile", referencedColumnName="id", unique=false, onDelete="CASCADE")
     */
    private $profile;

    /**
     * @param Person  $student
     * @param Profile $profile
     */
    public function __construct(Person $student, Profile $profile)
    {
        $this->student = $student;
        $this->profile = $profile;
    }

    /**
     * @return Person
     */
    public function getStudent()
    {
        return $this->student;
    }

    /**
     * @return Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->student->getFullName();
    }
}
