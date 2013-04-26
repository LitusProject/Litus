<?php

namespace QuizBundle\Form\Admin\Team;

use CommonBundle\Component\Form\Admin\Element\Hidden,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
    Doctrine\ORM\EntityManager,
    QuizBundle\Entity\Team,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add a new team
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

        $field = new Text('number');
        $field->setLabel('Team number');
        $field->setRequired();
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'quiz_team_add');
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
                    'name' => 'number',
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

    /**
     * Populates the form with values from the entity
     *
     * @param \QuizBundle\Entity\Team $team
     */
    public function populateFromTeam(Team $team)
    {
        $data = array(
            'name' => $team->getName(),
            'number' => $team->getNumber(),
        );

        $this->setData($data);
    }
}
