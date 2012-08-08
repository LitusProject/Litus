<?php
 
namespace CalendarBundle\Controller\Admin;

/**
 * InstallController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{
    protected function _initConfig()
    {
    }
    
    protected function _initAcl()
    {
        $this->installAclStructure(
            array(
                'calendarBundle' => array(
                    'admin_calendar' => array(
                        'add', 'delete', 'edit', 'manage'
                    ),
                )
            )
        );
        
        $this->installRoles(
            array(
                'guest' => array(
                    'parent_roles' => array(),
                    'actions' => array(
                    )
                ),
            )
        );
    }
}
