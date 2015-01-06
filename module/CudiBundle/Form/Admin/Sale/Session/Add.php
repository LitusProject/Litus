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

        foreach ($units as $unit) {
            $unitElements[] = array(
                'type'       => 'text',
                'name'       => $unit->getId(),
                'label'      => '&euro; ' . number_format($unit->getUnit() / 100, 2),
                'required'   => true,
                'value'      => 0,
                'attributes' => array(
                    'autocomplete' => 'off',
                    'class'        => 'moneyunit',
                    'data-value'   => $unit->getUnit(),
                ),
                'options'    => array(
                    'input' => array(
                        'filters'  => array(
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

        $this->add(array(
            'type'     => 'fieldset',
            'name'     => 'unit',
            'elements' => $unitElements,
        ));

        $devices = $this->getDevices();
        $deviceElements = array();

        foreach ($devices as $device) {
            $deviceElements[] = array(
                'type'       => 'text',
                'name'       => $device->getId(),
                'label'      => $device->getName(),
                'required'   => true,
                'value'      => 0,
                'attributes' => array(
                    'autocomplete' => 'off',
                    'class'        => 'device',
                ),
                'options'    => array(
                    'input' => array(
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'price'),
                        ),
                    ),
                ),
            );
        }

        $this->add(array(
            'type'     => 'fieldset',
            'name'     => 'device',
            'elements' => $deviceElements,
        ));

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
}
