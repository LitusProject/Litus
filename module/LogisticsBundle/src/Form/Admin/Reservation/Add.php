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

namespace LogisticsBundle\Form\Admin\Reservation;



use CommonBundle\Component\Form\Admin\Element\Collection,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit,
    CommonBundle\Entity\General\AcademicYear,
    LogisticsBundle\Component\Validator\Driver,
    CalendarBundle\Component\Validator\DateCompare as DateCompareValidator;

/**
 * The form used to add a new Reservation.
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
    public function __construct(EntityManager $entityManager, AcademicYear $currentYear, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;
        
        $drivers = $this->_entityManager
            ->getRepository('LogisticsBundle\Entity\Driver')
            ->findAllByYear($currentYear);
        
        $drivernames = array();
        // Add the possibility to select no driver (yet)
        $drivernames[-1] = 'Unspecified';
        foreach($drivers as $driver) {
            $drivernames[$driver->getPerson()->getId()] = $driver->getPerson()->getFullName(); 
        }

        $field = new Text('start_date');
        $field->setLabel('Start Date')
            ->setRequired();
        $this->add($field);
        
        $field = new Text('end_date');
        $field->setLabel('End Date')
            ->setRequired();
        $this->add($field);
        
        $field = new Text('reason');
        $field->setLabel('Reason')
            ->setRequired();
        $this->add($field);
        
        $field = new Textarea('additional_info');
        $field->setLabel('Additional Information');
        $this->add($field);
        
        $field = new Select('driver');
        $field->setLabel('Driver')
            ->setRequired(false)
            ->setAttribute('options', $drivernames);
        $this->add($field);
        
        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'reservation_add');
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
                        'name'     => 'reason',
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
                        'name'     => 'additional_info',
                        'required' => false,
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
