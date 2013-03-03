<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace SecretaryBundle\Controller\Admin;

use CommonBundle\Entity\General\Language;

/**
 * InstallController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{
    protected function initConfig()
    {
        $this->installConfig(
            array(
                array(
                    'key'         => 'secretary.registration_enabled',
                    'value'       => '1',
                    'description' => 'Flag whether the registration module is enabled or not',
                ),
                array(
                    'key'         => 'secretary.membership_article',
                    'value'       => '427',
                    'description' => 'The article for the membership',
                ),
                array(
                    'key'         => 'secretary.terms_and_conditions_nl',
                    'value'       => 'Blablabal',
                    'description' => 'The organization terms and conditions',
                ),
                array(
                    'key'         => 'secretary.terms_and_conditions_en',
                    'value'       => 'Blablabal',
                    'description' => 'The organization terms and conditions',
                ),
            )
        );
    }

    protected function initAcl()
    {
        $this->installAcl(
            array(
                'secretarybundle' => array(
                    'secretary_registration' => array(
                        'add', 'complete', 'edit', 'saveStudies', 'saveSubjects', 'studies', 'subjects'
                    ),
                    'admin_secretary_registration' => array(
                        'barcode', 'edit', 'manage', 'search'
                    ),
                    'admin_mail_promotion' => array(
                        'manage'
                    ),
                ),
            )
        );

        $this->installRoles(
            array(
                'student' => array(
                    'system' => true,
                    'parents' => array(
                        'guest',
                    ),
                    'actions' => array(
                        'secretary_registration' => array(
                            'add', 'complete', 'edit', 'saveStudies', 'saveSubjects', 'studies', 'subjects'
                        ),
                    ),
                ),
            )
        );
    }
}
