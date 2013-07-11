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
                    'bootstrap_responsive_css' => array(
                        'assets' => array(
                            'less/responsive.less',
                        ),
                        'filters' => array(
                            'bootstrap_responsive_less' => array(
                                'name' => '\Assetic\Filter\LessFilter',
                                'option' => array(
                                    'nodeBin'   => '/usr/local/bin/node',
                                    'nodePaths' => array(
                                        '/usr/local/lib/node_modules',
                                    ),
                                    'compress'  => false,
                                ),
                            ),
                        ),
                        'options' => array(
                            'output' => 'bootstrap_responsive_css.css',
                        ),
                    ),
                    'bootstrap_js_affix' => array(
                        'assets' => array(
                            'js/bootstrap-affix.js',
                        ),
                    ),
                    'bootstrap_js_alert' => array(
                        'assets' => array(
                            'js/bootstrap-alert.js',
                        ),
                    ),
                    'bootstrap_js_button' => array(
                        'assets' => array(
                            'js/bootstrap-button.js',
                        ),
                    ),
                    'bootstrap_js_carousel' => array(
                        'assets' => array(
                            'js/bootstrap-carousel.js',
                        ),
                    ),
                    'bootstrap_js_collapse' => array(
                        'assets' => array(
                            'js/bootstrap-collapse.js',
                        ),
                    ),
                    'bootstrap_js_dropdown' => array(
                        'assets' => array(
                            'js/bootstrap-dropdown.js',
                        ),
                    ),
                    'bootstrap_js_modal' => array(
                        'assets' => array(
                            'js/bootstrap-modal.js',
                        ),
                    ),
                    'bootstrap_js_popover' => array(
                        'assets' => array(
                            'js/bootstrap-popover.js',
                        ),
                    ),
                    'bootstrap_js_scrollspy' => array(
                        'assets' => array(
                            'js/bootstrap-scrollspy.js',
                        ),
                    ),
                    'bootstrap_js_tab' => array(
                        'assets' => array(
                            'js/bootstrap-tab.js',
                        ),
                    ),
                    'bootstrap_js_tooltip' => array(
                        'assets' => array(
                            'js/bootstrap-tooltip.js',
                        ),
                    ),
                    'bootstrap_js_transition' => array(
                        'assets' => array(
                            'js/bootstrap-transition.js',
                        ),
                    ),
                    'bootstrap_js_typeahead' => array(
                        'assets' => array(
                            'js/bootstrap-typeahead.js',
                        ),
                    ),
                ),
            ),
        ),
    ),
);
