<?php

namespace CudiBundle\Entity\Log\Article\SubjectMap;

use CommonBundle\Entity\User\Person;
use CudiBundle\Entity\Article\SubjectMap;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Log\Article\SubjectMap\Added")
 * @ORM\Table(name="cudi_log_articles_subjects_map_added")
 */
class Added extends \CudiBundle\Entity\Log
{
    /**
     * @param Person     $person
     * @param SubjectMap $subjectMap
     */
    public function __construct(Person $person, SubjectMap $subjectMap)
    {
        parent::__construct($person, $subjectMap->getId());
    }

    /**
     * @param  EntityManager $entityManager
     * @return SubjectMap
     */
    public function getSubjectMap(EntityManager $entityManager)
    {
        return $entityManager->getRepository('CudiBundle\Entity\Article\SubjectMap')
            ->findOneById($this->getText());
    }
}
