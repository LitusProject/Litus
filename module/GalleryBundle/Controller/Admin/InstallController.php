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
                    'value'       => 'data/gallery/watermark.png',
                    'description' => 'The path to the watermark',
                ),
                array(
                    'key'         => 'gallery.archive_url',
                    'value'       => 'http://old.vtk.be/ontspanning/fotoboek/',
                    'description' => 'The url to the archive',
                )
            )
        );
    }

    protected function initAcl()
    {
        $this->installAcl(
            array(
                'gallerybundle' => array(
                    'gallery_admin_gallery' => array(
                        'add', 'addPhotos', 'censorPhoto', 'delete', 'deletePhoto', 'edit', 'manage', 'photos', 'unCensorPhoto', 'upload', 'viewPhoto'
                    ),
                    'gallery' => array(
                        'album', 'overview', 'year', 'censor', 'uncensor'
                    )
                )
            )
        );
    }
}
