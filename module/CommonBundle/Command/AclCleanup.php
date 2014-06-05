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

/**
 * Cleanup of Acl
 */
class AclCleanup extends \CommonBundle\Component\Console\Command
{
    protected function configure()
    {
        $this
            ->setName('common:acl-cleanup')
            ->setDescription('Cleanup old acl actions and resources.')
            ->addOption('flush', 'f', null, 'Stores the result in the database.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command removes old acl actions and resources
EOT
        );
    }

    protected function executeCommand()
    {
        $removedEntities = false;

        $allActions = $this->_getAllActions();

        $currentActions = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Acl\Action')
            ->findAll();

        $roles = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Acl\Role')
            ->findAll();

        foreach($currentActions as $action) {
            $parent = $action->getResource()->getParent()->getName();
            $resource = $action->getResource()->getName();
            $actionName = $action->getName();
            if (isset($allActions[$parent][$resource]) && in_array($actionName, $allActions[$parent][$resource]))
                continue;

            $this->writeln('Removing action <comment>' . $parent . '.' . $resource . '.' . $actionName . '</comment>.');

            foreach($roles as $role)
                $role->removeAction($action);

            if ($this->getOption('flush'))
                $this->getEntityManager()->flush();

            $this->getEntityManager()->remove($action);
            $removedEntities = true;
        }

        $currentResources = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\Acl\Resource')
            ->findAll();

        foreach($currentResources as $resource) {
            if ($resource->getParent()) {
                if (isset($allActions[$resource->getParent()->getName()][$resource->getName()]))
                    continue;

                $this->writeln('Removing resource <comment>' . $resource->getParent()->getName() . '.' . $resource->getName() . '</comment>.');
            } else {
                if (isset($allActions[$resource->getName()]))
                    continue;

                $this->writeln('Removing resource <comment>' . $resource->getName() . '</comment>.');
            }

            $this->getEntityManager()->remove($resource);
            $removedEntities = true;
        }

        if ($this->getOption('flush')) {
            $this->write('Flushing entity manager...');
            $this->getEntityManager()->flush();
            $this->writeln(' done.', true);
        }

        if (!$removedEntities) {
            $this->writeln('Nothing removed, acl was clean.');
        }
    }

    protected function getLogName()
    {
        return 'AclCleanup';
    }

    private function _getModules()
    {
        $config = $this->getServiceLocator()
            ->get('Config');
        $config = $config['litus']['install'];

        // CommonBundle has to be first
        return array_merge(
            array('CommonBundle'),
            array_filter(array_keys($config), function ($v) { return $v != 'CommonBundle'; })
        );
    }

    private function _getAllActions()
    {
        $acl = array();
        $modules = $this->_getModules();

        foreach($modules as $module)
            $acl = array_merge($acl, $this->_getAclConfiguration($module));

        return $acl;
    }

    private function _getAclConfiguration($module)
    {
        $configuration = $this->getServiceLocator()->get('Config');
        $configuration = $configuration['litus']['install'];
        $configuration = array_change_key_case($configuration);

        if (isset($configuration[strtolower($module)]['acl']))
            return include $configuration[strtolower($module)]['acl'];

        return array();
    }
}
