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

namespace CommonBundle\Controller\Admin;

use CommonBundle\Entity\General\Address\City,
    CommonBundle\Entity\General\Address\Street,
    CommonBundle\Entity\General\Language,
    CommonBundle\Entity\General\Organization;

/**
 * InstallController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{
    protected function preInstall()
    {
        $this->_installLanguages();
        $this->_installCities();
        $this->_installOrganizations();
    }

    private function _installLanguages()
    {
        $languages = array(
            'en' => 'English',
            'nl' => 'Nederlands'
        );

        foreach($languages as $abbrev => $name) {
            $language = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Language')
                ->findOneByAbbrev($abbrev);

            if (null === $language) {
                $language = new Language($abbrev, $name);
                $this->getEntityManager()->persist($language);
            }
        }

        $this->getEntityManager()->flush();
    }

    private function _installCities()
    {
        $cities = include('config/streets.php');

        foreach($cities as $cityData) {
            $city = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Address\City')
                ->findOneByPostal($cityData['postal']);

            if (null === $city) {
                $city = new City($cityData['postal'], $cityData['name']);
                $this->getEntityManager()->persist($city);
            }

            foreach($cityData['streets'] as $streetData) {
                $street = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Address\Street')
                    ->findOneByCityAndName($city, $streetData['name']);
                if (null === $street)
                    $this->getEntityManager()->persist(new Street($city, $streetData['register'], $streetData['name']));
            }
        }

        $this->getEntityManager()->flush();
    }

    private function _installOrganizations()
    {
        $currentOrganizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();
        if (sizeof($currentOrganizations) > 0)
            return;

        $organizations = array(
            'VTK',
        );

        foreach($organizations as $name) {
            $organization = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Organization')
                ->findOneByName($name);

            if (null === $organization) {
                $organization = new Organization($name);
                $this->getEntityManager()->persist($organization);
            }
        }

        $this->getEntityManager()->flush();
    }
}
