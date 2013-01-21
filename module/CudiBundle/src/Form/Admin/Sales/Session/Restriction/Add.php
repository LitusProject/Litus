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

namespace CudiBundle\Form\Admin\Sales\Session\Restriction;

use CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CudiBundle\Component\Validator\Sales\Session\Restriction\Exists as ExistsValidator,
    CudiBundle\Component\Validator\Sales\Session\Restriction\Values as ValuesValidator,
    CudiBundle\Entity\Sales\Session,
    CudiBundle\Entity\Sales\Session\Restriction,
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
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @var \CudiBundle\Entity\Sales\Session
     */
    private $_session;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \CudiBundle\Entity\Sales\Session $session
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Session $session, $name = null )
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;
        $this->_session = $session;

        $field = new Select('type');
        $field->setLabel('Type')
            ->setRequired()
            ->setAttribute('options', Restriction::$POSSIBLE_TYPES);
        $this->add($field);

        $field = new Text('start_value');
        $field->setLabel('Start Value')
            ->setRequired();
        $this->add($field);

        $field = new Text('end_value');
        $field->setLabel('End Value')
            ->setRequired();
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
                    'name'     => 'type',
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

        return $inputFilter;
    }
}
