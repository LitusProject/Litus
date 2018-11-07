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

namespace CommonBundle\Command;

use Cron\CronExpression;

/**
 * Run configured cron jobs.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Cron extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
        $this
            ->setName('common:cron')
            ->setDescription('Run configured cron jobs')
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command runs all configured cron jobs.
EOT
            );
    }

    protected function executeCommand()
    {
        $logFile = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.cron_log');

        $jobs = $this->getConfig()['cron']['jobs'];
        foreach ($jobs as $job) {
            $cron = CronExpression::factory($job['schedule']);
            if ($cron->isDue()) {
                exec(sprintf('%s 1>> "%s" 2>&1 &', $job['command'], $logFile));
            }
        }
    }

    protected function getLogName()
    {
        return 'Cron';
    }
}
