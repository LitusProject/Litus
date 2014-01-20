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

namespace CudiBundle\Entity\Log\Article\SubjectMap;

use CommonBundle\Entity\User\Person,
    CudiBundle\Entity\Article\SubjectMap,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CudiBundle\Repository\Log\Article\SubjectMap\Removed")
 * @ORM\Table(name="cudi.log_articles_subject_map_remove")
 */
class Removed extends \CudiBundle\Entity\Log
{
    /**
     * @param \CommonBundle\Entity\User\Person $person
     * @param \CudiBundle\Entity\Article\SubjectMap $subjectMap
     */
    public function __construct(Person $person, SubjectMap $subjectMap)
    {
        parent::__construct($person, $subjectMap->getId());
    }

    /**
     * @return \CudiBundle\Entity\Article\SubjectMap
     */
    public function getSubjectMap(EntityManager $entityManager)
    {
        return $entityManager->getRepository('CudiBundle\Entity\Article\SubjectMap')
            ->findOneById($this->getText());
    }
}
