<?php

namespace QuizBundle\Form\Admin\Round;

use CommonBundle\Component\Form\Admin\Element\Hidden,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
    Doctrine\ORM\EntityManager,
    QuizBundle\Entity\Quiz,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add a new quiz
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @var \QuizBundle\Entity\Quiz $quiz The quiz the round will be added to
     * @var null|string|int $name Optional name for the form
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;


        $field = new Text('name');
        $field->setLabel('Name');
        $field->setRequired();
        $this->add($field);

        $field = new Text('max_points');
        $field->setLabel('Maximum points');
        $field->setRequired();
        $this->add($field);

        $field = new Text('order');
        $field->setLabel('Round number');
        $field->setRequired();
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'quiz_round_add');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();


        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'name',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'max_points',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'int'),
                    )
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'order',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'int'),
                    )
                )
            )
        );

        return $inputFilter;
    }
}
