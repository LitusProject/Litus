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

namespace BrBundle\Form\Cv;

use CommonBundle\Component\Form\Bootstrap\Element\Submit,
    CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\General\Language,
    CommonBundle\Entity\User\Person\Academic,
    Doctrine\ORM\EntityManager;

/**
 * Edit Cv
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Edit extends Add
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int             $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Academic $academic, AcademicYear $year, Language $language, $name = null)
    {
        parent::__construct($entityManager, $academic, $year, $language, $name);

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save Changes')
            ->setAttribute('class', 'btn btn-primary');
        $this->add($field);
    }

}
