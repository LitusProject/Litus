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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

$modules = array(
    'Laminas\Cache',
    'Laminas\Filter',
    'Laminas\Form',
    'Laminas\I18n',
    'Laminas\Mvc\I18n',
    'Laminas\InputFilter',
    'Laminas\Paginator',
    'Laminas\Router',
    'Laminas\Serializer',
    'Laminas\Session',
    'Laminas\Validator',
    'Laminas\ZendFrameworkBridge',

    'AsseticBundle',
    'DoctrineModule',
    'DoctrineORMModule',
    'ZendTwig',

    'ApiBundle',
    'BannerBundle',
    'BootstrapBundle',
    'BrBundle',
    'CalendarBundle',
    'CommonBundle',
    'CudiBundle',
    'DoorBundle',
    'FormBundle',
    'GalleryBundle',
    'LogisticsBundle',
    'MailBundle',
    'NewsBundle',
    'NotificationBundle',
    'OnBundle',
    'QuizBundle',
    'PageBundle',
    'PromBundle',
    'PublicationBundle',
    'SecretaryBundle',
    'ShiftBundle',
    'ShopBundle',
    'SportBundle',
    'SyllabusBundle',
    'TicketBundle',
    'WikiBundle',
);

if (getenv('APPLICATION_ENV') == 'development') {
    array_splice(
        $modules,
        array_search('ZendTwig', $modules),
        0,
        'Laminas\DeveloperTools'
    );
}

return $modules;
