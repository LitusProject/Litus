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

namespace BrBundle\Entity\Event;

use BrBundle\Entity\Company;
use BrBundle\Entity\Event;
use Doctrine\ORM\Mapping as ORM;
use RuntimeException;

/**
 * CompanyMetadata
 *
 * @author Belian Callaerts <belian.callaerts@vtk.be>
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Event\CompanyMap")
 * @ORM\Table(name="br_events_companies_metadata")
 */
class CompanyMetadata
{
    /**
     * @var integer The ID of the mapping
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    // TODO: The metadata is not defined yet
    /**
     * @var string The master interests of the company
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $master_interests;


    const POSSIBLE_MASTERS = Company::POSSIBLE_MASTERS + array('other' => 'Other');

    /**
     * @param Company $company
     * @param Event   $event
     */
    public function __construct()
    {}

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return
     */
    public function getMasterInterests()
    {
        if (substr($this->master_interests, 0, 2) != 'a:') {
            throw new RuntimeException('Badly formatted master interests in company metadata (get)');
        }
        return unserialize($this->master_interests);
    }

    /**
     * @param  array $master_interests
     * @return CompanyMetadata
     */
    public function setMasterInterests($master_interests)
    {
        if (is_string($master_interests) && substr($this->master_interests, 0, 2) != 'a:') {
            throw new RuntimeException('Badly formatted master interests in company metadata (set)');
        }

        $this->master_interests = serialize($master_interests);

        return $this;
    }
}