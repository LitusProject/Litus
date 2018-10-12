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
        'has_documents'     => true,
    ),
    array(
        'validators' => array(
            'aliases' => array(
                'mail_entry_academic'    => Component\Validator\Entry\Academic::class,
                'mail_entry_external'    => Component\Validator\Entry\External::class,
                'mail_entry_mailinglist' => Component\Validator\Entry\MailingList::class,
                'mail_admin_map'         => Component\Validator\AdminMap::class,
                'mail_admin_role'        => Component\Validator\AdminRole::class,
                'mail_alias'             => Component\Validator\Alias::class,
                'mail_multi_mail'        => Component\Validator\MultiMail::class,
                'mail_named_list'        => Component\Validator\NamedList::class,
            ),
        ),
    )
);
