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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace OnBundle\Entity;

use CommonBundle\Entity\User\Person;
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
     * @param Person|null $creationPerson
     */
    public function __construct($creationPerson)
    {
        $this->creationPerson = $creationPerson;
        $this->hits = 0;
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
     * @return self
     */
    public function incrementHits()
    {
        $this->hits++;

        return $this;
    }
}
