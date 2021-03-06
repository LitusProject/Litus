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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\Module;

use CommonBundle\Entity\General\Address\City;
use CommonBundle\Entity\General\Address\Street;
use CommonBundle\Entity\General\Language;
use CommonBundle\Entity\General\Organization;

/**
 * CommonBundle Installer
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Installer extends \CommonBundle\Component\Module\AbstractInstaller
{
    protected function preInstall()
    {
        $this->write('Installing languages...');
        $this->installLanguages();
        $this->writeln(" <fg=green>\u{2713}</fg=green>", true);

        $this->write('Installing streets...');
        $this->installStreets();
        $this->writeln(" <fg=green>\u{2713}</fg=green>", true);

        $this->write('Installing organizations...');
        $this->installOrganizations();
        $this->writeln(" <fg=green>\u{2713}</fg=green>", true);
    }

    private function installLanguages()
    {
        $languages = array(
            'en' => 'English',
            'nl' => 'Nederlands',
        );

        foreach ($languages as $abbrev => $name) {
            $language = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev($abbrev);

            if ($language === null) {
                $language = new Language($abbrev, $name);
                $this->getEntityManager()->persist($language);
            }
        }

        $this->getEntityManager()->flush();
    }

    private function installStreets()
    {
        $cities = include 'config/streets.php';

        foreach ($cities as $cityData) {
            $city = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Address\City')
                ->findOneByPostal($cityData['postal']);

            if ($city === null) {
                $city = new City($cityData['postal'], $cityData['name']);
                $this->getEntityManager()->persist($city);
            }

            foreach ($cityData['streets'] as $streetData) {
                $street = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Address\Street')
                    ->findOneByCityAndName($city, $streetData['name']);

                if ($street === null) {
                    $this->getEntityManager()->persist(new Street($city, $streetData['register'], $streetData['name']));
                }
            }
        }

        $this->getEntityManager()->flush();
    }

    private function installOrganizations()
    {
        $currentOrganizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        if (count($currentOrganizations) > 0) {
            return;
        }

        $organizations = array(
            'Student IT',
        );

        foreach ($organizations as $name) {
            $organization = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Organization')
                ->findOneByName($name);

            if ($organization === null) {
                $organization = new Organization($name);
                $this->getEntityManager()->persist($organization);
            }
        }

        $this->getEntityManager()->flush();
    }
}
