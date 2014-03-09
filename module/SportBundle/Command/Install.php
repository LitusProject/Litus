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

namespace SportBundle\Command;

use SportBundle\Entity\Department;

/**
 * InstallController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Install extends \CommonBundle\Component\Console\Command\Install
{
    protected function postInstall()
    {
        $this->write('Installing Departments...');
        $this->_installDepartments();
        $this->writeln(' done.', true);
    }

    private function _installDepartments()
    {
        $departments = array(
            array(
                'name'       => 'Architectuur',
                'happyHours' => array(
                    '0809'
                ),
            ),
            array(
                'name'       => 'Bouwkunde',
                'happyHours' => array(
                    '1819'
                ),
            ),
            array(
                'name'       => 'Chemische Ingenieurstechnieken',
                'happyHours' => array(
                    '1415'
                ),
            ),
            array(
                'name'       => 'Computerwetenschappen',
                'happyHours' => array(
                    '1112'
                ),
            ),
            array(
                'name'       => 'Eerstejaars Burgies',
                'happyHours' => array(
                    '1718'
                ),
            ),
            array(
                'name'       => 'Elektrotechniek',
                'happyHours' => array(
                    '2300'
                ),
            ),
            array(
                'name'       => 'Materiaalkunde',
                'happyHours' => array(
                    '1213'
                ),
            ),
            array(
                'name'       => 'Tweedejaars Burgies',
                'happyHours' => array(
                    '2223'
                ),
            ),
            array(
                'name'       => 'Werktuigkunde',
                'happyHours' => array(
                    '1314'
                ),
            ),
        );

        foreach ($departments as $department) {
            $repositoryCheck = $this->getEntityManager()
                ->getRepository('SportBundle\Entity\Department')
                ->findOneByName($department['name']);

            if (null === $repositoryCheck) {
                $newDepartment = new Department($department['name'], $department['happyHours']);
                $this->getEntityManager()->persist($newDepartment);
            } else {
                $repositoryCheck->setHappyHours($department['happyHours']);
            }
        }
        $this->getEntityManager()->flush();
    }
}
