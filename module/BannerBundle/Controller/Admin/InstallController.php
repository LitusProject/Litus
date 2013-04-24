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

namespace BannerBundle\Controller\Admin;

use CommonBundle\Entity\General\Language;

/**
 * InstallController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{
    protected function initConfig() {
        $this->installConfig(
            array(
                array(
                    'key'         => 'banner.image_path',
                    'value'       => 'data/banner/images',
                    'description' => 'The path to the banner images',
                ),
            )
        );
    }

    protected function initAcl()
    {
        $this->installAcl(
            array(
                'bannerBundle' => array(
                    'banner' => array(
                        'view'
                    ),
                    'banner_admin_banner' => array(
                        'add', 'delete', 'edit', 'manage', 'progress', 'upload'
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
                        'banner' => array(
                            'view'
                        ),
                    ),
                ),
            )
        );
    }
}
