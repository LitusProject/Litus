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

namespace CommonBundle\Command;

/**
 * Cleanup of Acl
 */
class CleanupAcl extends \CommonBundle\Component\Console\Command
{
    /**
     * @return null
     */
    protected function configure()
    {
        $this->setName('common:cleanup-acl')
            ->setDescription('Cleanup old ACL actions and resources')
            ->addOption('flush', 'f', null, 'Store the result in the database');
    }

    /**
     * @return null
     */
    protected function invoke()
    {
        $allActions = $this->getAllActions();

        $currentActions = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Acl\Action')
            ->findAll();

        $roles = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Acl\Role')
            ->findAll();

        $removedEntities = false;
        foreach ($currentActions as $action) {
            $parent = $action->getResource()->getParent()->getName();
            $resource = $action->getResource()->getName();
            $actionName = $action->getName();
            if (isset($allActions[$parent][$resource]) && in_array($actionName, $allActions[$parent][$resource])) {
                continue;
            }

            $this->writeln('Removing action <comment>' . $parent . '.' . $resource . '.' . $actionName . '</comment>');

            foreach ($roles as $role) {
                $role->removeAction($action);
            }

            if ($this->getOption('flush')) {
                $this->getEntityManager()->flush();
            }

            $this->getEntityManager()->remove($action);
            $removedEntities = true;
        }

        $currentResources = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Acl\Resource')
            ->findAll();

        foreach ($currentResources as $resource) {
            if ($resource->getParent()) {
                if (isset($allActions[$resource->getParent()->getName()][$resource->getName()])) {
                    continue;
                }

                $this->writeln('Removing resource <comment>' . $resource->getParent()->getName() . '.' . $resource->getName() . '</comment>');
            } else {
                if (isset($allActions[$resource->getName()])) {
                    continue;
                }

                $this->writeln('Removing resource <comment>' . $resource->getName() . '</comment>');
            }

            $this->getEntityManager()->remove($resource);
            $removedEntities = true;
        }

        if ($this->getOption('flush')) {
            $this->write('Flushing entity manager...');
            $this->getEntityManager()->flush();
            $this->writeln(" <fg=green>\u{2713}</fg=green>", true);
        }

        if (!$removedEntities) {
            $this->writeln('Nothing removed, ACL was clean');
        }
    }

    /**
     * @return array
     */
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

    /**
     * @return array
     */
    private function getAllActions()
    {
        $acl = array();
        $modules = $this->getModules();

        foreach ($modules as $module) {
            $acl = array_merge($acl, $this->getAclConfig($module));
        }

        return $acl;
    }

    /**
     * @param  string $module
     * @return array
     */
    private function getAclConfig($module)
    {
        $config = $this->getConfig()['litus']['install'];
        $config = array_change_key_case($config);

        if (isset($config[strtolower($module)]['acl'])) {
            return include $config[strtolower($module)]['acl'];
        }

        return array();
    }
}
