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

namespace CudiBundle\Form\Admin\Stock\Deliveries;

use CommonBundle\Component\Form\Admin\Element\Hidden,
    CommonBundle\Component\Form\Admin\Element\Text,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Submit,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Add Delivery
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param string                      $barcodePrefix
     * @param null|string|int             $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $barcodePrefix = '', $name = null)
    {
        parent::__construct($name);

        $this->setAttribute('id', 'deliveryForm');

        $field = new Hidden('add_with_virtual_order');
        $field->setAttribute('id', 'addWithVirtualOrder');
        $this->add($field);

        $field = new Hidden('article_id');
        $field->setAttribute('id', 'articleId');
        $this->add($field);

        $field = new Text('article');
        $field->setLabel('Article')
            ->setAttribute('class', 'disableEnter')
            ->setAttribute('style', 'width: 400px;')
            ->setAttribute('id', 'articleSearch')
            ->setAttribute('autocomplete', 'off')
            ->setAttribute('data-provide', 'typeahead')
            ->setValue($barcodePrefix)
            ->setRequired();
        $this->add($field);

        $field = new Text('number');
        $field->setLabel('Number')
            ->setAttribute('autocomplete', 'off')
            ->setAttribute('id', 'delivery_number')
            ->setRequired();
        $this->add($field);

        $field = new Submit('add');
        $field->setValue('Add')
            ->setAttribute('class', 'stock_add')
            ->setAttribute('id', 'stock_add');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'article_id',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'int',
                        ),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'article',
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
