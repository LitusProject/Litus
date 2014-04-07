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
    'general' => array(
        'general_0' => array(
            'common_admin_index' => array(
                'action' => 'index',
                'title'  => 'Dashboard',
            ),
        ),
        'general_1' => array(
            'common_admin_cache'  => array('title' => 'Cache'),
            'common_admin_config' => array('title' => 'Configuration'),
        ),
        'general_2' => array(
            'common_admin_academic' => array('title' => 'Academics'),
            'common_admin_role'     => array('title' => 'Roles'),
        ),
        'general_3' => array(
            'common_admin_location' => array('title' => 'Locations'),
            'common_admin_unit'     => array('title' => 'Units')
        ),
    ),
);
