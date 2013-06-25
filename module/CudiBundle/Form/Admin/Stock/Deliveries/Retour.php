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

namespace CudiBundle\Form\Admin\Stock\Deliveries;

use CommonBundle\Component\Form\Admin\Element\Textarea,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Submit,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Return to supplier (inverse of delivery)
 *
 * (named so because php complains when 'Return' is used)
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Retour extends \CudiBundle\Form\Admin\Stock\Deliveries\Add
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param string $barcodePrefix
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $barcodePrefix = '', $name = null)
    {
        parent::__construct($entityManager, $barcodePrefix, $name);

        $this->remove('submit');

        $field = new Textarea('comment');
        $field->setLabel('Comment')
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
        $inputFilter = parent::getInputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'comment',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}

