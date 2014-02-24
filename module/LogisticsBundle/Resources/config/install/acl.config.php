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
    'logisticsbundle' => array(
        'logistics_admin_driver' => array(
            'add', 'delete', 'edit', 'manage'
        ),
        'logistics_admin_piano_reservation' => array(
            'add', 'delete', 'edit', 'manage', 'old'
        ),
        'logistics_admin_van_reservation' => array(
            'add', 'delete', 'edit', 'manage', 'old'
        ),
        'logistics_admin_lease' => array(
            'add', 'delete', 'edit', 'manage'
        ),
        'logistics_index' => array(
            'add', 'delete', 'edit', 'export', 'fetch', 'index', 'move'
        ),
        'logistics_lease' => array(
            'availabilityCheck', 'history', 'index', 'show', 'typeahead'
        ),
        'logistics_auth' => array(
            'login', 'logout', 'shibboleth',
        ),
        'logistics_piano' => array(
            'index'
        ),
    ),
);
