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

namespace CudiBundle\Form\Prof\Article;

use SyllabusBundle\Entity\Subject,
    Doctrine\ORM\EntityManager,
    CommonBundle\Component\Form\Bootstrap\Element\Submit;

/**
 * Add With Subject
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class AddWithSubject extends Add
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \CudiBundle\Entity\Article  $article
     * @param null|string|int             $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Subject $subject, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->get('subject')->get('subject_id')->setAttribute('value', $subject->getId());
        $this->get('subject')->get('subject')->setAttribute('value', $subject->getCode() . ' - ' . $subject->getName())
            ->setAttribute('disabled', 'disabled');
    }

    public function getInputFilter()
    {
        $inputFilter = parent::getInputFilter();

        $inputFilter->remove('subject');

        return $inputFilter;
    }
}
