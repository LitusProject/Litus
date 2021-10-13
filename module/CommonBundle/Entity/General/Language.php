<?php

namespace CommonBundle\Entity\General;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a language entry that is saved in the database
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\General\Language")
 * @ORM\Table(name="general_languages")
 */
class Language
{
    /**
     * @var integer The ID of the language
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The language abbrev
     *
     * @ORM\Column(type="string", length=4, unique=true)
     */
    private $abbrev;

    /**
     * @var string The language name
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @param string $abbrev The language abbrev
     * @param string $name   The language name
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
