<?php

namespace SportBundle\Component\Module;

use SportBundle\Entity\Department;

/**
 * SportBundle installer
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Installer extends \CommonBundle\Component\Module\AbstractInstaller
{
    protected function postInstall()
    {
        $this->write('Installing Departments...');
        $this->installDepartments();
        $this->writeln(" <fg=green>\u{2713}</fg=green>", true);
    }

    private function installDepartments()
    {
        $departments = array(
            array(
                'name'       => 'Architectuur',
                'happyHours' => array(
                    '0809',
                ),
            ),
            array(
                'name'       => 'Bouwkunde',
                'happyHours' => array(
                    '1819',
                ),
            ),
            array(
                'name'       => 'Chemische Ingenieurstechnieken',
                'happyHours' => array(
                    '1415',
                ),
            ),
            array(
                'name'       => 'Computerwetenschappen',
                'happyHours' => array(
                    '1112',
                ),
            ),
            array(
                'name'       => 'Eerstejaars Burgies A-groep',
                'happyHours' => array(
                    '0508',
                ),
            ),
            array(
                'name'       => 'Eerstejaars Burgies B-groep',
                'happyHours' => array(
                    '0508',
                ),
            ),
            array(
                'name'       => 'Elektrotechniek',
                'happyHours' => array(
                    '2300',
                ),
            ),
            array(
                'name'       => 'Materiaalkunde',
                'happyHours' => array(
                    '1213',
                ),
            ),
            array(
                'name'       => 'Tweedejaars Burgies',
                'happyHours' => array(
                    '2223',
                ),
            ),
            array(
                'name'       => 'Werktuigkunde',
                'happyHours' => array(
                    '1314',
                ),
            ),
        );

        foreach ($departments as $department) {
            $repositoryCheck = $this->getEntityManager()
                ->getRepository('SportBundle\Entity\Department')
                ->findOneByName($department['name']);

            if ($repositoryCheck === null) {
                $newDepartment = new Department($department['name'], $department['happyHours']);
                $this->getEntityManager()->persist($newDepartment);
            } else {
                $repositoryCheck->setHappyHours($department['happyHours']);
            }
        }
        $this->getEntityManager()->flush();
    }
}
