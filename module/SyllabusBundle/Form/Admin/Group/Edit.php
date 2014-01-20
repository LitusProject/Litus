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

namespace SyllabusBundle\Form\Admin\Group;

use Doctrine\ORM\EntityManager,
    SyllabusBundle\Component\Validator\Group\Name as NameValidator,
    SyllabusBundle\Entity\Group,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Edit Group
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends Add
{
    /**
     * @var \SyllabusBundle\Entity\Group
     */
    private $_group = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \SyllabusBundle\Entity\Group $group The group we're going to modify
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Group $group, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->_group = $group;

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'edit');
        $this->add($field);

        $this->populateFromGroup($group);
    }

    public function getInputFilter()
    {
        $inputFilter = parent::getInputFilter();
        $factory = new InputFactory();

        $inputFilter->remove('name');
        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'name',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new NameValidator($this->_entityManager, $this->_group),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
