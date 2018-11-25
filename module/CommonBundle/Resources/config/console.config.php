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
    'install_all'    => CommonBundle\Command\InstallAll::class,
    'install_common' => CommonBundle\Command\Install::class,

    'common_cleanup_acl'      => CommonBundle\Command\CleanupAcl::class,
    'common_cleanup_sessions' => CommonBundle\Command\CleanupSessions::class,
    'common_config'           => CommonBundle\Command\Config::class,
    'common_cron'             => CommonBundle\Command\Cron::class,
    'common_destroy_account'  => CommonBundle\Command\DestroyAccount::class,
    'common_sockets'          => CommonBundle\Command\Sockets::class,
);
