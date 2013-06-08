<?php

namespace QuizBundle\Form\Admin\Quiz;

use CommonBundle\Component\Form\Admin\Element\Text,
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
     * @param null|string|int $name Optional name for the form
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $field = new Text('name');
        $field->setLabel('Name')
            ->setRequired();
        $this->add($field);

        // XXX: Edit role: In form?

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'quiz_add');
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

        return $inputFilter;
    }

    /**
     * Populates the form with values from the entity
     *
     * @param \QuizBundle\Entity\Quiz $quiz
     */
    public function populateFromQuiz(Quiz $quiz)
    {
        $data = array(
            'name' => $quiz->getName()
        );

        $this->setData($data);
    }
}
