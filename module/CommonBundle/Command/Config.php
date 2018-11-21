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

use RuntimeException;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Configuration value utilities.
 */
class Config extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
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
