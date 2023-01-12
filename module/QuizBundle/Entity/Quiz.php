<?php

namespace QuizBundle\Entity;

use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a quiz.
 *
 * @ORM\Entity(repositoryClass="QuizBundle\Repository\Quiz")
 * @ORM\Table(name="quiz_quizes")
 */
class Quiz
{
    /**
     * @var integer The entry's unique identifier
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
     * @var Tiebreaker The tiebreaker to determine winner if equal
     *
     * @ORM\OneToOne(targetEntity="QuizBundle\Entity\Tiebreaker", mappedBy="quiz")
     * @ORM\JoinColumn(name="tiebreaker", referencedColumnName="id", nullable=true, onDelete="set null")
     */
    private $tiebreaker;

    /**
     * @var ArrayCollection The roles that can edit this quiz
     *
     * @ORM\ManyToMany(targetEntity="CommonBundle\Entity\Acl\Role")
     * @ORM\JoinTable(
     *      name="quiz_quizes_roles_map",
     *      joinColumns={@ORM\JoinColumn(name="quiz", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role", referencedColumnName="name")}
     * )
     */
    private $editRoles;

    /**
     * @param Person $person
     */
    public function __construct(Person $person)
    {
        $this->person = $person;
        $this->timestamp = new DateTime();

        $this->editRoles = new ArrayCollection();
        $this->rounds = new ArrayCollection();
        $this->teams = new ArrayCollection();
    }

    /**
     * @return integer
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
     * @param string $name
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
     * @param array $editRoles
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
     * @param Person|null $person The person that should be checked
     * @return boolean
     */
    public function canBeEditedBy(Person $person = null)
    {
        if ($person === null) {
            return false;
        }

        foreach ($person->getFlattenedRoles() as $role) {
            if ($this->editRoles->contains($role) || $role->getName() == 'editor') {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getRounds()
    {
        return $this->rounds->toArray();
    }

    /**
     * @return array
     */
    public function getTeams()
    {
        return $this->teams->toArray();
    }

    /**
     * @return Tiebreaker
     */
    public function getTiebreaker()
    {
        return $this->tiebreaker;
    }

    /**
     * @param Tiebreaker $tiebreaker
     * @return self
     */
    public function setTiebreaker(Tiebreaker $tiebreaker)
    {
        $this->tiebreaker = $tiebreaker;

        return $this;
    }
}
