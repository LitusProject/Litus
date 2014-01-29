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
    'assetic_configuration' => array(
        'modules' => array(
            'bootstrapbundle' => array(
                'root_path' => __DIR__ . '/../../../../vendor/twitter/bootstrap/',
                'collections' => array(
                    'bootstrap_css' => array(
                        'assets' => array(
                            'less/bootstrap.less',
                        ),
                        'filters' => array(
                            'bootstrap_less' => array(
                                'name' => '\Assetic\Filter\LessFilter',
                                'option' => array(
                                    'nodeBin'   => '/usr/local/bin/node',
                                    'nodePaths' => array(
                                        '/usr/local/lib/node_modules',
                                    ),
                                    'compress'  => true,
                                ),
                            ),
                        ),
                        'options' => array(
                            'output' => 'bootstrap_css.css',
                        ),
                    ),
                    'bootstrap_js_affix' => array(
                        'assets' => array(
                            'js/affix.js',
                        ),
                    ),
                    'bootstrap_js_alert' => array(
                        'assets' => array(
                            'js/alert.js',
                        ),
                    ),
                    'bootstrap_js_button' => array(
                        'assets' => array(
                            'js/button.js',
                        ),
                    ),
                    'bootstrap_js_carousel' => array(
                        'assets' => array(
                            'js/carousel.js',
                        ),
                    ),
                    'bootstrap_js_collapse' => array(
                        'assets' => array(
                            'js/collapse.js',
                        ),
                    ),
                    'bootstrap_js_dropdown' => array(
                        'assets' => array(
                            'js/dropdown.js',
                        ),
                    ),
                    'bootstrap_js_modal' => array(
                        'assets' => array(
                            'js/modal.js',
                        ),
                    ),
                    'bootstrap_js_popover' => array(
                        'assets' => array(
                            'js/popover.js',
                        ),
                    ),
                    'bootstrap_js_scrollspy' => array(
                        'assets' => array(
                            'js/scrollspy.js',
                        ),
                    ),
                    'bootstrap_js_tab' => array(
                        'assets' => array(
                            'js/tab.js',
                        ),
                    ),
                    'bootstrap_js_tooltip' => array(
                        'assets' => array(
                            'js/tooltip.js',
                        ),
                    ),
                    'bootstrap_js_transition' => array(
                        'assets' => array(
                            'js/transition.js',
                        ),
                    ),
                ),
            ),
        ),
    ),
);
