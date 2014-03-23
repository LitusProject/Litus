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
class GetConfig extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
        $this
            ->setName('config:get')
            ->setDescription('Get configuration values.')
            ->addOption('quiet', 'q', null, 'don\'t output anything')
            ->addArgument('key', InputArgument::REQUIRED, 'the name of the configuration value')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command gets configuration values.

The exit status is 0 if the configuration entry exists, 1 otherwise.

If the <comment>--quiet</comment> flag is given, the output of this command will
be empty. The status code will still be set, so this can be used to test the
existence of the configuration entry.
EOT
        );
    }

    protected function executeCommand()
    {
        $config = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->find($this->getArgument('key'));

        if (null === $config) {
            if (!$this->getOption('quiet'))
                fwrite(STDERR, 'Configuration key "' . $this->getArgument('key') . '" doesn\'t exist' . PHP_EOL);

            return 1;
        }

        if (!$this->getOption('quiet'))
            echo $config->getValue() . PHP_EOL;

        return 0;
    }

    protected function getLogName()
    {
        return '';
    }
}
