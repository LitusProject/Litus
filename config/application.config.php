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
        'SportBundle',
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
    ),
);
