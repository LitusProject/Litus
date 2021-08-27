<?php

namespace PageBundle;

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
                'PageTitle' => Component\Validator\PageTitle::class,
                'FAQ' => Component\Validator\FAQ::class,
                'faq' => Component\Validator\FAQ::class,
                'Faq' => Component\Validator\FAQ::class,
            ),
        ),
    )
);
