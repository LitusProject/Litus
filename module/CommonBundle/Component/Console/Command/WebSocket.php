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
        $module = $this->getModuleName();
        $name = strtolower($this->getCommandName());

        $this
            ->setName('socket:' . $module . ':' . $name)
            ->setDescription('Starts the ' . $name . ' WebSocket')
            ->addOption('is-enabled', null, null, 'Checks whether the WebSocket is enabled')
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command starts the $name WebSocket.

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

        $socket = $this->createSocket();
        if ($socket === null) {
            return 1;
        }

        $socket->process();
    }

    /**
     * @return string The name of this WebSocket, used in the commands
     */
    abstract protected function getCommandName();

    /**
     * @return boolean whether the socket is enabled
     */
    abstract protected function isSocketEnabled();

    /**
     * @return \CommonBundle\Component\WebSocket\Server|null
     */
    abstract protected function createSocket();

    /**
     * @return string The name of the bundle of this WebSocket
     */
    private function getModuleName()
    {
        $calledClass = static::class;
        $module = substr($calledClass, 0, strpos($calledClass, '\\', 1));

        return str_replace(array('bundle', 'module'), '', strtolower($module));
    }
}
