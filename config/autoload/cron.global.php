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
    'cron' => array(
        'jobs' => array(
            'common:gc' => array(
                'command'  => 'php bin/console.php common:cleanup-sessions',
                'schedule' => '* * * * *',
            ),
            'cudi:catalog-update' => array(
                'command'  => 'php bin/console.php cudi:update-catalog -m',
                'schedule' => '0 2 * * *',
            ),
            'cudi:expire-warning' => array(
                'command'  => 'php bin/console.php cudi:expire-warning -m',
                'schedule' => '0 2 * * *',
            ),
            'form:mail' => array(
                'command'  => 'php bin/console.php form:reminders -m',
                'schedule' => '0 2 * * *',
            )
        ),
    ),
);
