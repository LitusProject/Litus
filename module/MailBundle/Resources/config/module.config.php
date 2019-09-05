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
