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
            'invokables' => array(
                'mail_entry_academic'    => 'MailBundle\Component\Validator\Entry\Academic',
                'mail_entry_external'    => 'MailBundle\Component\Validator\Entry\External',
                'mail_entry_mailinglist' => 'MailBundle\Component\Validator\Entry\MailingList',
                'mail_admin_map'         => 'MailBundle\Component\Validator\AdminMap',
                'mail_admin_role'        => 'MailBundle\Component\Validator\AdminRole',
                'mail_alias'             => 'MailBundle\Component\Validator\Alias',
                'mail_multi_mail'        => 'MailBundle\Component\Validator\MultiMail',
                'mail_named_list'        => 'MailBundle\Component\Validator\NamedList',
            ),
        ),
    )
);
