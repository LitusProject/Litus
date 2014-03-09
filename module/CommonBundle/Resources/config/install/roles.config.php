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
    'guest' => array(
        'system' => true,
        'parents' => array(
        ),
        'actions' => array(
            'common_admin_auth' => array(
                'authenticate', 'login', 'logout', 'shibboleth'
            ),
            'common_auth' => array(
                'login', 'logout', 'shibboleth'
            ),
            'common_index' => array(
                'index'
            ),
            'common_account' => array(
                'activate'
            ),
            'common_robots' => array(
                'index'
            ),
        ),
    ),
    'student' => array(
        'system' => true,
        'parents' => array(
        ),
        'actions' => array(
            'common_account' => array(
                'edit', 'index', 'saveStudies', 'saveSubjects', 'studies', 'subjects',
            ),
        ),
    ),
);
