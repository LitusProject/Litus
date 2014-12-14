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

namespace CommonBundle\Hydrator\General\Bank;

use CommonBundle\Entity\General\Bank\CashRegister as CashRegisterEntity;

class CashRegister extends \CommonBundle\Component\Hydrator\Hydrator
{
    protected function doExtract($object = null)
    {
        if (null === $object) {
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
        if (null === $object) {
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
