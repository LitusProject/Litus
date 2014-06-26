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

namespace CommonBundle\Command;

use Symfony\Component\Console\Input\InputArgument;

/**
 * Performs garbage collection on the sessions.
 */
class Config extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
        $this
            ->setName('common:config')
            ->setDescription('Get configuration values.')
            ->addArgument('action', InputArgument::REQUIRED, 'the action to take (test|get)')
            ->addArgument('key',    InputArgument::REQUIRED, 'the name of the configuration value')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command gets or sets configuration values.

For <comment>test</comment> and <comment>get</comment>:
    The exit status is 0 if the configuration entry exists, 1 otherwise.
    <comment>test</comment> does not output anything, <comment>get</comment> outputs the value.
EOT
        );
    }

    protected function executeCommand()
    {
        $key = $this->getArgument('key');
        $action = $this->getArgument('action');

        $config = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->find($key);

        if ('get' == $action) {
            if (null === $config) {
                fwrite(STDERR, 'Configuration key "' . $key . '" doesn\'t exist' . PHP_EOL);

                return 1;
            } else {
                $this->writeln($config->getValue());

                return 0;
            }
        } elseif ('test' == $action) {
            return (null === $config) ? 1 : 0;
        } else {
            throw new \RuntimeException('Invalid action: ' . $action);
        }
    }

    protected function getLogName()
    {
        return false;
    }
}
