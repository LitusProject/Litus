<?php

namespace CudiBundle\Form\Admin\Sale\Session;

use CommonBundle\Entity\General\Bank\CashRegister;

/**
 * Add Sale Session content
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'CommonBundle\Hydrator\General\Bank\CashRegister';

    /**
     * @var CashRegister|null
     */
    protected $cashRegister = null;

    public function init()
    {
        parent::init();

        $units = $this->getUnits();
        $unitElements = array();
        $lastSession = $this->getLastSalesSession();
        $amounts = $lastSession->getCloseRegister()->getMoneyUnitAmounts();

        for ($i = 0; $i < sizeof($units); $i++){
            $unit = $units[$i];
            $amount = $amounts[$i]->getAmount();

            $unitElements[] = array(
                'type'       => 'text',
                'name'       => $unit->getId(),
                'label'      => '&euro; ' . number_format($unit->getUnit() / 100, 2),
                'required'   => true,
                'value'      => $amount,//todo deze aanpassen naar getal tussen haakjes
                'attributes' => array(
                    'id'           => 'unit_' . $unit->getId(),
                    'autocomplete' => 'off',
                    'class'        => 'moneyunit',
                    'data-value'   => $unit->getUnit(),
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'int',
                            ),
                        ),
                    ),
                ),
            );
        }

        /*foreach ($units as $unit) {
            $unitElements[] = array(
                'type'       => 'text',
                'name'       => $unit->getId(),
                'label'      => '&euro; ' . number_format($unit->getUnit() / 100, 2),
                'required'   => true,
                'value'      => 0,//todo deze aanpassen naar getal tussen haakjes
                'attributes' => array(
                    'id'           => 'unit_' . $unit->getId(),
                    'autocomplete' => 'off',
                    'class'        => 'moneyunit',
                    'data-value'   => $unit->getUnit(),
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'int',
                            ),
                        ),
                    ),
                ),
            );
        }*/
        $this->add(
            array(
                'type'     => 'fieldset',
                'name'     => 'unit',
                'elements' => $unitElements,
            )
        );

        $devices = $this->getDevices();
        $deviceElements = array();
        $amounts = $lastSession->getCloseRegister()->getBankDeviceAmounts();

        for ($i = 0; $i < sizeof($devices); $i++){
            $device = $devices[$i];
            $amount = (($amounts[$i]->getAmount())/100);

            $deviceElements[] = array(
                'type'       => 'text',
                'name'       => $device->getId(),
                'label'      => $device->getName(),
                'required'   => true,
                'value'      => $amount,//todo deze aanpassen naar getal tussen haakjes
                'attributes' => array(
                    'id'           => 'device_' . $device->getId(),
                    'autocomplete' => 'off',
                    'class'        => 'device',
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Price'),
                        ),
                    ),
                ),
            );
        }
        /*foreach ($devices as $device) {
            $deviceElements[] = array(
                'type'       => 'text',
                'name'       => $device->getId(),
                'label'      => $device->getName(),
                'required'   => true,
                'value'      => 0,//todo deze aanpassen naar getal tussen haakjes
                'attributes' => array(
                    'id'           => 'device_' . $device->getId(),
                    'autocomplete' => 'off',
                    'class'        => 'device',
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Price'),
                        ),
                    ),
                ),
            );
        }*/

        $this->add(
            array(
                'type'     => 'fieldset',
                'name'     => 'device',
                'elements' => $deviceElements,
            )
        );

        $this->addSubmit('Add', 'sale_add');
    }

    /**
     * @param  CashRegister $cashRegister
     * @return self
     */
    public function setCashRegister(CashRegister $cashRegister)
    {
        $this->cashRegister = $cashRegister;

        return $this;
    }

    private function getUnits()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Bank\MoneyUnit')
            ->findAll();
    }

    private function getDevices()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Bank\BankDevice')
            ->findAll();
    }

    private function getLastSalesSession()
    {
        return $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session')
            ->getLast();
    }
}
