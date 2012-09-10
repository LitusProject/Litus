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
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
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
                    'admin_driver' => array(
                        'add', 'delete', 'edit', 'manage'
                    ),
                    'admin_van_reservation' => array(
                        'add', 'delete', 'edit', 'manage'
                    ),
                )
            )
        );
    }
}
