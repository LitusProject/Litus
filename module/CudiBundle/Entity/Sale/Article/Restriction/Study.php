<?php

namespace CudiBundle\Entity\Sale\Article\Restriction;

use CommonBundle\Component\Util\AcademicYear;
use CommonBundle\Entity\User\Person;
use CudiBundle\Entity\Sale\Article;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use SyllabusBundle\Entity\Study as StudyEntity;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Sale\Article\Restriction\Study")
 * @ORM\Table(name="cudi_sale_articles_restrictions_study")
 */
class Study extends \CudiBundle\Entity\Sale\Article\Restriction
{
    /**
     * @var ArrayCollection The value of the restriction
     *
     * @ORM\ManyToMany(targetEntity="SyllabusBundle\Entity\Study")
     * @ORM\JoinTable(name="cudi_sale_articles_restrictions_studies_map",
     *     joinColumns={@ORM\JoinColumn(name="restriction", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="study", referencedColumnName="id", onDelete="CASCADE")}
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
        foreach ($this->studies as $study) {
            $value .= 'Phase ' . $study->getPhase() . ' - ' . $study->getTitle() . ' ; ';
        }

        return $value;
    }

    /**
     * @return ArrayCollection
     */
    public function getStudies()
    {
        return $this->studies;
    }

    /**
     * @param  StudyEntity $study
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
            ->getRepository('SecretaryBundle\Entity\Syllabus\Enrollment\Study')
            ->findAllByAcademicAndAcademicYear($person, $academicYear);

        $allowedStudies = $this->studies->toArray();

        foreach ($studies as $study) {
            foreach ($allowedStudies as $allowedStudy) {
                if ($allowedStudy == $study->getStudy()) {
                    return true;
                }
            }
        }

        return false;
    }
}
