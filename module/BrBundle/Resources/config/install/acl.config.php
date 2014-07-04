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
    'brbundle' => array(
        'br_admin_collaborator' => array(
            'add', 'retire', 'edit', 'manage', 'rehire'
        ),
        'br_admin_company' => array(
            'add', 'delete', 'editLogo', 'edit', 'manage', 'search', 'upload', 'contacts'
        ),
        'br_admin_company_event' => array(
            'add', 'delete', 'edit', 'editPoster', 'manage', 'upload'
        ),
        'br_admin_company_job' => array(
            'add', 'delete', 'edit', 'manage'
        ),
        'br_admin_company_user' => array(
            'add', 'delete', 'edit', 'manage', 'activate'
        ),
        'br_admin_company_logo' => array(
            'manage', 'add', 'delete'
        ),
        'br_admin_contract' => array(
            'manage', 'edit', 'view', 'history', 'sign', 'download', 'delete'
        ),
        'br_admin_cv_entry' => array(
            'manage', 'delete', 'export', 'exportAcademics'
        ),
        'br_admin_invoice' => array(
            'history', 'view', 'edit', 'manage', 'download'
        ),
        'br_admin_order' => array(
            'product', 'edit', 'delete', 'view', 'add', 'old', 'manage'
        ),
        'br_admin_overview' => array(
            'person', 'company'
        ),
        'br_admin_product' => array(
            'add', 'delete', 'manage', 'edit', 'old'
        ),
        'br_career_index' => array(
            'index'
        ),
        'br_career_company' => array(
            'view', 'overview', 'file', 'search',
        ),
        'br_career_event' => array(
            'view', 'overview'
        ),
        'br_career_vacancy' => array(
            'view', 'overview'
        ),
        'br_career_internship' => array(
            'view', 'overview'
        ),
        'br_cv_index' => array(
            'cv', 'edit', 'complete',
        ),
        'br_corporate_auth' => array(
            'login', 'logout',
        ),
        'br_corporate_index' => array(
            'index',
        ),
        'br_corporate_cv' => array(
            'downloadArchive', 'grouped', 'list', 'search',
        ),
    ),
);
