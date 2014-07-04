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

namespace CudiBundle\Form\Admin\Sales\Session\Restriction;

use CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CudiBundle\Component\Validator\Sales\Session\Restriction\Exists as ExistsValidator,
    CudiBundle\Component\Validator\Sales\Session\Restriction\Values as ValuesValidator,
    CudiBundle\Entity\Sale\Session,
    CudiBundle\Entity\Sale\Session\Restriction,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Submit,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Add Sale Session content
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @var Session
     */
    private $_session;

    /**
     * @param EntityManager   $entityManager The EntityManager instance
     * @param Session         $session
     * @param null|string|int $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Session $session, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;
        $this->_session = $session;

        $field = new Select('restriction_type');
        $field->setLabel('Type')
            ->setRequired()
            ->setAttribute('options', Restriction::$POSSIBLE_TYPES)
            ->setAttribute('data-help', 'Limit the students that can buy articles during this sale session:
                <ul>
                    <li><b>Name:</b> restrict by name</li>
                    <li><b>Year:</b> restrict study year</li>
                </ul>');
        $this->add($field);

        $field = new Text('start_value');
        $field->setLabel('Start Value')
            ->setRequired();
        $this->add($field);

        $field = new Text('end_value');
        $field->setLabel('End Value')
            ->setRequired();
        $this->add($field);

        $field = new Select('start_value_year');
        $field->setLabel('Start Value')
            ->setRequired()
            ->setAttribute('options', Restriction::$POSSIBLE_YEARS);
        $this->add($field);

        $field = new Select('end_value_year');
        $field->setLabel('End Value')
            ->setRequired()
            ->setAttribute('options', Restriction::$POSSIBLE_YEARS);
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'add');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'restriction_type',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new ExistsValidator($this->_entityManager, $this->_session)
                    ),
                )
            )
        );

        if (isset($this->data['restriction_type']) && $this->data['restriction_type'] == 'year') {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'start_value_year',
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
                        'name'     => 'end_value_year',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            new ValuesValidator('start_value_year')
                        ),
                    )
                )
            );
        } else {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'start_value',
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
                        'name'     => 'end_value',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            new ValuesValidator('start_value')
                        ),
                    )
                )
            );
        }

        return $inputFilter;
    }
}
