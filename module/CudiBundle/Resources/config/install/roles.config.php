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
        'actions' => array(
            'cudi_prof_auth' => array(
                'login', 'logout', 'shibboleth',
            ),
            'cudi_opening_hour' => array(
                'week',
            ),
        ),
    ),
    'supplier' => array(
        'system' => true,
        'parents' => array(
            'guest',
        ),
        'actions' => array(
            'cudi_supplier_article' => array(
                'manage',
            ),
            'cudi_supplier_index' => array(
                'index',
            ),
        ),
    ),
    'prof' => array(
        'system' => true,
        'parents' => array(
            'guest',
        ),
        'actions' => array(
            'cudi_prof_article' => array(
                'add', 'addFromSubject', 'edit', 'manage', 'typeahead',
            ),
            'cudi_prof_article_mapping' => array(
                'activate', 'add', 'delete',
            ),
            'cudi_prof_article_comment' => array(
                'delete', 'manage',
            ),
            'cudi_prof_subject_comment' => array(
                'delete', 'manage',
            ),
            'cudi_prof_file' => array(
                'delete', 'download', 'manage', 'upload',
            ),
            'cudi_prof_index' => array(
                'index',
            ),
            'cudi_prof_prof' => array(
                'add', 'delete', 'typeahead',
            ),
            'cudi_prof_subject' => array(
                'manage', 'subject', 'typeahead',
            ),
        ),
    ),
    'student' => array(
        'system' => true,
        'parents' => array(
            'guest',
        ),
        'actions' => array(
            'cudi_booking' => array(
                'book', 'bookSearch', 'cancel', 'keepUpdated', 'search', 'view',
            ),
        ),
    ),
);
