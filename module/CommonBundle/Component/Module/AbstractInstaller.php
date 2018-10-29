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

namespace CommonBundle\Component\Module;

use CommonBundle\Component\Acl\Acl;
use CommonBundle\Component\Console\Command;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\CacheTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\ConfigTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAware\DoctrineTrait;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait;
use CommonBundle\Entity\Acl\Action;
use CommonBundle\Entity\Acl\Resource;
use CommonBundle\Entity\Acl\Role;
use CommonBundle\Entity\General\Config as ConfigEntity;
use RuntimeException;

/**
 * Installs a module.
 */
abstract class AbstractInstaller implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    use CacheTrait;
    use ConfigTrait;
    use DoctrineTrait;

    /**
     * @var string the module name
     */
    private $module;

    /**
     * @var Command|null the command that executes the installer, if any
     */
    private $command;

    public function __construct()
    {
        $calledClass = static::class;

        $this->module = substr($calledClass, 0, strpos($calledClass, '\\', 1));
    }

    /**
     * Execute the installer
     *
     * @return void
     */
    public function install()
    {
        $configuration = $this->getModuleConfig();

        $this->preInstall();

        if (array_key_exists('configuration', $configuration)) {
            $this->write('Installing configuration ...');
            $this->installConfig($configuration['configuration']);
            $this->writeln(' done.', true);
        }

        if (array_key_exists('acl', $configuration)) {
            $this->write('Installing acl ...');
            $this->installAcl($configuration['acl']);
            $this->writeln(' done.', true);
        }

        if (array_key_exists('roles', $configuration)) {
            $this->write('Installing roles ...');
            $this->installRoles($configuration['roles']);
            $this->writeln(' done.', true);
        }

        $this->postInstall();
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

    /**
     * @param  string  $string
     * @param  boolean $raw
     * @return void
     */
    protected function write($string, $raw = false)
    {
        if ($this->command !== null) {
            $this->command->write($string, $raw);
        }
    }

    /**
     * @param  string  $string
     * @param  boolean $raw
     * @return void
     */
    protected function writeln($string, $raw = false)
    {
        if ($this->command !== null) {
            $this->command->writeln($string, $raw);
        }
    }

    /**
     * @param  Command $command
     * @return self
     */
    public function setCommand(Command $command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * @return array
     */
    private function getModuleConfig()
    {
        $config = $this->getConfig()['litus']['install'];
        $config = array_change_key_case($config);

        $key = strtolower($this->module);
        if (array_key_exists($key, $config)) {
            $moduleConfig = $config[$key];
        } elseif (array_key_exists(str_replace('bundle', '', $key), $config)) {
            $key = str_replace('bundle', '', $key);
            $moduleConfig = $config[$key];
        } else {
            throw new RuntimeException('Module ' . $this->module . ' does not have any configured installation files.');
        }

        return $moduleConfig;
    }

    /**
     * @param  string|array $config
     * @return array
     */
    private static function loadConfig($config)
    {
        if (is_array($config)) {
            return $config;
        }

        return require $config;
    }

    /**
     * Install the config values.
     *
     * @param array $config The configuration values
     */
    private function installConfig($config)
    {
        $config = self::loadConfig($config);

        foreach ($config as $item) {
            try {
                $entry = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->findOneByKey($item['key']);

                if ($entry === null) {
                    $entry = new ConfigEntity($item['key'], $item['value']);
                    $entry->setDescription($item['description']);

                    if (isset($item['published'])) {
                        $entry->setPublished($item['published']);
                    }

                    $this->getEntityManager()->persist($entry);
                } else {
                    $entry->setDescription($item['description']);
                }
            } catch (\Throwable $e) {
                $entry = new ConfigEntity($item['key'], $item['value']);
                $entry->setDescription($item['description']);

                if (isset($item['published'])) {
                    $entry->setPublished($item['published']);
                }

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
    private function installRoles($roles)
    {
        $roles = self::loadConfig($roles);

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

            if ($role === null) {
                $system = array_key_exists('system', $config) ? $config['system'] : false;

                $role = new Role(
                    $roleName,
                    $system,
                    $parents
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

                    if (!in_array($action, $role->getActions())) {
                        $role->addAction($action);
                    }
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
    private function installAcl($structure)
    {
        $structure = self::loadConfig($structure);

        foreach ($structure as $module => $routesArray) {
            $repositoryCheck = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\Acl\Resource')
                ->findOneByName($module);

            if ($repositoryCheck === null) {
                $moduleResource = new Resource($module);
                $this->getEntityManager()->persist($moduleResource);
            } else {
                $moduleResource = $repositoryCheck;
            }

            foreach ($routesArray as $route => $actions) {
                $repositoryCheck = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Acl\Resource')
                    ->findOneBy(array('name' => $route, 'parent' => $module));

                if ($repositoryCheck === null) {
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

                    if ($repositoryCheck === null) {
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

        if ($this->getCache() !== null && $this->getCache()->hasItem('acl')) {
            $this->getCache()->replaceItem(
                'acl',
                new Acl(
                    $this->getEntityManager()
                )
            );
        }
    }
}
