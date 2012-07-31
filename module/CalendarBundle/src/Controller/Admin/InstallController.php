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
        $this->_installConfig(
            array(
                /*array(
                    'key'         => 'search_max_results',
                    'value'       => '30',
                    'description' => 'The maximum number of search results shown',
                ),*/
            )
        );
    }
    
    protected function _initAcl()
    {
        $this->installAclStructure(
            array(
                'calendarBundle' => array(
                    'common_calendar' => array(
                        'overview', 'view'
                    ),
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
                        'common_calendar' => array(
                            'overview', 'view'
                        ),
                    )
                ),
                'sudo' => array(
                    'parent_roles' => array(
                        'guest'
                    ),
                    'actions' => array(
                        'admin_calendar' => array(
                            'add', 'delete', 'edit', 'manage'
                        ),
                    )
                )
            )
        );
    }
}
