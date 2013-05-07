<?php

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
