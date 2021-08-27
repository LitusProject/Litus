<?php

return array(
    'install_all'    => CommonBundle\Command\InstallAll::class,
    'install_common' => CommonBundle\Command\Install::class,

    'common_cleanup_acl'      => CommonBundle\Command\CleanupAcl::class,
    'common_cleanup_sessions' => CommonBundle\Command\CleanupSessions::class,
    'common_config'           => CommonBundle\Command\Config::class,
    'common_cron'             => CommonBundle\Command\Cron::class,
    'common_destroy_account'  => CommonBundle\Command\DestroyAccount::class,
    'common_sockets'          => CommonBundle\Command\Sockets::class,
);
