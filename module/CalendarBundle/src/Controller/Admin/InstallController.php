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
            )
        );
    }

    protected function initAcl()
    {
        $this->installAcl(
            array(
                'calendarbundle' => array(
                    'admin_calendar' => array(
                        'add', 'delete', 'edit', 'editPoster', 'manage', 'old', 'pdf', 'poster'
                    ),
                    'calendar' => array(
                        'month', 'overview', 'poster', 'view'
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
