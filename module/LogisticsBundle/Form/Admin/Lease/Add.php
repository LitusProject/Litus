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
namespace LogisticsBundle\Form\Admin\Lease;

use CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
    Doctrine\ORM\EntityManager,
    LogisticsBundle\Entity\Lease\Item,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add a new lease
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param null|string|int $name Optional name for the form
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $field = new Text('name');
        $field->setLabel('Name')
            ->setRequired();
        $this->add($field);

        $field = new Text('barcode');
        $field->setLabel('Barcode')
                ->setRequired();
        $this->add($field);

        $field = new Textarea('additional_info');
        $field->setLabel('Additional information');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'add');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'name',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

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
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'additional_info',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        return $inputFilter;
    }

    /**
     * Populates the form with values from the entity
     *
     * @param \LogisticsBundle\Entity\Lease\Item $lease
     */
    public function populateFromLease(Item $lease)
    {
        $data = array(
            'name'            => $lease->getName(),
            'barcode'         => $lease->getBarcode(),
            'additional_info' => $lease->getAdditionalInfo(),
        );

        $this->setData($data);
    }


}
