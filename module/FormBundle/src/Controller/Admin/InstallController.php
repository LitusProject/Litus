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

namespace FormBundle\Controller\Admin;

use CommonBundle\Entity\General\Language;

/**
 * InstallController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{
    protected function initConfig() {}

    protected function initAcl()
    {
        $this->installAcl(
            array(
                'formBundle' => array(
                    'admin_form' => array(
                        'add', 'delete', 'edit', 'manage'
                    ),
                    'admin_form_field' => array(
                        'add', 'delete', 'manage'
                    ),
                    'admin_form_viewer' => array(
                        'add', 'delete', 'manage'
                    ),
                    'form_view' => array(
                        'view', 'complete',
                    ),
                    'form_manage' => array(
                        'index', 'view', 'edit', 'delete',
                    ),
                    'form_manage_auth' => array(
                        'login', 'logout',
                    ),
                ),
            )
        );
    }
}
