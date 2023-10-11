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

namespace BrBundle\Entity\Match\Wave;

use BrBundle\Entity\Connection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Connection\Wave\WaveMatcheeMap")
 * @ORM\Table(name="br_match_companywave_match_map")
 */
class WaveMatchMap
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
     *@var CompanyWave The company wave
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Connection\Wave\CompanyWave")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="cascade")
     */
    private $companywave;

    /**
     * @var Connection The match
     *
     * @ORM\OneToOne(targetEntity="\BrBundle\Entity\Connection")
     * @ORM\JoinColumn(name="match", referencedColumnName="id", onDelete="cascade")
     */
    private $connection;

    /**
     * @param Connection       $connection
     * @param CompanyWave $wave
     */
    public function __construct(Connection $connection, CompanyWave $wave)
    {
        $this->companywave = $wave;
        $this->connection = $connection;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Connection
     */
    public function getMatch()
    {
        return $this->connection;
    }

    /**
     * @return CompanyWave
     */
    public function getWave()
    {
        return $this->companywave;
    }
}
