<?php

namespace CommonBundle\Command;

/**
 * Run all installers.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class InstallAll extends \CommonBundle\Component\Console\Command
{
    /**
     * @var string|null The name of the module currently being installed.
     */
    private $currentModule = null;

    protected function configure()
    {
        parent::configure();

        $this->setName('install:all')
            ->setDescription('Install all modules');
    }

    protected function invoke()
    {
        foreach ($this->getModules() as $module) {
            $this->installModule($module);
        }
    }

    protected function getLogName()
    {
        if ($this->currentModule !== null) {
            return $this->currentModule;
        }

        return parent::getLogName();
    }

    protected function getLogNameLength()
    {
        return max(
            array_map(
                function ($value) {
                    return strlen($value);
                },
                $this->getModules()
            )
        );
    }

    protected function getLogNameTag()
    {
        if ($this->currentModule !== null) {
            return 'fg=blue';
        }

        return parent::getLogNameTag();
    }

    private function getModules()
    {
        $config = $this->getConfig()['litus']['install'];

        // CommonBundle has to be first
        return array_merge(
            array('CommonBundle'),
            array_filter(
                array_keys($config),
                function ($v) {
                    return $v != 'CommonBundle';
                }
            )
        );
    }

    private function installModule($module)
    {
        $this->writeln('Installing <comment>' . $module . '</comment>');

        $this->currentModule = $module;

        $this->getServiceLocator()
            ->get($module . '\Component\Module\Installer')
            ->setCommand($this)
            ->install();

        $this->currentModule = null;
    }
}
