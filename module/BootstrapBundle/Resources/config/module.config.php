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
                'root_path' => __DIR__ . '/../assets',
                'collections' => array(
                    'bootstrap_css' => array(
                        'assets' => array(
                            'less/bootstrap.less',
                        ),
                        'filters' => array(
                            'bootstrap_less' => array(
                                'name' => '\CommonBundle\Component\Assetic\Filter\Less',
                            ),
                        ),
                        'options' => array(
                            'output' => 'bootstrap_css.css',
                        ),
                    ),
                    'bootstrap_js_affix' => array(
                        'assets' => array(
                            __DIR__ . '/../../../../vendor/twitter/bootstrap/js/affix.js',
                        ),
                        'filters' => array(
                            'bootstrap_js_affix_yui' => array(
                                'name' => '\CommonBundle\Component\Assetic\Filter\Js',
                            ),
                        ),
                        'options' => array(
                            'output' => 'bootstrap_js_affix.js',
                        ),
                    ),
                    'bootstrap_js_alert' => array(
                        'assets' => array(
                            __DIR__ . '/../../../../vendor/twitter/bootstrap/js/alert.js',
                        ),
                        'filters' => array(
                            'bootstrap_js_alert_yui' => array(
                                'name' => '\CommonBundle\Component\Assetic\Filter\Js',
                            ),
                        ),
                        'options' => array(
                            'output' => 'bootstrap_js_alert.js',
                        ),
                    ),
                    'bootstrap_js_button' => array(
                        'assets' => array(
                            __DIR__ . '/../../../../vendor/twitter/bootstrap/js/button.js',
                        ),
                        'filters' => array(
                            'bootstrap_js_button_yui' => array(
                                'name' => '\CommonBundle\Component\Assetic\Filter\Js',
                            ),
                        ),
                        'options' => array(
                            'output' => 'bootstrap_js_button.js',
                        ),
                    ),
                    'bootstrap_js_carousel' => array(
                        'assets' => array(
                            __DIR__ . '/../../../../vendor/twitter/bootstrap/js/carousel.js',
                        ),
                        'filters' => array(
                            'bootstrap_js_carousel_yui' => array(
                                'name' => '\CommonBundle\Component\Assetic\Filter\Js',
                            ),
                        ),
                        'options' => array(
                            'output' => 'bootstrap_js_carousel.js',
                        ),
                    ),
                    'bootstrap_js_collapse' => array(
                        'assets' => array(
                            __DIR__ . '/../../../../vendor/twitter/bootstrap/js/collapse.js',
                        ),
                        'filters' => array(
                            'bootstrap_js_collapse_yui' => array(
                                'name' => '\CommonBundle\Component\Assetic\Filter\Js',
                            ),
                        ),
                        'options' => array(
                            'output' => 'bootstrap_js_collapse.js',
                        ),
                    ),
                    'bootstrap_js_dropdown' => array(
                        'assets' => array(
                            __DIR__ . '/../../../../vendor/twitter/bootstrap/js/dropdown.js',
                        ),
                        'filters' => array(
                            'bootstrap_js_dropdown_yui' => array(
                                'name' => '\CommonBundle\Component\Assetic\Filter\Js',
                            ),
                        ),
                        'options' => array(
                            'output' => 'bootstrap_js_dropdown.js',
                        ),
                    ),
                    'bootstrap_js_modal' => array(
                        'assets' => array(
                            __DIR__ . '/../../../../vendor/twitter/bootstrap/js/modal.js',
                        ),
                        'filters' => array(
                            'bootstrap_js_modal_yui' => array(
                                'name' => '\CommonBundle\Component\Assetic\Filter\Js',
                            ),
                        ),
                        'options' => array(
                            'output' => 'bootstrap_js_modal.js',
                        ),
                    ),
                    'bootstrap_js_popover' => array(
                        'assets' => array(
                            __DIR__ . '/../../../../vendor/twitter/bootstrap/js/popover.js',
                        ),
                        'filters' => array(
                            'bootstrap_js_popover_yui' => array(
                                'name' => '\CommonBundle\Component\Assetic\Filter\Js',
                            ),
                        ),
                        'options' => array(
                            'output' => 'bootstrap_js_popover.js',
                        ),
                    ),
                    'bootstrap_js_scrollspy' => array(
                        'assets' => array(
                            __DIR__ . '/../../../../vendor/twitter/bootstrap/js/scrollspy.js',
                        ),
                        'filters' => array(
                            'bootstrap_js_scrollspy_yui' => array(
                                'name' => '\CommonBundle\Component\Assetic\Filter\Js',
                            ),
                        ),
                        'options' => array(
                            'output' => 'bootstrap_js_scrollspy.js',
                        ),
                    ),
                    'bootstrap_js_tab' => array(
                        'assets' => array(
                            __DIR__ . '/../../../../vendor/twitter/bootstrap/js/tab.js',
                        ),
                        'filters' => array(
                            'bootstrap_js_tab_yui' => array(
                                'name' => '\CommonBundle\Component\Assetic\Filter\Js',
                            ),
                        ),
                        'options' => array(
                            'output' => 'bootstrap_js_tab.js',
                        ),
                    ),
                    'bootstrap_js_tooltip' => array(
                        'assets' => array(
                            __DIR__ . '/../../../../vendor/twitter/bootstrap/js/tooltip.js',
                        ),
                        'filters' => array(
                            'bootstrap_js_tooltip_yui' => array(
                                'name' => '\CommonBundle\Component\Assetic\Filter\Js',
                            ),
                        ),
                        'options' => array(
                            'output' => 'bootstrap_js_tooltip.js',
                        ),
                    ),
                    'bootstrap_js_transition' => array(
                        'assets' => array(
                            __DIR__ . '/../../../../vendor/twitter/bootstrap/js/transition.js',
                        ),
                        'filters' => array(
                            'bootstrap_js_transition_yui' => array(
                                'name' => '\CommonBundle\Component\Assetic\Filter\Js',
                            ),
                        ),
                        'options' => array(
                            'output' => 'bootstrap_js_transition.js',
                        ),
                    ),
                ),
            ),
        ),
    ),
);
