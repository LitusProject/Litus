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

namespace CommonBundle\Entity\General;

/**
 * This class represents a language entry that is saved in the database
 *
 * @Entity(repositoryClass="CommonBundle\Repository\General\Language")
 * @Table(name="general.languages")
 */
class Language
{
    /**
     * @var integer The ID of the language
     *
     * @Id
     * @GeneratedValue
     * @Column(type="bigint")
     */
    private $id;

    /**
     * @var string The language abbrev
     *
     * @Column(type="string", length=4)
     */
    private $abbrev;

    /**
     * @var string The language name
     *
     * @Column(type="string")
     */
    private $name;

    /**
     * @param string $abbrev The language abbrev
     * @param string $name The language name
     */
    public function __construct($abbrev, $name)
    {
        $this->abbrev = $abbrev;
        $this->name = $name;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getAbbrev()
    {
        return $this->abbrev;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
