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

namespace SyllabusBundle\Form\Admin\Subject\Study;

use Doctrine\ORM\EntityManager,
    SyllabusBundle\Entity\Subject,
    SyllabusBundle\Entity\StudySubjectMap,
    Zend\Form\Element\Submit;

/**
 * Edit Subject
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends Add
{
    /**
     * @param EntityManager   $entityManager The EntityManager instance
     * @param StudySubjectMap $mapping
     * @param null|string|int $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, StudySubjectMap $mapping, $name = null)
    {
        parent::__construct($entityManager, $mapping->getSubject(), $mapping->getAcademicYear(), $name);

        $this->remove('study_id');
        $this->remove('study');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'edit');
        $this->add($field);

        $this->populateFromMapping($mapping);
    }

    public function getInputFilter()
    {
        $inputFilter = parent::getInputFilter();

        $inputFilter->remove('study_id');
        $inputFilter->remove('study');

        return $inputFilter;
    }
}
