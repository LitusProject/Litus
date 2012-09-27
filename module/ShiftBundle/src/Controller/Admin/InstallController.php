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
                array(
                    'key'         => 'shiftbundle.mail',
                    'value'       => 'it@vtk.be',
                    'description' => 'The mail address from which shift notifications are sent',
                ),
                array(
                    'key'         => 'shiftbundle.mail_name',
                    'value'       => 'VTK IT',
                    'description' => 'The name of the mail address from which shift notifications are sent',
                ),
                array(
                    'key'         => 'shiftbundle.praesidium_removed_mail',
                    'value'       => 'Dear,

You have been removed from the following shift by a non-praesidium volunteer:
{{ shift }}

-- This is an automatically generated email, please do not reply --',
                    'description' => 'The mail sent to a praesidium member when a volunteer removes him from a shift.',
                ),
                array(
                    'key'         => 'shiftbundle.praesidium_removed_mail_subject',
                    'value'       => 'Shift Unsubscription',
                    'description' => 'The subject of the mail sent to a praesidium member when a volunteer removes him from a shift.',
                ),
                array(
                    'key'         => 'shiftbundle.shift_deleted_mail',
                    'value'       => 'Dear,

The following shift to which you were subscribed has been deleted:
{{ shift }}

-- This is an automatically generated email, please do not reply --',
                    'description' => 'The mail sent to a shift subscriber when the shift is deleted.',
                ),
                array(
                    'key'         => 'shiftbundle.shift_deleted_mail_subject',
                    'value'       => 'Shift Deleted',
                    'description' => 'The subject of the mail sent to a shift subscriber when the shift is deleted.',
                ),
                array(
                    'key'         => 'shiftbundle.subscription_deleted_mail',
                    'value'       => 'Dear,

You have been removed from the following shift by an administrator:
{{ shift }}

-- This is an automatically generated email, please do not reply --',
                    'description' => 'The mail sent to a shift subscriber when he is removed from the shift.',
                ),
                array(
                    'key'         => 'shiftbundle.subscription_deleted_mail_subject',
                    'value'       => 'Shift Unsubscription',
                    'description' => 'The subject of the mail sent to a shift subscriber when he is removed from the shift.',
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
                    'admin_shift' => array(
                        'add', 'delete', 'edit', 'manage'
                    ),
                    'admin_subscription' => array(
                        'manage', 'delete',
                    )
                    'shift' => array(
                        'index', 'responsible', 'signout', 'volunteer'
                    ),
                ),
            )
        );

        $this->installRoles(
            array(
                'student' => array(
                    'system' => true,
                    'parents' => array(
                        'guest',
                    ),
                    'actions' => array(
                        'shift' => array(
                            'index', 'responsible', 'signout', 'volunteer'
                        ),
                    ),
                ),
            )
        );
    }
}
