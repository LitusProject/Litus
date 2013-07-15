<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace CudiBundle\Form\Admin\Stock\Orders;

use CommonBundle\Component\Form\Admin\Element\Textarea,
    CudiBundle\Entity\Stock\Order\Order,
    Zend\Form\Element\Submit,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Add Order Comment
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Comment extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @param \CudiBundle\Entity\Stock\Order\Order $order
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(Order $order, $name = null)
    {
        parent::__construct($name);

        $field = new Textarea('comment');
        $field->setLabel('Comment')
            ->setAttribute('style', 'height: 50px')
            ->setValue($order->getComment())
            ->setRequired();
        $this->add($field);

        $field = new Submit('save');
        $field->setValue('Save')
            ->setAttribute('class', 'edit');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'comment',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
