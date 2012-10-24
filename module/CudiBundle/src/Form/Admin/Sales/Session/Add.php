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

namespace CudiBundle\Form\Admin\Sales\Session;

use CommonBundle\Entity\General\Bank\CashRegister,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Validator\Price as PriceValidator,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Submit,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Add Sale Session content
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
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
    public function __construct(EntityManager $entityManager, $name = null )
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $units = $this->_getUnits();

        foreach($units as $unit) {
            $field = new Text('unit_' . $unit->getId());
            $field->setLabel('&euro; ' . number_format($unit->getUnit() / 100, 2))
                ->setAttribute('autocomplete', 'off')
                ->setRequired()
                ->setValue(0);
            $this->add($field);
        }

        $devices = $this->_getDevices();

        foreach($devices as $device) {
            $field = new Text('device_' . $device->getId());
            $field->setLabel($device->getName())
                ->setAttribute('autocomplete', 'off')
                ->setRequired()
                ->setValue(0);
            $this->add($field);
        }

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'sale_add');
        $this->add($field);
    }

    private function _getUnits()
    {
        return $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Bank\MoneyUnit')
            ->findAll();
    }

    private function _getDevices()
    {
        return $this->_entityManager
            ->getRepository('CommonBundle\Entity\General\Bank\BankDevice')
            ->findAll();
    }

    public function populateFromCashRegister(CashRegister $cashRegister)
    {
        $data = array();
        foreach($cashRegister->getBankDeviceAmounts() as $amount)
            $data['device_' . $amount->getDevice()->getId()] = $amount->getAmount() / 100;
        foreach($cashRegister->getMoneyUnitAmounts() as $amount)
            $data['unit_' . $amount->getUnit()->getId()] = $amount->getAmount();

        $this->setData($data);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $units = $this->_getUnits();
        foreach($units as $unit) {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'unit_' . $unit->getId(),
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
        }

        $devices = $this->_getDevices();
        foreach($devices as $device) {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'device_' . $device->getId(),
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
        }

        return $inputFilter;
    }
}
