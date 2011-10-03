<?php

namespace Litus\Entity\Sport;

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
}