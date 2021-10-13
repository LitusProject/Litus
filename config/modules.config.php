<?php

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
