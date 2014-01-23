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

namespace CalendarBundle\Controller\Admin;

/**
 * InstallController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{
    protected function initConfig()
    {
        $this->installConfig(
            array(
                array(
                    'key'         => 'calendar.poster_path',
                    'value'       => 'data/calendar/posters',
                    'description' => 'The path to the calendar poster files',
                ),
                array(
                    'key'         => 'calendar.icalendar_uid_suffix',
                    'value'       => 'event.vtk.be',
                    'description' => 'The suffix of an iCalendar event uid',
                ),
            )
        );
    }

    protected function initAcl()
    {
        $this->installAcl(
            array(
                'calendarbundle' => array(
                    'calendar_admin_calendar' => array(
                        'add', 'delete', 'edit', 'editPoster', 'manage', 'old', 'pdf', 'poster', 'progress', 'upload'
                    ),
                    'calendar_admin_calendar_registration' => array(
                        'export', 'manage'
                    ),
                    'calendar' => array(
                        'export', 'month', 'overview', 'poster', 'view'
                    ),
                )
            )
        );

        $this->installRoles(
            array(
                'guest' => array(
                    'parent_roles' => array(),
                    'actions' => array(
                        'calendar' => array(
                            'month', 'overview', 'poster', 'view'
                        ),
                    ),
                ),
            )
        );
    }
}
