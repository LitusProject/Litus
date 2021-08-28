<?php

return array(
    'laminas-developer-tools' => array(
        'profiler' => array(
            'enabled'     => true,
            'strict'      => false,
            'flush_early' => false,
            'cache_dir'   => 'data/cache',
            'matcher'     => array(),
            'collectors'  => array(
                'config' => null,
                'db'     => null,
            ),
        ),
        'toolbar' => array(
            'enabled'       => getenv('APPLICATION_ENV') == 'development',
            'auto_hide'     => true,
            'position'      => 'bottom',
            'version_check' => true,
            'entries'       => array(),
        ),
    ),
);
