<?php

namespace CommonBundle\Command;

use RuntimeException;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Configuration value utilities.
 */
class Config extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
        parent::configure();

        $this->setName('common:config')
            ->setDescription('Configuration value utilities')
            ->addArgument('action', InputArgument::REQUIRED, 'The action to take')
            ->addArgument('key', InputArgument::REQUIRED, 'The name of the configuration value');
    }

    protected function invoke()
    {
        $key = $this->getArgument('key');
        $action = $this->getArgument('action');

        $config = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->find($key);

        switch ($action) {
            case 'get':
                if ($config === null) {
                    fwrite(STDERR, 'Configuration key "' . $key . '" doesn\'t exist' . PHP_EOL);

                    return 1;
                }

                $this->writeln($config->getValue(), true);

                return 0;

            case 'test':
                return $config === null ? 1 : 0;

            default:
                throw new RuntimeException('Invalid action: ' . $action);
        }
    }
}
