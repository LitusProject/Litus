<?php

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
     * @var string
     */
    private $module;

    protected function configure()
    {
        parent::configure();

        $module = $this->getModule();
        $name = str_replace('bundle', '', strtolower($module));

        $this->module = $module;

        $this->setName('install:' . $name)
            ->setDescription('Install ' . $this->module);
    }

    protected function invoke()
    {
        $this->getServiceLocator()
            ->get($this->module . '\Component\Module\Installer')
            ->setCommand($this)
            ->install();
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
        return substr(static::class, 0, strpos(static::class, '\\', 1));
    }
}
