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

namespace SportBundle\Controller\Admin;

use CommonBundle\Entity\General\Language,
    SportBundle\Entity\Department;

/**
 * InstallController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{
    protected function initConfig()
    {
        $this->installConfig(
            array(
                array(
                    'key'         => 'sport.run_result_page',
                    'value'       => 'http://live.24urenloop.be/tussenstand.json',
                    'description' => 'The URL where the official result page can be found',
                ),
                array(
                    'key'         => 'sport.run_team_id',
                    'value'       => '4',
                    'description' => 'The ID of the organization on the official result page',
                ),
                array(
                    'key'         => 'sport.queue_socket_file',
                    'value'       => 'tcp://127.0.0.1:8897',
                    'description' => 'The file used for the websocket of the queue',
                ),
                array(
                    'key'         => 'sport.queue_socket_public',
                    'value'       => '127.0.0.1:8897',
                    'description' => 'The public address for the websocket of the queue',
                ),
                array(
                    'key'         => 'sport.queue_socket_key',
                    'value'       => md5(uniqid(rand(), true)),
                    'description' => 'The key used for the websocket of the queue',
                ),
                array(
                    'key'         => 'sport.points_criteria',
                    'value'       => serialize(
                        array(
                            array(
                                'limit'  => '90',
                                'points' => '1',
                            ),
                            array(
                                'limit'  => '87',
                                'points' => '3',
                            ),
                            array(
                                'limit'  => '84',
                                'points' => '4',
                            ),
                            array(
                                'limit'  => '81',
                                'points' => '5',
                            ),
                            array(
                                'limit'  => '79',
                                'points' => '6',
                            ),
                        )
                    ),
                    'description' => 'The criteria for the lap times that determine the number of points it is worth (times should decrease)',
                ),
            )
        );

        $this->_installDepartments();
    }

    protected function initAcl()
    {
        $this->installAcl(
            array(
                'sportbundle' => array(
                    'sport_admin_run' => array(
                        'edit', 'departments', 'groups', 'identification', 'killSocket', 'laps', 'pasta', 'queue', 'update'
                    ),
                    'sport_run_group' => array(
                        'add', 'getName'
                    ),
                    'sport_run_index' => array(
                        'index'
                    ),
                    'sport_run_queue' => array(
                        'index', 'getName'
                    ),
                    'sport_run_screen' => array(
                        'index'
                    ),
                ),
            )
        );

        $this->installRoles(
            array(
                'guest' => array(
                    'system' => true,
                    'parents' => array(
                    ),
                    'actions' => array(
                        'sport_run_group' => array(
                            'add', 'getName'
                        ),
                        'sport_run_index' => array(
                            'index'
                        ),
                    ),
                ),
            )
        );
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

        foreach($departments as $department) {
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
