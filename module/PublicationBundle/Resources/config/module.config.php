<?php

namespace PublicationBundle;

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
                'titleeditionhtml' => Component\Validator\Title\Edition\Html::class,
                'titleEditionHtml' => Component\Validator\Title\Edition\Html::class,
                'TitleEditionHtml' => Component\Validator\Title\Edition\Html::class,
                'titleeditionpdf'  => Component\Validator\Title\Edition\Pdf::class,
                'titleEditionPdf'  => Component\Validator\Title\Edition\Pdf::class,
                'TitleEditionPdf'  => Component\Validator\Title\Edition\Pdf::class,
                'titlepublication' => Component\Validator\Title\Publication::class,
                'titlePublication' => Component\Validator\Title\Publication::class,
                'TitlePublication' => Component\Validator\Title\Publication::class,
            ),
        ),
    )
);
