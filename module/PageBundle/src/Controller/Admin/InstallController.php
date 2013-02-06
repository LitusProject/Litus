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

namespace PageBundle\Controller\Admin;

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
                    'key'         => 'page.file_path',
                    'value'       => 'data/page/files',
                    'description' => 'The path to the page files',
                ),
            )
        );
    }

    protected function initAcl()
    {
        $this->installAcl(
            array(
                'pagebundle' => array(
                    'admin_page' => array(
                        'add', 'delete', 'edit', 'manage', 'upload', 'uploadProgress'
                    ),
                    'admin_page_category' => array(
                        'add', 'delete', 'edit', 'manage'
                    ),
                    'admin_page_link' => array(
                        'add', 'delete', 'edit', 'manage'
                    ),
                    'link' => array(
                        'view'
                    ),
                    'page' => array(
                        'file', 'view'
                    ),
                )
            )
        );

        $this->installRoles(
            array(
                'editor' => array(
                    'system' => true,
                    'parents' => array(),
                    'actions' => array(
                        'admin_page' => array(
                            'add', 'delete', 'edit', 'manage', 'upload', 'uploadProgress'
                        ),
                    )
                ),
                'guest' => array(
                    'system' => true,
                    'parents' => array(),
                    'actions' => array(
                        'link' => array(
                            'view'
                        ),
                        'page' => array(
                            'file', 'view'
                        ),
                    )
                ),
            )
        );
    }
}
