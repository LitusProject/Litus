<?php

namespace CommonBundle\Command;

use Ko\Process;
use Ko\ProcessManager;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * Start all WebSockets.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Sockets extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
        parent::configure();

        $this->setName('common:sockets')
            ->setDescription('Start all WebSockets');
    }

    protected function invoke()
    {
        $processManager = new ProcessManager();
        $logFile = fopen($this->getLogFile(), 'a', false);

        $commands = $this->getApplication()->all('socket');
        foreach ($commands as $command) {
            // Close parent connection to force reconnection in child process
            $this->getEntityManager()->getConnection()->close();

            $processManager->fork(
                function (Process $p) use ($command, $logFile) {
                    $command->run(
                        new StringInput(''),
                        new StreamOutput($logFile)
                    );
                }
            );
        }

        $processManager->wait();
    }

    private function getLogFile()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.sockets_log');
    }
}
