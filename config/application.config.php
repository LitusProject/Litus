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
    'modules' => array(
        'AsseticBundle',
        'EdpMarkdown',
        'DoctrineModule',
        'DoctrineORMModule',
        'DoctrineMongoODMModule',
        'ZendDeveloperTools',
        'ZfcTwig',

        'BootstrapBundle',

        'CommonBundle',

        'BrBundle',
        'CudiBundle',
        'MailBundle',
        'ShiftBundle',
        'ShopBundle',
        'SportBundle',
        'PromBundle',
        'SyllabusBundle',
        'TicketBundle',

        'LogisticsBundle',
        'SecretaryBundle',

        'BannerBundle',
        'CalendarBundle',
        'NewsBundle',
        'NotificationBundle',
        'PageBundle',
        'GalleryBundle',

        'FormBundle',
        'PublicationBundle',

        'ApiBundle',
        'DoorBundle',
        'OnBundle',

        'WikiBundle',

        'QuizBundle',
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            'config/autoload/{,*.}{global,local}.php',
        ),
        'module_paths' => array(
            './module',
            './vendor',
        ),

        // Whether or not to enable a configuration cache.
        // If enabled, the merged configuration will be cached and used in
        // subsequent requests.
        'config_cache_enabled' => ('production' == getenv('APPLICATION_ENV')),

        // The key used to create the configuration cache file name.
        'config_cache_key' => 'zf2.configuration',

        // Whether or not to enable a module class map cache.
        // If enabled, creates a module class map cache which will be used
        // by in future requests, to reduce the autoloading process.
        'module_map_cache_enabled' => ('production' == getenv('APPLICATION_ENV')),

        // The key used to create the class map cache file name.
        'module_map_cache_key' => 'zf2.classmap',

        // The path in which to cache merged configuration.
        'cache_dir' => 'data/cache',
    ),
);
