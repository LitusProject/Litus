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

namespace BrBundle\Form\Admin\Company\Vacancy;

use BrBundle\Entity\Company\Vacancy,
    CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Submit;

/**
 * Edit an vacancy.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends Add
{
    /**
     * @param \BrBundle\Entity\Company\Vacancy $vacancy
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(Vacancy $vacancy, $name = null)
    {
        parent::__construct($name);

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Edit')
            ->setAttribute('class', 'companies_edit');
        $this->add($field);

        $this->populateFromVacancy($vacancy);
    }
}
