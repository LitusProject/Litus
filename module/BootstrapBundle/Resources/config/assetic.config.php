<?php

namespace BootstrapBundle;

use Assetic\Filter\LessFilter;
use Assetic\Filter\UglifyJs3Filter;

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
                '?UglifyJs3Filter' => array(
                    'name' => UglifyJs3Filter::class,
                ),
            ),
        ),
        'bootstrap_js_alert' => array(
            'assets' => array(
                __DIR__ . '/../../../../vendor/twitter/bootstrap/js/alert.js',
            ),
            'filters' => array(
                '?UglifyJs3Filter' => array(
                    'name' => UglifyJs3Filter::class,
                ),
            ),
        ),
        'bootstrap_js_button' => array(
            'assets' => array(
                __DIR__ . '/../../../../vendor/twitter/bootstrap/js/button.js',
            ),
            'filters' => array(
                '?UglifyJs3Filter' => array(
                    'name' => UglifyJs3Filter::class,
                ),
            ),
        ),
        'bootstrap_js_carousel' => array(
            'assets' => array(
                __DIR__ . '/../../../../vendor/twitter/bootstrap/js/carousel.js',
            ),
            'filters' => array(
                '?UglifyJs3Filter' => array(
                    'name' => UglifyJs3Filter::class,
                ),
            ),
        ),
        'bootstrap_js_collapse' => array(
            'assets' => array(
                __DIR__ . '/../../../../vendor/twitter/bootstrap/js/collapse.js',
            ),
            'filters' => array(
                '?UglifyJs3Filter' => array(
                    'name' => UglifyJs3Filter::class,
                ),
            ),
        ),
        'bootstrap_js_dropdown' => array(
            'assets' => array(
                __DIR__ . '/../../../../vendor/twitter/bootstrap/js/dropdown.js',
            ),
            'filters' => array(
                '?UglifyJs3Filter' => array(
                    'name' => UglifyJs3Filter::class,
                ),
            ),
        ),
        'bootstrap_js_modal' => array(
            'assets' => array(
                __DIR__ . '/../../../../vendor/twitter/bootstrap/js/modal.js',
            ),
            'filters' => array(
                '?UglifyJs3Filter' => array(
                    'name' => UglifyJs3Filter::class,
                ),
            ),
        ),
        'bootstrap_js_popover' => array(
            'assets' => array(
                __DIR__ . '/../../../../vendor/twitter/bootstrap/js/popover.js',
            ),
            'filters' => array(
                '?UglifyJs3Filter' => array(
                    'name' => UglifyJs3Filter::class,
                ),
            ),
        ),
        'bootstrap_js_scrollspy' => array(
            'assets' => array(
                __DIR__ . '/../../../../vendor/twitter/bootstrap/js/scrollspy.js',
            ),
            'filters' => array(
                '?UglifyJs3Filter' => array(
                    'name' => UglifyJs3Filter::class,
                ),
            ),
        ),
        'bootstrap_js_tab' => array(
            'assets' => array(
                __DIR__ . '/../../../../vendor/twitter/bootstrap/js/tab.js',
            ),
            'filters' => array(
                '?UglifyJs3Filter' => array(
                    'name' => UglifyJs3Filter::class,
                ),
            ),
        ),
        'bootstrap_js_tooltip' => array(
            'assets' => array(
                __DIR__ . '/../../../../vendor/twitter/bootstrap/js/tooltip.js',
            ),
            'filters' => array(
                '?UglifyJs3Filter' => array(
                    'name' => UglifyJs3Filter::class,
                ),
            ),
        ),
        'bootstrap_js_transition' => array(
            'assets' => array(
                __DIR__ . '/../../../../vendor/twitter/bootstrap/js/transition.js',
            ),
            'filters' => array(
                '?UglifyJs3Filter' => array(
                    'name' => UglifyJs3Filter::class,
                ),
            ),
        ),
    ),
);
