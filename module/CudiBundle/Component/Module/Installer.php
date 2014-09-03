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

namespace CudiBundle\Component\Module;

use CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\General\Address,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    CommonBundle\Entity\General\Bank\BankDevice,
    CommonBundle\Entity\General\Bank\MoneyUnit,
    CommonBundle\Entity\General\Config,
    CudiBundle\Entity\Article\Option\Binding,
    CudiBundle\Entity\Article\Option\Color,
    CudiBundle\Entity\Sale\PayDesk,
    DateInterval,
    DateTime,
    Exception;

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
        $this->write('Installing Addresses...');
        $this->installAddresses();
        $this->writeln(' done.', true);

        $this->write('Installing Bindings...');
        $this->installBinding();
        $this->writeln(' done.', true);

        $this->write('Installing Academic Years...');
        $this->installAcademicYear();
        $this->writeln(' done.', true);

        $this->write('Installing Colors...');
        $this->installColor();
        $this->writeln(' done.', true);

        $this->write('Installing Money Units...');
        $this->installMoneyUnit();
        $this->writeln(' done.', true);

        $this->write('Installing Bank Devices...');
        $this->installBankDevice();
        $this->writeln(' done.', true);

        $this->write('Installing Pay Desks...');
        $this->installPayDesks();
        $this->writeln(' done.', true);
    }

    private function installBinding()
    {
        $bindings = array(
            'glued' => 'Glued',
            'none' => 'None',
            'stapled' => 'Stapled',
        );

        foreach ($bindings as $code => $name) {
            $binding = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Article\Option\Binding')
                ->findOneByCode($code);
            if (null == $binding) {
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
            if (null == $color) {
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

        if (null === $academicYear) {
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
            if (null == $academicYear) {
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
        } catch (Exception $e) {
            $address = new Address(
                'Kasteelpark Arenberg',
                41,
                null,
                3001,
                'Heverlee',
                'BE'
            );
            $this->getEntityManager()->persist($address);
            $config = new Config('cudi.delivery_address', (string) $address->getId());
            $config->setDescription('The delivery address of the cudi');
            $this->getEntityManager()->persist($config);
        }

        try {
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('cudi.billing_address');
        } catch (Exception $e) {
            $address = new Address(
                'Studentenwijk Arenberg',
                '6',
                '0',
                3001,
                'Heverlee',
                'BE'
            );
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
            if (null == $unit) {
                $unit = new MoneyUnit($item);
                $this->getEntityManager()->persist($unit);
            }
        }
        $this->getEntityManager()->flush();
    }

    private function installBankDevice()
    {
        $bankdevices = array('Device 1', 'Device 2');

        foreach ($bankdevices as $item) {
            $bankdevice = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Bank\BankDevice')
                ->findOneByName($item);
            if (null == $bankdevice) {
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
            if (null == $paydesk) {
                $paydesk = new PayDesk($code, $name);
                $this->getEntityManager()->persist($paydesk);
            }
        }
        $this->getEntityManager()->flush();
    }
}
