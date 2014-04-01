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

namespace CudiBundle\Form\Admin\Stock\Orders;

use CommonBundle\Component\Form\Admin\Element\Text,
    CudiBundle\Entity\Stock\Order\Item,
    Zend\Form\Element\Submit,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Edit Order
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @param Item            $item
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(Item $item, $name = null)
    {
        parent::__construct($name);

        $field = new Text('number');
        $field->setLabel('Number')
            ->setAttribute('autocomplete', 'off')
            ->setValue($item->getNumber())
            ->setRequired();
        $this->add($field);

        $field = new Submit('edit');
        $field->setValue('Save')
            ->setAttribute('class', 'stock_edit');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'number',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'int',
                        ),
                        array(
                            'name' => 'greaterthan',
                            'options' => array(
                                'min' => 0,
                            ),
                        ),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
