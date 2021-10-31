<?php

namespace QuizBundle;

use Assetic\Filter\LessFilter;

return array(
    'controllers' => array(
        'quiz_admin_quiz' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'quiz_admin_team' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'quiz_admin_round' => array(
            '@common_jquery',
            '@common_jqueryui',
            '@common_jquery_table_sort',
            '@admin_css',
            '@admin_js',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'quiz_quiz' => array(
            '@common_jquery',
            '@bootstrap_css',
            '@bootstrap_js_tab',
            '@bootstrap_js_transition',
            '@quiz_css',
        ),
    ),

    'collections' => array(
        'quiz_css' => array(
            'assets' => array(
                'quiz/less/base.less',
            ),
            'filters' => array(
                '?LessFilter' => array(
                    'name' => LessFilter::class,
                ),
            ),
            'options' => array(
                'output' => 'quiz_css.css',
            ),
        ),
    ),
);
