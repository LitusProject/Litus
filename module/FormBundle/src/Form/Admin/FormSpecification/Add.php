<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace FormBundle\Form\Admin\FormSpecification;

use CommonBundle\Component\Form\Admin\Element\Checkbox,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
    CommonBundle\Component\Validator\DateCompare as DateCompareValidator,
    Doctrine\ORM\EntityManager,
    FormBundle\Entity\Nodes\FormSpecification,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add FormSpecification
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $field = new Text('title');
        $field->setLabel('Title')
            ->setRequired(true);
        $this->add($field);

        $field = new TextArea('introduction');
        $field->setLabel('Introduction')
            ->setAttribute('rows', 20)
            ->setRequired(true);
        $this->add($field);

        $field = new Text('submittext');
        $field->setLabel('Submit Button Text')
            ->setRequired(true);
        $this->add($field);

        $field = new Text('start_date');
        $field->setLabel('Start Date')
            ->setRequired();
        $this->add($field);

        $field = new Text('end_date');
        $field->setLabel('End Date')
            ->setRequired();
        $this->add($field);

        $field = new Checkbox('active');
        $field->setLabel('Active');
        $this->add($field);

        $field = new Text('max');
        $field->setLabel('Total max entries');
        $this->add($field);

        $field = new Checkbox('multiple');
        $field->setLabel('Multiple entries / person allowed');
        $this->add($field);

        $field = new Checkbox('redoable');
        $field->setLabel('Entries can be overwritten');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'form_add');
        $this->add($field);
    }

    public function getInputFilter()
    {
        if ($this->_inputFilter == null) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'start_date',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'date',
                                'options' => array(
                                    'format' => 'd/m/Y H:i',
                                ),
                            ),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'end_date',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'date',
                                'options' => array(
                                    'format' => 'd/m/Y H:i',
                                ),
                            ),
                            new DateCompareValidator('start_date', 'd/m/Y H:i'),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'max',
                        'required' => false,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'digits',
                            ),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'title',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'submittext',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );

            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
}
