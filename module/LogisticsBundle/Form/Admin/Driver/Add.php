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

namespace LogisticsBundle\Form\Admin\Driver;

use CommonBundle\Component\Form\Admin\Element\Hidden,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    LogisticsBundle\Component\Validator\Driver as DriverValidator,
    LogisticsBundle\Entity\Driver,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * The form used to add a new Driver
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @param EntityManager   $entityManager The EntityManager instance
     * @param null|string|int $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $years = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $yearnames = array();
        foreach ($years as $year) {
            $yearnames[$year->getId()] = $year->getCode();
        }

        $field = new Text('person_name');
        $field->setLabel('Name')
            ->setRequired(true)
            ->setAttribute('id', 'personSearch')
            ->setAttribute('autocomplete', 'off')
            ->setAttribute('data-provide', 'typeahead');
        $this->add($field);

        $field = new Hidden('person_id');
        $field->setAttribute('id', 'personId');
        $this->add($field);

        $field = new Text('color');
        $field->setLabel('Color')
            ->setAttribute('value', '#888888')
            ->setRequired(false);
        $this->add($field);

        $field = new Select('years');
        $field->setLabel('Years')
            ->setAttribute('multiple', true)
            ->setAttribute('options', $yearnames);
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'driver_add');
        $this->add($field);
    }

    public function populateFromDriver(Driver $driver)
    {
        $years = $driver->getYears();

        $yearids = array();
        foreach ($years as $year) {
            $yearids[] = $year->getId();
        }

        $formData = array(
            'color' => $driver->getColor(),
            'years' => $yearids,
        );

        $this->setData($formData);
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
                            new DriverValidator(
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
                            new DriverValidator(
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

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'color',
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'regex',
                            'options' => array(
                                'pattern' => '/^#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?$/',
                            ),
                        ),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
