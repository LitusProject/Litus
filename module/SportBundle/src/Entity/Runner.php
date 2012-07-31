<?php

namespace Litus\Entity\Sport;

use \Litus\Application\Resource\Doctrine as DoctrineResource;

use \Zend\Registry;

/**
 * @Entity(repositoryClass="Litus\Repository\Sport\Runner")
 * @Table(name="sport.runners")
 */
class Runner
{
    /**
     * @var string The runner's university identification
     *
     * @Id
     * @Column(name="university_identification", type="string", length=8)
     */
    private $universityIdentification;

    /**
     * @var string The runner's first name
     *
     * @Column(name="first_name", type="string", length=20)
     */
    private $firstName;

    /**
     * @var string The runner's last name
     *
     * @Column(name="last_name", type="string", length=30)
     */
    private $lastName;

    /**
     * @var \Litus\Entity\Sport\Group The runner's group
     *
     * @ManyToOne(targetEntity="Litus\Entity\Sport\Group", inversedBy="members")
     * @JoinColumn(name="group_of_friends", referencedColumnName="id")
     */
    private $group;

    /**
     * @param string $universityIdentification The runner's university identification
     * @param string $firstName The runner's first name
     * @param string $lastName The runner's last name
     */
    public function __construct($universityIdentification, $firstName, $lastName)
    {
        $this->universityIdentification = $universityIdentification;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getUniversityIdentification()
    {
        return $this->universityIdentification;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    /**
     * @return \Litus\Entity\Sport\Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @throws \InvalidArgumentException
     * @param \Litus\Entity\Sport\Group $group The runner's group
     * @return \Litus\Entity\Sport\Runner
     */
    public function setGroup(Group $group) {
        if (null === $group)
            throw new \InvalidArgumentException('The group cannot be null');
        $this->group = $group;

        return $this;
    }

    /**
     * @return array
     */
    public function getLaps() {
        $entityManager = Registry::get(DoctrineResource::REGISTRY_KEY);

        return $entityManager->getRepository('Litus\Entity\Sport\Lap')
            ->findByRunner($this->universityIdentification);
    }
}
