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
    'shopbundle' => array(
        'shop' => array(
            'index', 'reserve', 'reservations',
        ),
        'shop_admin_shop_product' => array(
            'manage', 'add', 'edit', 'delete',
        ),
        'shop_admin_shop_salessession' => array(
            'manage', 'add', 'edit', 'delete', 'reservations',
        ),
        'shop_admin_shop_reservation' => array(
            'delete', 'noshow', 'manage',
        ),
        'shop_admin_shop_blacklist' => array(
            'manage', 'delete', 'add', 'togglepermission',
        ),
    ),
);
