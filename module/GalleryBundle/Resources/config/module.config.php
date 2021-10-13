<?php

namespace GalleryBundle;

use CommonBundle\Component\Module\Config;

return Config::create(
    array(
        'namespace'         => __NAMESPACE__,
        'directory'         => __DIR__,
        'translation_files' => array('site', 'validator'),
    ),
    array(
        'validators' => array(
            'aliases' => array(
                'albumname' => Component\Validator\AlbumName::class,
                'albumName' => Component\Validator\AlbumName::class,
                'AlbumName' => Component\Validator\AlbumName::class,
            ),
        ),
    )
);
