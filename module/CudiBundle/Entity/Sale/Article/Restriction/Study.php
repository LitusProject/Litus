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

namespace CudiBundle\Entity\Sale\Article\Restriction;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\User\Person,
    CudiBundle\Entity\Sale\Article,
    CudiBundle\Entity\Sale\Article\Restriction,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM,
    SyllabusBundle\Entity\Study as StudyEntity;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\Article\Restriction\Study")
 * @ORM\Table(name="cudi.sales_articles_restrictions_study")
 */
class Study extends Restriction
{
    /**
     * @var string|null The value of the restriction
     *
     * @ORM\ManyToMany(targetEntity="SyllabusBundle\Entity\Study")
     * @ORM\JoinTable(name="cudi.sales_articles_restrictions_study_map",
     *      joinColumns={@ORM\JoinColumn(name="restriction", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="study", referencedColumnName="id")}
     * )
     */
    private $studies;

    /**
     * @param Article $article The article of the restriction
     */
    public function __construct(Article $article)
    {
        parent::__construct($article);

        $this->studies = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'study';
    }

    /**
     * @return string
     */
    public function getValue()
    {
        $value = '';
        foreach ($this->studies as $study)
            $value .= 'Phase ' . $study->getPhase() . ' - ' . $study->getFullTitle() . ' ; ';

        return $value;
    }

    /**
     * @return string|null
     */
    public function getStudies()
    {
        return $this->studies;
    }

    /**
     * @param  SyllabusBundle\Entity\Study $study
     * @return self
     */
    public function addStudy(StudyEntity $study)
    {
        $this->studies->add($study);

        return $this;
    }

    /**
     * @param Person        $person
     * @param EntityManager $entityManager
     *
     * @return boolean
     */
    public function canBook(Person $person, EntityManager $entityManager)
    {
        $academicYear = AcademicYear::getUniversityYear($entityManager);

        $studies = $entityManager
            ->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
            ->findAllByAcademicAndAcademicYear($person, $academicYear);

        $allowedStudies = $this->studies->toArray();
        foreach ($this->studies as $study)
            $allowedStudies = array_merge($allowedStudies, $study->getAllChildren());

        foreach ($studies as $study) {
            foreach ($allowedStudies as $allowedStudy) {
                if ($allowedStudy == $study->getStudy())
                    return true;
            }
        }

        return false;
    }
}
