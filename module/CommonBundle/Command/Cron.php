<?php

namespace CommonBundle\Command;

use Cron\CronExpression;
use Ko\Process;
use Ko\ProcessManager;

/**
 * Run configured cron jobs.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Cron extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
        parent::configure();

        $this->setName('common:cron')
            ->setDescription('Run configured cron jobs');
    }

    protected function invoke()
    {
        $manager = new ProcessManager();
        $logFile = $this->getLogFile();

        $jobs = $this->getConfig()['cron']['jobs'];
        foreach ($jobs as $job) {
            $cron = CronExpression::factory($job['schedule']);
            if ($cron->isDue()) {
                $manager->fork(
                    function (Process $p) use ($job, $logFile) {
                        $command = sprintf(
                            '%s >> %s 2>&1',
                            $job['command'],
                            $logFile
                        );

                        exec($command);
                    }
                );
            }
        }

        $manager->wait();
    }

    private function getLogFile()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.cron_log');
    }
}
