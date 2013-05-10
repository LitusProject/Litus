<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace LogisticsBundle\Controller\Admin;

use Exception;

/**
 * InstallController for the LogisticsBundle
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{

    protected function initConfig() {}

    protected function initAcl()
    {
        $this->installAcl(
            array(
                'logisticsbundle' => array(
                    'logistics_admin_driver' => array(
                        'add', 'delete', 'edit', 'manage'
                    ),
                    'logistics_admin_van_reservation' => array(
                        'add', 'delete', 'edit', 'manage', 'old'
                    ),
                    'logistics_index' => array(
                        'add', 'delete', 'edit', 'fetch', 'index', 'move'
                    ),
                    'logistics_auth' => array(
                        'login', 'logout', 'shibboleth',
                    ),
                )
            )
        );

        $this->installRoles(
            array(
                'guest' => array(
                    'system' => true,
                    'parents' => array(
                    ),
                    'actions' => array(
                        'logistics_index' => array(
                            'fetch', 'index'
                        ),
                        'logistics_auth' => array(
                            'login', 'logout', 'shibboleth',
                        ),
                    ),
                ),
            )
        );
    }
}
