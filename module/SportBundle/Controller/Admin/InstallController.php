<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
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
                    'key'         => 'sport.queue_socket_port',
                    'value'       => '8897',
                    'description' => 'The port used for the websocket of the queue',
                ),
                array(
                    'key'         => 'sport.queue_socket_remote_host',
                    'value'       => '127.0.0.1',
                    'description' => 'The remote host for the websocket of the queue',
                ),
                array(
                    'key'         => 'sport.queue_socket_host',
                    'value'       => '127.0.0.1',
                    'description' => 'The host used for the websocket of the queue',
                ),
                array(
                    'key'         => 'sport.queue_socket_key',
                    'value'       => '2wA25hTrkiUIWUIGNedstXSWYhKSr30p',
                    'description' => 'The key used for the websocket of the queue',
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
                        'edit', 'groups', 'identification', 'queue', 'update', 'laps', 'killSocket'
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
            'Bouwkunde',
            'Chemische Ingenieurstechnieken',
            'Computerwetenschappen',
            'Elektrotechniek',
            'Geotechniek en mijnbouwkunde',
            'Materiaalkunde',
            'Werktuigkunde',
        );

        foreach($departments as $name) {
            $department = $this->getEntityManager()
                ->getRepository('SportBundle\Entity\Department')
                ->findOneByCode($name);
            if (null == $department) {
                $department = new Department($name);
                $this->getEntityManager()->persist($department);
            }
        }
        $this->getEntityManager()->flush();
    }
}
