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
    'shiftbundle' => array(
        'shift_admin_shift' => array(
            'add', 'delete', 'edit', 'export', 'manage', 'old', 'pdf', 'search'
        ),
        'shift_admin_shift_counter' => array(
            'delete', 'index', 'payed', 'payout', 'search', 'units', 'view'
        ),
        'shift_admin_shift_ranking' => array(
            'index'
        ),
        'shift_admin_shift_subscription' => array(
            'manage', 'delete',
        ),
        'shift' => array(
            'export', 'index', 'responsible', 'signOut', 'volunteer'
        ),
    ),
);
