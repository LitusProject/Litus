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

use Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    RuntimeException;

/**
 * AllInstallController calls all other installations.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class AllInstall extends \CommonBundle\Component\Console\Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('install:all')
            ->setAliases(array('install'))
            ->setDescription('Install all modules.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> installs all the modules.
EOT
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->_getModules() as $module)
            $this->_installModule($input, $output, $module);

        $output->writeln('Installation completed successfully!');
    }

    private function _getModules()
    {
        $config = $this->getServiceLocator()
            ->get('Config');
        $config = $config['litus']['install'];

        return array_merge(
            array('CommonBundle'),
            array_filter(array_keys($config), function ($v) { return $v != 'CommonBundle'; })
        );
    }

    private function _installModule(InputInterface $input, OutputInterface $output, $module)
    {
        $output->writeln('Installing module <comment>' . $module . '</comment>');

        $moduleName = str_replace('bundle', '', strtolower($module));

        $command = $this->getApplication()->find('install:' . $moduleName);

        if (null === $command)
            throw new RuntimeException('Unknown command install:' . $moduleName . ' for module ' . $module);

        $command->execute($input, $output);
    }
}
