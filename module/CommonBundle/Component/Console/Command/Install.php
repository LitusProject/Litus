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

/**
 * This abstract function should be implemented by all controller that want to provide
 * installation functionality for a bundle.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
abstract class Install extends \CommonBundle\Component\Console\Command
{
    /**
     * @var string the module name
     */
    private $module;

    protected function configure()
    {
        $this->module = $this->getModule();

        $this
            ->setName('install:' . str_replace(array('bundle', 'module'), '', strtolower($this->module)))
            ->setDescription('Install the ' . $this->module . '.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command installs the $this->module module.
EOT
        );
    }

    /**
     * Running all installation methods.
     *
     * @return void
     */
    protected function executeCommand()
    {
        $installer = $this->getServiceLocator()
            ->get('litus.install.' . $this->module);

        $installer->setCommand($this);

        $installer->install();
    }

    protected function getLogName()
    {
        return $this->module;
    }

    protected function getLogNameTag()
    {
        return 'fg=blue';
    }

    private function getModule()
    {
        $calledClass = get_called_class();

        return substr($calledClass, 0, strpos($calledClass, '\\', 1));
    }
}
