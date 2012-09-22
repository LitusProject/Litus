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

namespace ShiftBundle\Controller\Admin;

use CommonBundle\Entity\General\Language;

/**
 * InstallController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{
    protected function initConfig()
    {
        $this->installConfig(
            array(
                array(
                    'key'         => 'shiftbundle.signout_treshold',
                    'value'       => 'P1D',
                    'description' => 'The date interval after which a person cannot sign out from a shift',
                ),
                array(
                    'key'         => 'shiftbundle.responsible_signout_treshold',
                    'value'       => 'P12H',
                    'description' => 'The date interval after which a responsible cannot be signed out from a shift',
                ),
            )
        );
    }

    protected function initAcl()
    {
        $this->installAcl(
            array(
                'shiftbundle' => array(
                    'admin_unit' => array(
                        'add', 'delete', 'edit', 'manage'
                    ),
                ),
            )
        );
    }
}
