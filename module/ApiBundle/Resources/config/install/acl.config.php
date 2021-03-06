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
    'apibundle' => array(
        'api_admin_key' => array(
            'add', 'delete', 'edit', 'manage',
        ),
        'api_auth' => array(
            'getCorporate', 'getPerson',
        ),
        'api_calendar' => array(
            'activeEvents', 'poster',
        ),
        'api_config' => array(
            'entries',
        ),
        'api_cudi' => array(
            'articles', 'book', 'bookings', 'cancelBooking', 'currentSession', 'openingHours', 'signIn', 'signInStatus',
        ),
        'api_door' => array(
            'getRules', 'log',
        ),
        'api_mail' => array(
            'getAliases', 'getLists', 'getListsArchive',
        ),
        'api_member' => array(
            'all',
        ),
        'api_news' => array(
            'all',
        ),
        'api_oauth' => array(
            'authorize', 'shibboleth', 'token',
        ),
        'api_shift' => array(
            'active', 'responsible', 'volunteer', 'signOut',
        ),
    ),
);
