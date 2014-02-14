<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

return array(
    'controllers'  => array(
        'secretary_install' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
        ),
        'secretary_admin_registration' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@common_remote_typeahead',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'secretary_admin_promotion' => array(
            '@common_jquery',
            '@admin_css',
            '@admin_js',
            '@common_remote_typeahead',
            '@bootstrap_js_transition',
            '@bootstrap_js_modal',
        ),
        'secretary_registration' => array(
            '@common_jquery',
            '@bootstrap_css',
            '@site_css',
            '@bootstrap_js_dropdown',
            '@bootstrap_js_transition',
            '@bootstrap_js_carousel',
            '@bootstrap_js_collapse',
            '@bootstrap_js_modal',
            '@bootstrap_js_alert',
            '@secretary_css',
            '@common_remote_typeahead',
        ),
    ),

    'collections' => array(
        'secretary_css' => array(
            'assets' => array(
                'secretary/less/study.less',
            ),
            'filters' => array(
                'secretary_less' => array(
                    'name' => '\CommonBundle\Component\Assetic\Filter\Less',
                ),
            ),
            'options' => array(
                'output' => 'secretary.css',
            ),
        ),
    ),
);
