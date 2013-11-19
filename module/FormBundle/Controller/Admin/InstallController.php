<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace FormBundle\Controller\Admin;

use CommonBundle\Entity\General\Language;

/**
 * InstallController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{
    protected function initConfig() {
        $this->installConfig(
            array(
                array(
                    'key'         => 'form.file_upload_path',
                    'value'       => 'data/form/files',
                    'description' => 'The path to the uploaded form files',
                ),
                array(
                    'key'         => 'form.mail_confirmation',
                    'value'       => serialize(
                        array(
                            'en' => array(
                                'content' => 'Dear %first_name% %last_name%,

Your subscription was successful. Your unique subscription id is %id%. Below is a summary of the values you entered in this form:

%entry_summary%

With best regards,
VTK',
                            ),
                            'nl' => array(
                                'content' => 'Beste %first_name% %last_name%,

Uw inschrijving was succesvol. Uw unieke inschrijving id is %id%. Hieronder is een overzicht van de ingevulde waarden:

%entry_summary%

Met vriendelijke groeten,
VTK',
                            ),
                        )
                    ),
                    'description' => 'The mail template for confirmation mails',
                ),
                array(
                    'key'         => 'form.mail_reminder',
                    'value'       => serialize(
                        array(
                            'en' => array(
                                'content' => 'Dear %first_name% %last_name%,

Your subscription was successful. Your unique subscription id is %id%. Below is a summary of the values you entered in this form:

%entry_summary%

With best regards,
VTK',
                            ),
                            'nl' => array(
                                'content' => 'Beste %first_name% %last_name%,

Uw inschrijving was succesvol. Uw unieke inschrijving id is %id%. Hieronder is een overzicht van de ingevulde waarden:

%entry_summary%

Met vriendelijke groeten,
VTK',
                            ),
                        )
                    ),
                    'description' => 'The mail template for confirmation mails',
                ),
            )
        );
    }

    protected function initAcl()
    {
        $this->installAcl(
            array(
                'formbundle' => array(
                    'form_admin_form' => array(
                        'add', 'delete', 'edit', 'manage', 'old'
                    ),
                    'form_admin_group' => array(
                        'add', 'delete', 'deleteForm', 'edit', 'forms', 'manage', 'old', 'sort'
                    ),
                    'form_admin_form_field' => array(
                        'add', 'delete', 'edit', 'manage', 'sort'
                    ),
                    'form_admin_form_viewer' => array(
                        'add', 'delete', 'manage'
                    ),
                    'form_view' => array(
                        'doodle', 'downloadFile', 'edit', 'view',
                    ),
                    'form_group' => array(
                        'view',
                    ),
                    'form_manage' => array(
                        'delete', 'doodle', 'download', 'downloadFile', 'edit', 'index', 'view',
                    ),
                    'form_manage_group' => array(
                        'index', 'view',
                    ),
                    'form_manage_mail' => array(
                        'send'
                    ),
                    'form_manage_auth' => array(
                        'login', 'logout', 'shibboleth',
                    ),
                ),
            )
        );

        $this->installRoles(
            array(
                'guest' => array(
                    'system' => true,
                    'parents' => array(
                    ),
                    'actions' => array(
                        'form_manage' => array(
                            'index'
                        ),
                        'form_manage_auth' => array(
                            'login', 'logout', 'shibboleth',
                        ),
                    ),
                ),
            )
        );
    }
}
