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
 *
 * @license http://litus.cc/LICENSE
 */

namespace QuizBundle\Entity;

use CommonBundle\Entity\User\Person,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a quiz.
 *
 * @ORM\Entity(repositoryClass="QuizBundle\Repository\Quiz")
 * @ORM\Table(name="quiz.quizes")
 */
class Quiz
{
    /**
     * @var The entry's unique identifier
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The name of the quiz
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var Person The person created this quiz
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var DateTime The create time
     *
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @var ArrayCollection The rounds in this quiz
     *
     * @ORM\OneToMany(targetEntity="QuizBundle\Entity\Round", mappedBy="quiz", cascade="remove")
     */
    private $rounds;

    /**
     * @var ArrayCollection The teams in this quiz
     *
     * @ORM\OneToMany(targetEntity="QuizBundle\Entity\Team", mappedBy="quiz", cascade="remove")
     */
    private $teams;

    /**
     * @var ArrayCollection The roles that can edit this quiz
     *
     * @ORM\ManyToMany(targetEntity="CommonBundle\Entity\Acl\Role")
     * @ORM\JoinTable(
     *      name="quiz.quizes_roles_map",
     *      joinColumns={@ORM\JoinColumn(name="quiz", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role", referencedColumnName="name")}
     * )
     */
    private $editRoles;

    /**
     * @param Person $person
     * @param string $name
     * @param array  $editRoles
     */
    public function __construct(Person $person, $name, $editRoles)
    {
        $this->person = $person;
        $this->name = $name;
        $this->editRoles = new ArrayCollection($editRoles);
        $this->timestamp = new DateTime();
        $this->rounds = new ArrayCollection;
        $this->teams = new ArrayCollection;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param  array $editRoles
     * @return self
     */
    public function setEditRoles(array $editRoles)
    {
        $this->editRoles = new ArrayCollection($editRoles);

        return $this;
    }

    /**
     * @return array
     */
    public function getEditRoles()
    {
        return $this->editRoles->toArray();
    }

    /**
     * Checks whether or not the given user can edit the quiz.
     *
     * @param  Person  $person The person that should be checked
     * @return boolean
     */
    public function canBeEditedBy(Person $person = null)
    {
        if (null === $person)
            return false;

        foreach ($person->getFlattenedRoles() as $role) {
            if ($this->editRoles->contains($role) || $role->getName() == 'editor')
                return true;
        }

        return false;
    }

    public function getRounds()
    {
        return $this->rounds->toArray();
    }

    public function getTeams()
    {
        return $this->teams->toArray();
    }
}
