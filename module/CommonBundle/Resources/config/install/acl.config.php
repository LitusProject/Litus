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
    'commonbundle' => array(
        'common_admin_academic' => array(
            'activate', 'add', 'delete', 'edit', 'manage', 'search', 'typeahead'
        ),
        'common_admin_auth' => array(
            'authenticate', 'login', 'logout', 'shibboleth'
        ),
        'common_admin_cache' => array(
            'manage', 'flush'
        ),
        'common_admin_config' => array(
            'edit', 'manage'
        ),
        'common_admin_index' => array(
            'index'
        ),
        'common_admin_location' => array(
            'add', 'edit', 'delete', 'geocoding', 'manage'
        ),
        'common_admin_person' => array(
            'typeahead'
        ),
        'common_admin_role' => array(
            'add', 'edit', 'delete', 'deleteMember', 'manage', 'members', 'prune'
        ),
        'common_admin_session' => array(
            'expire'
        ),
        'common_admin_unit' => array(
            'add', 'delete', 'deleteMember', 'edit', 'manage', 'members', 'prune'
        ),
        'common_account' => array(
            'activate', 'edit', 'index', 'passbook', 'saveStudies', 'saveSubjects', 'studies', 'subjects', 'uploadProfileImage'
        ),
        'common_session' => array(
            'manage', 'expire'
        ),
        'common_auth' => array(
            'login', 'logout', 'shibboleth'
        ),
        'common_index' => array(
            'index'
        ),
        'common_robots' => array(
            'index'
        ),
        'common_praesidium' => array(
            'overview'
        ),
    ),
);
