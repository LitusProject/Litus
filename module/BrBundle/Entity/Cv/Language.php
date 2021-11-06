<?php

namespace BrBundle\Entity\Cv;

use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * This is the entity for a language specification on a cv.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Cv\Language")
 * @ORM\Table(name="br_cv_languages")
 */
class Language
{
    /**
     * @var array The possible written skills.
     */
    public static $writtenSkills = array(
        'none'          => 'None_skills',
        'active'        => 'Active (Writing)',
        'passive'       => 'Passive (Reading)',
        'Notions'       => 'Notions',
        'Basis'         => 'Basis',
        'Good'          => 'Good',
        'Very good'     => 'Very good',
        'Mother tongue' => 'Mother tongue',
    );

    /**
     * @var array The possible oral skills.
     */
    public static $oralSkills = array(
        'none'          => 'None_skills',
        'active'        => 'Active (Speaking)',
        'passive'       => 'Passive (Listening)',
        'Notions'       => 'Notions',
        'Basis'         => 'Basis',
        'Good'          => 'Good',
        'Very good'     => 'Very good',
        'Mother tongue' => 'Mother tongue',
    );

    /**
     * @var integer The language entry's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var Entry The cv entry where this language belongs to.
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Cv\Entry", cascade={"persist"})
     * @ORM\JoinColumn(name="entry", referencedColumnName="id", onDelete="CASCADE")
     */
    private $entry;

    /**
     * @var string The written skills.
     *
     * @ORM\Column(type="string")
     */
    private $written;

    /**
     * @var string The oral skills.
     *
     * @ORM\Column(type="string")
     */
    private $oral;

    /**
     * @param Entry  $entry
     * @param string $name
     * @param string $written
     * @param string $oral
     */
    public function __construct(Entry $entry, $name, $written, $oral)
    {
        $this->entry = $entry;
        $this->name = $name;
        $this->setWrittenSkill($written);
        $this->setOralSkill($oral);
    }

    /**
     * @return integer id.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Entry The cv entry.
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * @return string The language name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  string $skill
     * @return boolean
     */
    public static function isValidWrittenSkill($skill)
    {
        return array_key_exists($skill, self::$writtenSkills);
    }

    /**
     * @param  string $skill
     * @return boolean
     */
    public static function isValidOralSkill($skill)
    {
        return array_key_exists($skill, self::$oralSkills);
    }

    /**
     * @param  string $skill
     * @return self
     */
    public function setWrittenSkill($skill)
    {
        if (!self::isValidWrittenSkill($skill)) {
            throw new InvalidArgumentException('The skill is not valid.');
        }
        $this->written = $skill;

        return $this;
    }

    /**
     * @return string
     */
    public function getWrittenSkill()
    {
        return self::$writtenSkills[$this->written];
    }

    /**
     * @return string
     */
    public function getWrittenSkillCode()
    {
        return $this->written;
    }

    /**
     * @param  string $skill
     * @return self
     */
    public function setOralSkill($skill)
    {
        if (!self::isValidOralSkill($skill)) {
            throw new InvalidArgumentException('The skill is not valid.');
        }

        $this->oral = $skill;

        return $this;
    }

    /**
     * @return string
     */
    public function getOralSkill()
    {
        return self::$oralSkills[$this->oral];
    }

    /**
     * @return string
     */
    public function getOralSkillCode()
    {
        return $this->oral;
    }
}
