<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace WikiBundle\Controller\Admin;

/**
 * InstallController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{
    protected function initConfig()
    {
        $this->installConfig(
            array(
                array(
                    'key'         => 'wiki.url',
                    'value'       => 'https://wiki.vtk.be/wiki/index.php?title=Special:Login&returnto=Main_Page',
                    'description' => 'The URL to the organization\'s wiki\'s login page',
                ),
            )
        );
    }

    protected function initAcl()
    {
        $this->installAcl(
            array(
                'wikibundle' => array(
                    'wiki_auth' => array(
                        'login', 'logout', 'shibboleth'
                    ),
                ),
            )
        );

        $this->installRoles(
            array(
                'guest' => array(
                    'system' => true,
                    'parents' => array(
                    ),
                    'actions' => array(
                        'wiki_auth' => array(
                            'login', 'logout', 'shibboleth'
                        ),
                    ),
                ),
            )
        );
    }
}
