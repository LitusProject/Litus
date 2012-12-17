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

namespace BrBundle\Controller\Admin;

use Exception;

/**
 * InstallController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class InstallController extends \CommonBundle\Component\Controller\ActionController\InstallController
{
    protected function initConfig()
    {
        $this->installConfig(
            array(
                array(
                    'key'         => 'br.logo_path',
                    'value'       => 'data/br/companies',
                    'description' => 'The path to the company logo files',
                ),
                array(
                    'key'         => 'br.file_path',
                    'value'       => 'data/br/files',
                    'description' => 'The path to the company files',
                ),
                array(
                    'key'         => 'br.public_logo_path',
                    'value'       => '_br/img',
                    'description' => 'The path to the public company logo files',
                ),
                array(
                    'key'         => 'br.cv_book_open',
                    'value'       => '0',
                    'description' => 'Whether the CV Book is currently open for entries or not',
                ),
                array(
                    'key'         => 'br.cv_default_languages',
                    'value'       => serialize(
                        array(
                            'nl' => 'Dutch',
                            'fr' => 'French',
                            'de' => 'German',
                            'en' => 'English',
                        )
                    ),
                    'description' => 'The default languages present in the CV book form',
                ),
            )
        );
    }

    protected function initAcl()
    {
        $this->installAcl(
            array(
                'brbundle' => array(
                    'admin_company' => array(
                        'add', 'delete', 'editLogo', 'edit', 'logo', 'manage'
                    ),
                    'admin_company_event' => array(
                        'add', 'delete', 'edit', 'editPoster', 'manage'
                    ),
                    'admin_company_job' => array(
                        'add', 'delete', 'edit', 'manage'
                    ),
                    'admin_company_user' => array(
                        'add', 'delete', 'edit', 'manage', 'activate'
                    ),
                    'admin_company_logo' => array(
                        'manage', 'add', 'delete'
                    ),
                    'admin_cv_entry' => array(
                        'manage', 'delete',
                    ),

                    'career_index' => array(
                        'index'
                    ),
                    'career_company' => array(
                        'view', 'overview', 'file', 'search', 'logo',
                    ),
                    'career_event' => array(
                        'view', 'overview'
                    ),
                    'career_vacancy' => array(
                        'view', 'overview'
                    ),
                    'career_internship' => array(
                        'view', 'overview'
                    ),

                    'cv_index' => array(
                        'cv', 'edit', 'complete',
                    ),

                    'corporate_auth' => array(
                        'login', 'logout',
                    ),
                    'corporate_index' => array(
                        'index',
                    ),
                    'corporate_cv' => array(
                        'cv', 'cvPhoto',
                    ),
                )
            )
        );

        $this->installRoles(
            array(
                'corporate' => array(
                    'system' => true,
                    'parents' => array(
                        'guest',
                    ),
                    'actions' => array(
                    ),
                ),
            )
        );
    }
}
