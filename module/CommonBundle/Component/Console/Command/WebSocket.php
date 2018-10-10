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

namespace CommonBundle\Component\Console\Command;

abstract class WebSocket extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
        $name = strtolower($this->getCommandName());
        $module = $this->getModuleName();

        $this
            ->setName($module . ':' . $name . '-socket')
            ->setAliases(
                array(
                    'socket:' . $module . ':' . $name,
                )
            )
            ->addOption('run', 'r', null, 'Run the WebSocket')
            ->addOption('is-enabled', null, null, 'Checks whether the WebSocket is enabled')
            ->setDescription('Runs the ' . $name . ' websocket.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command runs the $name websocket.

Call
    php bin/console.php <info>%command.name%</info> <comment>--run</comment>
to run the socket.

Call
    php bin/console.php <info>%command.name%</info> <comment>--is-enabled</comment>
to check whether the socket is enabled (return value 0) or disabled (return value 1).
EOT
        );
    }

    protected function executeCommand()
    {
        if ($this->getOption('is-enabled')) {
            return $this->isSocketEnabled() ? 0 : 1;
        }

        if (!$this->getOption('run')) {
            $this->writeln('Please add -r or --run to run the socket');

            return 1;
        }

        $socket = $this->createSocket();
        if (null === $socket) {
            return 2;
        }

        $socket->process();
    }

    protected function getLogName()
    {
        return ucfirst($this->getCommandName()) . 'Socket';
    }

    /**
     * @return bool whether the socket is enabled
     */
    abstract protected function isSocketEnabled();

    /**
     * @return string the name of this websocket, used in the commands.
     */
    abstract protected function getCommandName();

    /**
     * @return \CommonBundle\Component\WebSocket\Server|null
     */
    abstract protected function createSocket();

    /**
     * @return string the name of the bundle of this websocket.
     */
    private function getModuleName()
    {
        $calledClass = get_called_class();
        $module = substr($calledClass, 0, strpos($calledClass, '\\', 1));

        return str_replace(array('bundle', 'module'), '', strtolower($module));
    }
}
