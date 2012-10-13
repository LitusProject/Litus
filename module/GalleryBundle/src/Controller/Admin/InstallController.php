<?php

namespace GalleryBundle\Controller\Admin;

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
                    'key'         => 'gallery.path',
                    'value'       => '/_gallery/albums',
                    'description' => 'The path to the gallery albums',
                ),
                array(
                    'key'         => 'gallery.watermark_path',
                    'value'       => 'data/gallery/img/watermark.png',
                    'description' => 'The path to the watermark',
                )
            )
        );
    }

    protected function initAcl()
    {
        $this->installAcl(
            array(
                'galleryBundle' => array(
                    'admin_gallery' => array(
                        'add', 'addPhotos', 'censorPhoto', 'delete', 'edit', 'manage', 'photos', 'upload'
                    ),
                    'common_gallery' => array(
                        'album', 'overview', 'year'
                    )
                )
            )
        );
    }
}
