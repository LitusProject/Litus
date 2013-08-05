<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

return array(
    'zenddevelopertools' => array(
        'profiler' => array(
            'enabled'     => true,
            'strict'      => false,
            'flush_early' => false,
            'cache_dir'   => 'data/cache',
            'matcher'     => array(),
            'collectors'  => array(
                'config' => null,
                'db' => null,
            ),
        ),
        'toolbar' => array(
            'enabled'       => ('development' == getenv('APPLICATION_ENV')),
            'auto_hide'     => true,
            'position'      => 'bottom',
            'version_check' => false,
            'entries'       => array(),
        ),
    ),
);