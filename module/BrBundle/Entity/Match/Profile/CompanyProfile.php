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
use Doctrine\ORM\Mapping as ORM;

/**
 * This is a profile for a company. The company will use this to save company traits,
 * the student will use this to indicate which traits they desire in future employers.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Match\Profile\CompanyProfile")
 * @ORM\Table(name="br_match_profile_companyprofile")
 */
class CompanyProfile extends Profile
{
//    /**
//     * @var \BrBundle\Entity\Company The profile for a company
//     *
//     * @ORM\OneToOne(targetEntity="\BrBundle\Entity\Company")
//     * @ORM\JoinColumn(name="company", referencedColumnName="id")
//     */
//    private $company;


    public function __construct()
    {
        Profile::__construct();
    }

    public function getProfileType()
    {
        return 'company';
    }
}
