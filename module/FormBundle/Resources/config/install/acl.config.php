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

return array(
    'formbundle' => array(
        'form_admin_form' => array(
            'add', 'delete', 'edit', 'manage', 'old', 'clear'
        ),
        'form_admin_group' => array(
            'add', 'delete', 'deleteForm', 'edit', 'forms', 'manage', 'old', 'sort',
        ),
        'form_admin_form_field' => array(
            'add', 'delete', 'edit', 'manage', 'sort',
        ),
        'form_admin_form_viewer' => array(
            'add', 'delete', 'manage',
        ),
        'form_admin_group_viewer' => array(
            'add', 'delete', 'manage',
        ),
        'form_view' => array(
            'doodle', 'downloadFile', 'edit', 'index', 'view', 'saveDoodle',
        ),
        'form_group' => array(
            'view',
        ),
        'form_manage' => array(
            'add', 'delete', 'doodle', 'doodleAdd', 'download', 'downloadFile', 'downloadFiles', 'edit', 'index', 'view',
        ),
        'form_manage_group' => array(
            'index', 'view',
        ),
        'form_manage_mail' => array(
            'send',
        ),
        'form_manage_auth' => array(
            'login', 'logout', 'shibboleth',
        ),
    ),
);
