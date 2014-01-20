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

namespace CudiBundle\Form\Admin\Stock;

use CommonBundle\Component\Form\Admin\Element\Radio,
    CommonBundle\Component\Form\Admin\Element\Checkbox,
    Zend\Form\Element\Submit,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Stock Select options
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SelectOptions extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @param null|string|int $name Optional name for the element
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $field = new Radio('articles');
        $field->setLabel('Articles')
            ->setAttribute('options', array('all' => 'All', 'internal' => 'Internal', 'external' => 'External'))
            ->setValue('all')
            ->setRequired();
        $this->add($field);

        $field = new Radio('order');
        $field->setLabel('Order')
            ->setAttribute('options', array('barcode' => 'Barcode', 'title' => 'Title'))
            ->setValue('barcode')
            ->setRequired();
        $this->add($field);

        $field = new Checkbox('in_stock');
        $field->setLabel('Only In Stock');
        $this->add($field);

        $field = new Submit('select');
        $field->setValue('Select')
            ->setAttribute('class', 'view');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'articles',
                    'required' => true,
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'order',
                    'required' => true,
                )
            )
        );

        return $inputFilter;
    }
}
