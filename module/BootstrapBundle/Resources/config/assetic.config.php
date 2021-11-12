<?php

namespace BootstrapBundle;

use CommonBundle\Component\Assetic\Filter\Js as JsFilter;
use CommonBundle\Component\Assetic\Filter\Less as LessFilter;

return array(
    'collections' => array(
        'bootstrap_css' => array(
            'assets' => array(
                'less/bootstrap.less',
            ),
            'filters' => array(
                '?LessFilter' => array(
                    'name' => LessFilter::class,
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
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),
        'bootstrap_js_alert' => array(
            'assets' => array(
                __DIR__ . '/../../../../vendor/twitter/bootstrap/js/alert.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),
        'bootstrap_js_button' => array(
            'assets' => array(
                __DIR__ . '/../../../../vendor/twitter/bootstrap/js/button.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),
        'bootstrap_js_carousel' => array(
            'assets' => array(
                __DIR__ . '/../../../../vendor/twitter/bootstrap/js/carousel.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),
        'bootstrap_js_collapse' => array(
            'assets' => array(
                __DIR__ . '/../../../../vendor/twitter/bootstrap/js/collapse.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),
        'bootstrap_js_dropdown' => array(
            'assets' => array(
                __DIR__ . '/../../../../vendor/twitter/bootstrap/js/dropdown.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),
        'bootstrap_js_modal' => array(
            'assets' => array(
                __DIR__ . '/../../../../vendor/twitter/bootstrap/js/modal.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),
        'bootstrap_js_popover' => array(
            'assets' => array(
                __DIR__ . '/../../../../vendor/twitter/bootstrap/js/popover.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),
        'bootstrap_js_scrollspy' => array(
            'assets' => array(
                __DIR__ . '/../../../../vendor/twitter/bootstrap/js/scrollspy.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),
        'bootstrap_js_tab' => array(
            'assets' => array(
                __DIR__ . '/../../../../vendor/twitter/bootstrap/js/tab.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),
        'bootstrap_js_tooltip' => array(
            'assets' => array(
                __DIR__ . '/../../../../vendor/twitter/bootstrap/js/tooltip.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),
        'bootstrap_js_transition' => array(
            'assets' => array(
                __DIR__ . '/../../../../vendor/twitter/bootstrap/js/transition.js',
            ),
            'filters' => array(
                '?JsFilter' => array(
                    'name' => JsFilter::class,
                ),
            ),
        ),
    ),
);
