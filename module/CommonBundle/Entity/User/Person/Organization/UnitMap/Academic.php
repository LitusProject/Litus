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
use CommonBundle\Entity\User\Person\Academic as AcademicEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Specifying the mapping between organization and academic.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\User\Person\Organization\UnitMap\Academic")
 * @ORM\Table(name="users_people_organizations_units_map_academic")
 */
class Academic extends \CommonBundle\Entity\User\Person\Organization\UnitMap
{
    /**
     * @var AcademicEntity The person
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person\Academic", inversedBy="organizationMap")
     * @ORM\JoinColumn(name="academic", referencedColumnName="id")
     */
    private $academic;

    public function __construct(AcademicEntity $academic, AcademicYear $academicYear, Unit $unit, $coordinator, $description = '')
    {
        parent::__construct($academicYear, $unit, $coordinator, $description);
        $this->setAcademic($academic);
    }

    /**
     * @return AcademicEntity
     */
    public function getAcademic()
    {
        return $this->academic;
    }

    /**
     * @param  AcademicEntity $academic
     * @return self
     */
    public function setAcademic(AcademicEntity $academic)
    {
        $this->academic = $academic;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->academic->getFirstName();
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->academic->getLastName();
    }

    /**
     * @return string
     */
    public function getPhotoPath()
    {
        return $this->academic->getPhotoPath();
    }
}
