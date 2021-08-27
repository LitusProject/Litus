<?php

return array(
    'modules' => include __DIR__ . '/modules.config.php',

    'module_listener_options' => array(
        'module_paths' => array(
            'module/',
            'vendor/',
        ),

        'config_glob_paths' => array(
            'config/autoload/{{,*.}global,{,*.}local}.php',
        ),

        'config_cache_enabled'     => getenv('APPLICATION_ENV') != 'development',
        'config_cache_key'         => 'litus',
        'module_map_cache_enabled' => getenv('APPLICATION_ENV') != 'development',
        'module_map_cache_key'     => 'litus',

        'cache_dir' => 'data/cache/',
    ),
);
