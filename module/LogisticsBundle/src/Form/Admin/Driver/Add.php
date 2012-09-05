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
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace LogisticsBundle\Form\Admin\Driver;

use CommonBundle\Component\Form\Admin\Element\Hidden,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    LogisticsBundle\Component\Validator\Driver,
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
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;
        
        $years = $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();
        
        $yearnames = array();
        foreach($years as $year) {
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

    public function getInputFilter()
    {
        if ($this->_inputFilter == null) {

            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            if ('' == $this->data['person_id']) {
                $inputFilter->add(
                    $factory->createInput(
                        array(
                            'name' => 'person_name',
                            'required' => true,
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                            'validators' => array(
                                new Driver(
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
                                new Driver(
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
            
            $this->_inputFilter = $inputFilter;
        }

        return $this->_inputFilter;
    }
}
