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

namespace SyllabusBundle\Entity;

use CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="SyllabusBundle\Repository\Group")
 * @ORM\Table(name="syllabus.groups")
 */
class Group
{
    /**
     * @var integer The ID of the group
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The title of the group
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var boolean Whether to use this group in the cv book or not.
     *
     * @ORM\Column(name="cv_book", type="boolean")
     */
    private $cvBook;

    /**
     * @param string $name
     * @param boolean $cvBook
     */
    public function __construct($name, $cvBook)
    {
        $this->name = $name;
        $this->cvBook = $cvBook;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return \SyllabusBundle\Entity\Group
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getCvBook()
    {
        return $this->cvBook;
    }

    /**
     * @param boolean $cvBook
     * @return \SyllabusBundle\Entity\Group
     */
    public function setCvBook($cvBook)
    {
        $this->cvBook = $cvBook;
        return $this;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @return \SyllabusBundle\Entity\Group
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->_entityManager = $entityManager;
        return $this;
    }

    /**
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear
     * @return integer
     */
    public function getNbStudents(AcademicYear $academicYear)
    {
        return $this->_entityManager
            ->getRepository('SyllabusBundle\Entity\Group')
            ->findNbStudentsByGroupAndAcademicYear($this, $academicYear);
    }
}
