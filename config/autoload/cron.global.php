<?php

return array(
    'cron' => array(
        'jobs' => array(
            array(
                'command'  => 'php bin/console.php common:cleanup-sessions',
                'schedule' => '* * * * *',
            ),
            array(
                'command'  => 'php bin/console.php cudi:update-catalog -m',
                'schedule' => '0 2 * * *',
            ),
            array(
                'command'  => 'php bin/console.php cudi:expire-warning -m',
                'schedule' => '0 2 * * *',
            ),
            array(
                'command'  => 'php bin/console.php form:reminders -m',
                'schedule' => '0 2 * * *',
            ),
        ),
    ),
);
