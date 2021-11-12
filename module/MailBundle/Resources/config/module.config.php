<?php

namespace MailBundle;

use CommonBundle\Component\Module\Config;

return Config::create(
    array(
        'namespace'         => __NAMESPACE__,
        'directory'         => __DIR__,
        'translation_files' => array('validator'),
    ),
    array(
        'validators' => array(
            'aliases' => array(
                'adminmap'         => Component\Validator\AdminMap::class,
                'adminMap'         => Component\Validator\AdminMap::class,
                'AdminMap'         => Component\Validator\AdminMap::class,
                'adminrole'        => Component\Validator\AdminRole::class,
                'adminRole'        => Component\Validator\AdminRole::class,
                'AdminRole'        => Component\Validator\AdminRole::class,
                'alias'            => Component\Validator\Alias::class,
                'Alias'            => Component\Validator\Alias::class,
                'entryacademic'    => Component\Validator\Entry\Academic::class,
                'entryAcademic'    => Component\Validator\Entry\Academic::class,
                'EntryAcademic'    => Component\Validator\Entry\Academic::class,
                'entryexternal'    => Component\Validator\Entry\External::class,
                'entryExternal'    => Component\Validator\Entry\External::class,
                'EntryExternal'    => Component\Validator\Entry\External::class,
                'entrymailinglist' => Component\Validator\Entry\MailingList::class,
                'entryMailingList' => Component\Validator\Entry\MailingList::class,
                'EntryMailingList' => Component\Validator\Entry\MailingList::class,
                'listname'         => Component\Validator\ListName::class,
                'listName'         => Component\Validator\ListName::class,
                'ListName'         => Component\Validator\ListName::class,
                'multimail'        => Component\Validator\MultiMail::class,
                'multiMail'        => Component\Validator\MultiMail::class,
                'MultiMail'        => Component\Validator\MultiMail::class,
            ),
        ),
    )
);
