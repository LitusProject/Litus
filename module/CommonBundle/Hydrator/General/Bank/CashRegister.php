<?php

namespace CommonBundle\Hydrator\General\Bank;

use CommonBundle\Entity\General\Bank\CashRegister as CashRegisterEntity;

class CashRegister extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doExtract($object = null)
    {
        if ($object === null) {
            return array();
        }

        $data = array(
            'device' => array(),
            'unit'   => array(),
        );

        foreach ($object->getBankDeviceAmounts() as $amount) {
            $data['device'][$amount->getDevice()->getId()] = $amount->getAmount() / 100;
        }

        foreach ($object->getMoneyUnitAmounts() as $amount) {
            $data['unit'][$amount->getUnit()->getId()] = $amount->getAmount();
        }

        return $data;
    }

    protected function doHydrate(array $data, $object = null)
    {
        if ($object === null) {
            $object = new CashRegisterEntity();
        }

        if (isset($data['device'])) {
            $deviceData = $data['device'];

            $devices = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Bank\BankDevice')
                ->findAll();

            foreach ($devices as $device) {
                if (isset($deviceData[$device->getId()])) {
                    $object->setAmountForDevice($device, $deviceData[$device->getId()]);
                } else {
                    $object->setAmountForDevice($device, 0);
                }
            }
        }

        if (isset($data['unit'])) {
            $unitData = $data['unit'];

            $units = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Bank\MoneyUnit')
                ->findAll();

            foreach ($units as $unit) {
                if (isset($unitData[$unit->getId()])) {
                    $object->setAmountForUnit($unit, $unitData[$unit->getId()]);
                } else {
                    $object->setAmountForUnit($unit, 0);
                }
            }
        }

        return $object;
    }
}
