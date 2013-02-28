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
                    'key'         => 'shift.signout_treshold',
                    'value'       => 'P1D',
                    'description' => 'The date interval after which a person cannot sign out from a shift',
                ),
                array(
                    'key'         => 'shift.responsible_signout_treshold',
                    'value'       => 'PT12H',
                    'description' => 'The date interval after which a responsible cannot be signed out from a shift',
                ),
                array(
                    'key'         => 'shift.mail',
                    'value'       => 'it@vtk.be',
                    'description' => 'The mail address from which shift notifications are sent',
                ),
                array(
                    'key'         => 'shift.mail_name',
                    'value'       => 'VTK IT',
                    'description' => 'The name of the mail address from which shift notifications are sent',
                ),
                array(
                    'key'         => 'shift.praesidium_removed_mail',
                    'value'       => 'Dear,

You have been removed from the following shift by a non-praesidium volunteer:
{{ shift }}

-- This is an automatically generated email, please do not reply --',
                    'description' => 'The mail sent to a praesidium member when a volunteer removes him from a shift.',
                ),
                array(
                    'key'         => 'shift.praesidium_removed_mail_subject',
                    'value'       => 'Shift Signout',
                    'description' => 'The subject of the mail sent to a praesidium member when a volunteer removes him from a shift.',
                ),
                array(
                    'key'         => 'shift.shift_deleted_mail',
                    'value'       => 'Dear,

The following shift to which you were subscribed has been deleted:
{{ shift }}

-- This is an automatically generated email, please do not reply --',
                    'description' => 'The mail sent to a shift subscriber when the shift is deleted.',
                ),
                array(
                    'key'         => 'shift.shift_deleted_mail_subject',
                    'value'       => 'Shift Deleted',
                    'description' => 'The subject of the mail sent to a shift subscriber when the shift is deleted.',
                ),
                array(
                    'key'         => 'shift.subscription_deleted_mail',
                    'value'       => 'Dear,

You have been removed from the following shift by an administrator:
{{ shift }}

-- This is an automatically generated email, please do not reply --',
                    'description' => 'The mail sent to a shift subscriber when he is removed from the shift.',
                ),
                array(
                    'key'         => 'shift.subscription_deleted_mail_subject',
                    'value'       => 'Shift Signout',
                    'description' => 'The subject of the mail sent to a shift subscriber when he is removed from the shift.',
                ),
                array(
                    'key'         => 'shift.pdf_generator_path',
                    'value'       => 'data/shift/pdf_generator',
                    'description' => 'The path to the PDF generator files',
                ),
                array(
                    'key'         => 'shift.ranking_criteria',
                    'value'       => serialize(
                        array(
                            array(
                                'name' => 'silver',
                                'limit' => '10'
                            ),
                            array(
                                'name' => 'gold',
                                'limit' => '20'
                            ),
                        )
                    ),
                    'description' => 'The ranking criteria for volunteers',
                ),
                array(
                    'key'         => 'shift.icalendar_uid_suffix',
                    'value'       => 'shift.vtk.be',
                    'description' => 'The suffix of an iCalendar shift uid',
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
                        'add', 'delete', 'edit', 'manage', 'old'
                    ),
                    'admin_shift_counter' => array(
                        'delete', 'index', 'payed', 'search', 'units', 'view'
                    ),
                    'admin_shift_ranking' => array(
                        'index'
                    ),
                    'admin_shift_subscription' => array(
                        'manage', 'delete',
                    ),
                    'shift' => array(
                        'export', 'index', 'responsible', 'signout', 'volunteer'
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
                            'export', 'index', 'responsible', 'signout', 'volunteer'
                        ),
                    ),
                ),
            )
        );
    }
}
