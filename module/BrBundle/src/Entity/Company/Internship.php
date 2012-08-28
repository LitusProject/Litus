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

namespace BrBundle\Entity\Company;

use BrBundle\Entity\Company,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for an internship.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Company\Internship")
 * @ORM\Table(name="br.companies_internships")
 */
class Internship
{
    /**
     * @var string The internship's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string The internship's name
     *
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @var string The description of the internship
     *
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var \BrBundle\Entity\Company The company of the internship
     *
     * @ORM\OneToOne(targetEntity="BrBundle\Entity\Company")
     * @ORM\JoinColumn(name="company", referencedColumnName="id")
     */
    private $company;

    /**
     * @param string $name The internship's name
     * @param string $description The internship's description
     * @param \BrBundle\Entity\Company $company The internship's company
     */
    public function __construct($name, $description, Company $company)
    {
        $this->setName($name);
        $this->setDescription($description);

        $this->company = $company;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     * @return \BrBundle\Entity\Company\Internship
     */
    public function setName($name)
    {
        if ((null === $name) || !is_string($name))
            throw new \InvalidArgumentException('Invalid name');

        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $description
     * @return \BrBundle\Entity\Company\Internship
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getSummary($length = 50)
    {
        return substr($this->description, 0, $length) . (strlen($this->description) > $length ? '...' : '');
    }

    /**
     * @return \BrBundle\Entity\Company
     */
    public function getCompany()
    {
        return $this->company;
    }
}
