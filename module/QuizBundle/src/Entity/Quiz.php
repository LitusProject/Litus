<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace QuizBundle\Entity;

use CommonBundle\Entity\Users\Person,
    DateTime,
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
     * @var \CommonBundle\Entity\Users\Person The person created this quiz
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var \DateTime The create time
     *
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The roles that can edit this quiz
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
     * @param \CommonBundle\Entity\Users\Person $person
     * @param string $name
     * @param array $editRoles
     */
    public function __construct(Person $person, $name, $editRoles)
    {
        $this->person = $person;
        $this->name = $name;
        $this->editRoles = $editRoles;
        $this->timestamp = new DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
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
     * @return \QuizBundle\Entity\Quiz
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
     * @return \QuizBundle\Entity\Quiz
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
}