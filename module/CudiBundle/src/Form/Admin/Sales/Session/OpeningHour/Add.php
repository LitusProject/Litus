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

namespace CudiBundle\Form\Admin\Sales\Session\OpeningHour;

use CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Validator\DateCompare as DateCompareValidator,
    CudiBundle\Entity\Sales\Session\OpeningHour,
    Zend\Form\Element\Submit,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Add opening hour
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @param null|string|int $name Optional name for the element
     */
    public function __construct($name = null )
    {
        parent::__construct($name);

        $field = new Text('start');
        $field->setLabel('Start')
            ->setRequired()
            ->setAttribute('placeholder', 'dd/mm/yyyy hh:mm');
        $this->add($field);

        $field = new Text('end');
        $field->setLabel('End')
            ->setRequired()
            ->setAttribute('placeholder', 'dd/mm/yyyy hh:mm');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'clock_add');
        $this->add($field);
    }

    public function populateFromOpeningHour(OpeningHour $openingHour)
    {
        $this->setData(
            array(
                'start' => $openingHour->getStart()->format('d/m/Y H:i'),
                'end' => $openingHour->getEnd()->format('d/m/Y H:i'),
            )
        );
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'start',
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
                    'name'     => 'end',
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
                        new DateCompareValidator('start', 'd/m/Y H:i'),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
