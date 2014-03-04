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

namespace CommonBundle\Component\Console\Command;

abstract class Assetic extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
        $name = strtolower($this->getCommandName());

        $this
            ->setName('assetic:' . $name)
            ->setDescription('Performs Assetic ' . $name . '.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command performs an AsseticBundle $name.
EOT
        );
    }

    protected function executeCommand()
    {
        $controllerLoader = $this->getServiceLocator()->get('controllerloader');

        if (!$controllerLoader->has('AsseticBundle\Controller\Console')) {
            $this->writeln('<error>Error:</error> the assetic bundle is not configured properly.');

            return 1;
        }
        $asseticController = $controllerLoader->get('AsseticBundle\Controller\Console');

        $action = $this->getCommandName() . 'Action';
        if (!method_exists($asseticController, $action)) {
            $this->writeln('<error>Error:</error> unknown action ' . $action . ' on assetic bundle\'s ConsoleController');

            return 2;
        }

        $this->writeln('Performing ' . $this->getCommandName() . '...');
        $asseticController->$action();
        $this->writeln('done.');
    }

    protected function getLogName()
    {
        return 'Assetic' . ucfirst(strtolower($this->getCommandName()));
    }

    /**
     * @return string the name of this Assetic command.
     */
    abstract protected function getCommandName();
}
