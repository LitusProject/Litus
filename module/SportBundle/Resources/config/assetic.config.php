<?php

namespace SportBundle;

use CommonBundle\Component\Assetic\Filter\Less as LessFilter;

return array(
    'controllers' => array(
        'sport_admin_run' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
            '@common_socket',
            '@common_remote_typeahead',
        ),
    ),

    'collections' => array(
        'run_css' => array(
            'assets' => array(
                'run/less/base.less',
            ),
            'filters' => array(
                '?LessFilter' => array(
                    'name' => LessFilter::class,
                ),
            ),
            'options' => array(
                'output' => 'run_css.css',
            ),
        ),
    ),
);
