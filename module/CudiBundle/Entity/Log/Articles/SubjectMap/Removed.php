<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace CudiBundle\Entity\Log\Articles\SubjectMap;

use CommonBundle\Entity\Users\Person,
    CudiBundle\Entity\Articles\SubjectMap,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Log\Articles\SubjectMap\Removed")
 * @ORM\Table(name="cudi.log_articles_subject_map_remove")
 */
class Removed extends \CudiBundle\Entity\Log
{
    /**
     * @param \CommonBundle\Entity\Users\Person $person
     * @param \CudiBundle\Entity\Articles\SubjectMap $subjectMap
     */
    public function __construct(Person $person, SubjectMap $subjectMap)
    {
        parent::__construct($person, $subjectMap->getId());
    }

    /**
     * @return \CudiBundle\Entity\Articles\SubjectMap
     */
    public function getSubjectMap(EntityManager $entityManager)
    {
        return $entityManager->getRepository('CudiBundle\Entity\Articles\SubjectMap')
            ->findOneById($this->getText());
    }
}
