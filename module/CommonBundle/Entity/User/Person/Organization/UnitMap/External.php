<?php

namespace CommonBundle\Entity\User\Person\Organization\UnitMap;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\General\Organization\Unit;
use Doctrine\ORM\Mapping as ORM;

/**
 * Specifying the mapping between organization and external person.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\User\Person\Organization\UnitMap\External")
 * @ORM\Table(name="users_people_organizations_units_map_external")}
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
