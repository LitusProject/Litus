<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace ShiftBundle\Entity;

/**
 * This entity stores a unit of the organization.
 *
 * @Entity(repositoryClass="ShiftBundle\Repository\Unit")
 * @Table(name="shifts.units")
 */
class Unit
{
    /**
     * @var integer The ID of this unit
     *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;

    /**
     * @var boolean Whether or not this unit is active
     *
     * @Column(type="boolean")
     */
    private $active;

    /**
     * @var string The unit's name
     *
     * @Column(type="string")
     */
    private $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->active = true;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     * @return \ShiftBundle\Entity\Unit
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @return void
     */
    public function deactivate()
    {
        $this->active = false;
    }
}
