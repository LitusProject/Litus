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

namespace CommonBundle\Form\Admin\Unit;

use CommonBundle\Component\OldForm\Admin\Element\Checkbox,
    CommonBundle\Component\OldForm\Admin\Element\Hidden,
    CommonBundle\Component\OldForm\Admin\Element\Text,
    CommonBundle\Component\Validator\Academic as AcademicValidator,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * The form used to add a member to a unit.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Member extends \CommonBundle\Component\OldForm\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int             $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $field = new Text('person_name');
        $field->setLabel('Name')
            ->setRequired(true)
            ->setAttribute('id', 'personSearch')
            ->setAttribute('autocomplete', 'off')
            ->setAttribute('data-provide', 'typeahead');
        $this->add($field);

        $field = new Checkbox('coordinator');
        $field->setLabel('Coordinator');
        $this->add($field);

        $field = new Hidden('person_id');
        $field->setAttribute('id', 'personId');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'unit_member_add');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        if (!isset($this->data['person_id']) || '' == $this->data['person_id']) {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name' => 'person_name',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            new AcademicValidator(
                                $this->_entityManager,
                                array(
                                    'byId' => false,
                                )
                            )
                        ),
                    )
                )
            );
        } else {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name' => 'person_id',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            new AcademicValidator(
                                $this->_entityManager,
                                array(
                                    'byId' => true,
                                )
                            )
                        ),
                    )
                )
            );
        }

        return $inputFilter;
    }
}
