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

namespace CommonBundle\Entity\User\Person\Organization\UnitMap;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\General\Organization\Unit;
use Doctrine\ORM\Mapping as ORM;

/**
 * Specifying the mapping between organization and external person.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\User\Person\Organization\UnitMap\External")
 * @ORM\Table(name="users_people_organizations_unit_map_external")}
 * )
 */
class External extends \CommonBundle\Entity\User\Person\Organization\UnitMap
{
    /**
     * @var string The path to the external person's photo
     *
     * @ORM\Column(name="photo_path", type="string", nullable=true)
     */
    private $photoPath;

    /**
     * @var string The first name of the external person in the unit
     *
     * @ORM\Column(name="first_name", type="string", nullable=false)
     */
    private $firstName;

    /**
     * @var string The last name of the external person in the unit.
     *
     * @ORM\Column(name="last_name", type="string", nullable=false)
     */
    private $lastName;

    public function __construct($firstName, $lastName, $photoPath, AcademicYear $academicYear, Unit $unit, $coordinator, $description = '')
    {
        parent::__construct($academicYear, $unit, $coordinator, $description);
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->photoPath = $photoPath;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getPhotoPath()
    {
        return $this->photoPath;
    }
}
