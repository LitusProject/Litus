<?php

namespace OnBundle\Entity;

use CommonBundle\Entity\User\Person;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores a slug, and the URL it should redirect to.
 *
 * @ORM\Entity(repositoryClass="OnBundle\Repository\Slug")
 * @ORM\Table(
 *     name="on_slugs",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="on_slugs_name", columns={"name"})}
 * )
 */
class Slug
{
    /**
     * @var integer The ID of this slug
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Person The ID of the person that created this slug
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
     * @ORM\JoinColumn(name="creation_person", referencedColumnName="id")
     */
    private $creationPerson;

    /**
     * @var string The name of the slug
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string The URL this logs redirects to
     *
     * @ORM\Column(type="text")
     */
    private $url;

    /**
     * @var integer How many times this slug was hit
     *
     * @ORM\Column(type="bigint")
     */
    private $hits;

    /**
     * @var DateTime The expiration date of this slug
     *
     * @ORM\Column(name="expiration_date", type="datetime", nullable=true)
     */
    private $expirationDate;

    /**
     * @var boolean The flag whether the slug is active
     *
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @param Person|null $creationPerson
     */
    public function __construct($creationPerson)
    {
        $this->creationPerson = $creationPerson;
        $this->hits = 0;
        $this->active = true;
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
    public function getCreationPerson()
    {
        return $this->creationPerson;
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
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param  string $url
     * @return self
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return integer
     */
    public function getHits()
    {
        return $this->hits;
    }

    /**
     * @param  integer $hits
     * @return self
     */
    public function setHits($hits)
    {
        $this->hits = $hits;

        return $this;
    }

    /**
     * @param  boolean $active
     * @return self
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @return self
     */
    public function incrementHits()
    {
        $this->hits++;

        return $this;
    }

    /**
     * @param DateTime $expirationDate
     *
     * @return self
     */
    public function setExpirationDate(DateTime $expirationDate)
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }
}
