<?php

namespace CudiBundle\Component\Module;

use CommonBundle\Component\Util\AcademicYear;
use CommonBundle\Entity\General\AcademicYear as AcademicYearEntity;
use CommonBundle\Entity\General\Address;
use CommonBundle\Entity\General\Bank\BankDevice;
use CommonBundle\Entity\General\Bank\MoneyUnit;
use CommonBundle\Entity\General\Config;
use CudiBundle\Entity\Article\Option\Binding;
use CudiBundle\Entity\Article\Option\Color;
use CudiBundle\Entity\Sale\PayDesk;
use DateInterval;
use DateTime;

/**
 * CudiBundle installer
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Installer extends \CommonBundle\Component\Module\AbstractInstaller
{
    protected function postInstall()
    {
        $this->write('Installing addresses...');
        $this->installAddresses();
        $this->writeln(" <fg=green>\u{2713}</fg=green>", true);

        $this->write('Installing bindings...');
        $this->installBinding();
        $this->writeln(" <fg=green>\u{2713}</fg=green>", true);

        $this->write('Installing academic year...');
        $this->installAcademicYear();
        $this->writeln(" <fg=green>\u{2713}</fg=green>", true);

        $this->write('Installing Colors...');
        $this->installColor();
        $this->writeln(" <fg=green>\u{2713}</fg=green>", true);

        $this->write('Installing money units...');
        $this->installMoneyUnit();
        $this->writeln(" <fg=green>\u{2713}</fg=green>", true);

        $this->write('Installing bank devices...');
        $this->installBankDevice();
        $this->writeln(" <fg=green>\u{2713}</fg=green>", true);

        $this->write('Installing pay desks...');
        $this->installPayDesks();
        $this->writeln(" <fg=green>\u{2713}</fg=green>", true);
    }

    private function installBinding()
    {
        $bindings = array(
            'glued'   => 'Glued',
            'none'    => 'None',
            'stapled' => 'Stapled',
        );

        foreach ($bindings as $code => $name) {
            $binding = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Article\Option\Binding')
                ->findOneByCode($code);
            if ($binding == null) {
                $binding = new Binding($code, $name);
                $this->getEntityManager()->persist($binding);
            }
        }
        $this->getEntityManager()->flush();
    }

    private function installColor()
    {
        $colors = array('White');

        foreach ($colors as $item) {
            $color = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Article\Option\Color')
                ->findOneByName($item);
            if ($color == null) {
                $color = new Color($item);
                $this->getEntityManager()->persist($color);
            }
        }
        $this->getEntityManager()->flush();
    }

    private function installAcademicYear()
    {
        $now = new DateTime('now');
        $startAcademicYear = AcademicYear::getStartOfAcademicYear(
            $now
        );

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByUniversityStart($startAcademicYear);

        $organizationStart = str_replace(
            '{{ year }}',
            $startAcademicYear->format('Y'),
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('start_organization_year')
        );
        $organizationStart = new DateTime($organizationStart);

        if ($academicYear === null) {
            $academicYear = new AcademicYearEntity($organizationStart, $startAcademicYear);
            $this->getEntityManager()->persist($academicYear);
            $this->getEntityManager()->flush();
        }

        $organizationStart->add(
            new DateInterval('P1Y')
        );

        if ($organizationStart < new DateTime()) {
            $academicYear = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\AcademicYear')
                ->findOneByStart($organizationStart);
            if ($academicYear == null) {
                $startAcademicYear = AcademicYear::getEndOfAcademicYear(
                    $organizationStart
                );
                $academicYear = new AcademicYearEntity($organizationStart, $startAcademicYear);
                $this->getEntityManager()->persist($academicYear);
                $this->getEntityManager()->flush();
            }
        }
    }

    private function installAddresses()
    {
        try {
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.delivery_address');
        } catch (\Throwable $e) {
            $address = (new Address())
                ->setStreet('Kasteelpark Arenberg')
                ->setNumber(41)
                ->setMailbox(null)
                ->setPostal(3001)
                ->setCity('Heverlee')
                ->setCountry('BE');
            $this->getEntityManager()->persist($address);
            $config = new Config('cudi.delivery_address', (string) $address->getId());
            $config->setDescription('The delivery address of the cudi');
            $this->getEntityManager()->persist($config);
        }

        try {
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.billing_address');
        } catch (\Throwable $e) {
            $address = (new Address())
                ->setStreet('Studentenwijk Arenberg')
                ->setNumber(6)
                ->setMailbox('0')
                ->setPostal(3001)
                ->setCity('Heverlee')
                ->setCountry('BE');
            $this->getEntityManager()->persist($address);
            $config = new Config('cudi.billing_address', (string) $address->getId());
            $config->setDescription('The billing address of the cudi');
            $this->getEntityManager()->persist($config);
        }
    }

    private function installMoneyUnit()
    {
        $units = array(500, 200, 100, 50, 20, 10, 5, 2, 1, 0.50, 0.20, 0.10, 0.05, 0.02, 0.01);

        foreach ($units as $item) {
            $unit = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Bank\MoneyUnit')
                ->findOneByUnit($item);
            if ($unit == null) {
                $unit = new MoneyUnit($item);
                $this->getEntityManager()->persist($unit);
            }
        }
        $this->getEntityManager()->flush();
    }

    private function installBankDevice()
    {
        $bankdevices = array('Device 1', 'Device 2', 'CashFree');

        foreach ($bankdevices as $item) {
            $bankdevice = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Bank\BankDevice')
                ->findOneByName($item);
            if ($bankdevice == null) {
                $bankdevice = new BankDevice($item);
                $this->getEntityManager()->persist($bankdevice);
            }
        }
        $this->getEntityManager()->flush();
    }

    private function installPayDesks()
    {
        $paydesks = array(
            'paydesk_1' => '1',
            'paydesk_2' => '2',
            'paydesk_3' => '3',
        );

        foreach ($paydesks as $code => $name) {
            $paydesk = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\PayDesk')
                ->findOneByCode($code);
            if ($paydesk == null) {
                $paydesk = new PayDesk($code, $name);
                $this->getEntityManager()->persist($paydesk);
            }
        }
        $this->getEntityManager()->flush();
    }
}
