<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\Controller\ActionController;

use CommonBundle\Component\Acl\Acl,
    CommonBundle\Entity\Acl\Action as AclAction,
    CommonBundle\Entity\Acl\Role,
    CommonBundle\Entity\Acl\Resource,
    CommonBundle\Entity\General\Config,
    Exception,
    Zend\View\Model\ViewModel;

/**
 * This abstract function should be implemented by all controller that want to provide
 * installation functionality for a bundle.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
abstract class InstallController extends AdminController
{
    /**
     * Running all installation methods.
     *
     * @return void
     */
    public function indexAction()
    {
        $this->initConfig();
        $this->initAcl();

        return new ViewModel(
            array(
                'installerReady' => true,
            )
        );
    }

    /**
     * Initiliazes all configuration values for the bundle.
     *
     * @return void
     */
    abstract protected function initConfig();

    /**
     * Initializes the ACL tree for the bundle.
     *
     * @return void
     */
    abstract protected function initAcl();

    /**
     * Install the config values
     *
     * @param array $config The configuration values
     */
    protected function installConfig($config)
    {
        foreach($config as $item) {
            try {
                $value = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue($item['key']);
                if (null === $value) {
                    $config = new Config($item['key'], $item['value']);
                    $config->setDescription($item['description']);

                    $this->getEntityManager()->persist($config);
                }
            } catch(\Exception $e) {
                $config = new Config($item['key'], $item['value']);
                $config->setDescription($item['description']);

                $this->getEntityManager()->persist($config);
            }
        }

        $this->getEntityManager()->flush();
    }

    /**
     * Install the roles for the Acl
     *
     * @param array $roles
     */
    protected function installRoles($roles = array())
    {
        foreach($roles as $roleName => $config) {
            $role = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\Acl\Role')
                ->findOneByName($roleName);

            $parents = array();
            if (isset($config['parents'])) {
                foreach($config['parents'] as $name) {
                    $parents[] = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\Acl\Role')
                        ->findOneByName($name);
                }
            }

            if (null === $role) {
                $role = new Role(
                    $roleName, $config['system'], $parents
                );

                $this->getEntityManager()->persist($role);
            } elseif(!empty($config['parents'])) {
                $role->setParents($parents);
            }

            foreach ($config['actions'] as $resource => $actions) {
                foreach($actions as $action) {
                    $action = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\Acl\Action')
                        ->findOneBy(array('name' => $action, 'resource' => $resource));

                    if (!in_array($action, $role->getActions()))
                        $role->allow($action);
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
    protected function installAcl($structure = array())
    {
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
                        $actionResource = new AclAction(
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
