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

namespace LogisticsBundle\Form\Lease;

use CommonBundle\Component\Form\Bootstrap\Element\Hidden,
    CommonBundle\Component\Form\Bootstrap\Element\Text,
    CommonBundle\Component\Form\Bootstrap\Element\Textarea,
    CommonBundle\Component\Validator\Price as PriceValidator,
    LogisticsBundle\Component\Validator\LeaseValidator,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * The form used to register a returned item.
 *
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class AddReturn extends \CommonBundle\Component\Form\Bootstrap\Form
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

        $field = new Text('item');
        $field->setLabel('Item')
            ->setAttribute('class', 'js-item-search')
            ->setAttribute('autocomplete', false);
        $this->add($field);

        $field = new Hidden('barcode');
        $field->setAttribute('class', 'js-item-barcode');
        $this->add($field);

        $field = new Text('returned_by');
        $field->setLabel('Returned by')
                ->setAttribute('autocomplete', false)
                ->setRequired();
        $this->add($field);

        $field = new Text('returned_pawn');
        $field->setLabel('Resitution pawn')
                ->setRequired();
        $this->add($field);

        $field = new Textarea('comment');
        $field->setLabel('Comment')
                ->setAttribute('class', 'input-xlarge')
                ->setAttribute('rows', 3);
        $this->add($field);

        $field = new Submit('return');
        $field->setValue('Return')
            ->setAttribute('class', 'btn btn-primary');
        $this->add($field);

    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'barcode',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'barcode',
                            'options' => array(
                                'adapter'     => 'Ean12',
                                'useChecksum' => false,
                            ),
                        ),
                        new LeaseValidator($this->_entityManager, true),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'returned_by',
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
                    'name'     => 'returned_pawn',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new PriceValidator(),
                    ),
                )
            )
        );

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
