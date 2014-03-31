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

use CommonBundle\Component\Acl\Acl,
    CommonBundle\Entity\Acl\Action,
    CommonBundle\Entity\Acl\Role,
    CommonBundle\Entity\Acl\Resource,
    CommonBundle\Entity\General\Config,
    RuntimeException;

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
        $module = $this->_getModule();

        $this
            ->setName('install:' . str_replace(array('bundle', 'module'), '', strtolower($module)))
            ->setDescription('Install the ' . $module . '.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command installs the $module module.
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
        $this->module = $this->_getModule();

        $configuration = $this->_getConfiguration();

        $this->preInstall();

        if (array_key_exists('configuration', $configuration)) {
            $this->write('Installing configuration ...');
            $this->_installConfig($configuration['configuration']);
            $this->writeln(' done.', true);
        }

        if (array_key_exists('acl', $configuration)) {
            $this->write('Installing acl ...');
            $this->_installAcl($configuration['acl']);
            $this->writeln(' done.', true);
        }

        if (array_key_exists('roles', $configuration)) {
            $this->write('Installing roles ...');
            $this->_installRoles($configuration['roles']);
            $this->writeln(' done.', true);
        }

        $this->postInstall();
    }

    protected function getLogName()
    {
        return $this->_getModule();
    }

    protected function getLogNameTag()
    {
        return 'fg=blue';
    }

    private function _getModule()
    {
        $calledClass = get_called_class();

        return substr($calledClass, 0, strpos($calledClass, '\\', 1));
    }

    private function _getConfiguration()
    {
        $configuration = $this->getServiceLocator()->get('Config');
        $configuration = $configuration['litus']['install'];
        $configuration = array_change_key_case($configuration);

        $key = strtolower($this->module);
        if (array_key_exists($key, $configuration)) {
            $configuration = $configuration[$key];
        } elseif (array_key_exists(str_replace('bundle', '', $key), $configuration)) {
            $key = str_replace('bundle', '', $key);
            $configuration = $configuration[$key];
        } else {
            throw new RuntimeException('Module ' . $this->module . ' does not have any configured installation files.');
        }

        return $configuration;
    }

    /**
     * Called prior to installation of the module.
     *
     * @return void
     */
    protected function preInstall()
    {
    }

    /**
     * Called after the installation of the module.
     *
     * @return void
     */
    protected function postInstall()
    {
    }

    private static function _loadConfig($config)
    {
        if (is_array($config))
            return $config;
        return require $config;
    }

    /**
     * Install the config values
     *
     * @param array $config The configuration values
     */
    private function _installConfig($config)
    {
        $config = self::_loadConfig($config);

        foreach ($config as $item) {
            try {
                $entry = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->findOneByKey($item['key']);

                if (null === $entry) {
                    $entry = new Config($item['key'], $item['value']);
                    $entry->setDescription($item['description']);

                    $this->getEntityManager()->persist($entry);
                } else {
                    $entry->setDescription($item['description']);
                }
            } catch (\Exception $e) {
                $entry = new Config($item['key'], $item['value']);
                $entry->setDescription($item['description']);

                $this->getEntityManager()->persist($entry);
            }
        }

        $this->getEntityManager()->flush();
    }

    /**
     * Install the roles for the Acl
     *
     * @param array $roles
     */
    private function _installRoles($roles)
    {
        $roles = self::_loadConfig($roles);

        foreach ($roles as $roleName => $config) {
            $role = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\Acl\Role')
                ->findOneByName($roleName);

            $parents = array();
            if (isset($config['parents'])) {
                foreach ($config['parents'] as $name) {
                    $parents[] = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\Acl\Role')
                        ->findOneByName($name);
                }
            }

            if (null === $role) {
                $system = array_key_exists('system', $config) ? $config['system'] : false;

                $role = new Role(
                    $roleName, $system, $parents
                );

                $this->getEntityManager()->persist($role);
            } elseif (!empty($config['parents'])) {
                $role->setParents($parents);
            }

            foreach ($config['actions'] as $resource => $actions) {
                foreach ($actions as $action) {
                    $action = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\Acl\Action')
                        ->findOneBy(array('name' => $action, 'resource' => $resource));

                    if (!in_array($action, $role->getActions()))
                        $role->addAction($action);
                }
            }

            $this->getEntityManager()->flush();
        }
    }

    /**
     * Install the structure for the Acl
     *
     * @param array $structure
     */
    private function _installAcl($structure)
    {
        $structure = self::_loadConfig($structure);

        foreach ($structure as $module => $routesArray) {
            $repositoryCheck = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\Acl\Resource')
                ->findOneByName($module);

            if (null === $repositoryCheck) {
                $moduleResource = new Resource($module);
                $this->getEntityManager()->persist($moduleResource);
            } else {
                $moduleResource = $repositoryCheck;
            }

            foreach ($routesArray as $route => $actions) {
                $repositoryCheck = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Acl\Resource')
                    ->findOneBy(array('name' => $route, 'parent' => $module));

                if (null === $repositoryCheck) {
                    $routeResource = new Resource(
                        $route,
                        $moduleResource
                    );

                    $this->getEntityManager()->persist($routeResource);
                } else {
                    $routeResource = $repositoryCheck;
                }

                foreach ($actions as $action) {
                    $repositoryCheck = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\Acl\Action')
                        ->findOneBy(array('name' => $action, 'resource' => $route));

                    if (null === $repositoryCheck) {
                        $actionResource = new Action(
                            $action,
                            $routeResource
                        );

                        $this->getEntityManager()->persist($actionResource);
                    }
                }
            }
        }
        $this->getEntityManager()->flush();

        if (null !== $this->getCache() && $this->getCache()->hasItem('acl')) {
            $this->getCache()->replaceItem(
                'acl',
                new Acl(
                    $this->getEntityManager()
                )
            );
        }
    }
}
