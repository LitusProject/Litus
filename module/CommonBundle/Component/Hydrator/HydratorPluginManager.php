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

namespace CommonBundle\Component\Hydrator;

use RuntimeException,
    Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Manager for our hydrators
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class HydratorPluginManager extends \Zend\Stdlib\Hydrator\HydratorPluginManager
{
    public function get($name, $options = array(), $usePeeringServiceManagers = true)
    {
        if ($this->has($name))
            return parent::get($name, $options, $usePeeringServiceManagers);

        $hydratorName = $this->getHydratorName($name);
        if (!class_exists($hydratorName)) {
            throw new RuntimeException('Unknown hydrator: ' . $name);
        }

        $this->setInvokableClass($name, $hydratorName);

        return parent::get($name, $options, $usePeeringServiceManagers);
    }

    private function getHydratorName($name)
    {
        $parts = explode('\\', $name, 3);

        if ('Entity' !== $parts[1])
            return $name;
        $parts[1] = 'Hydrator';

        return implode('\\', $parts);
    }

    public function validatePlugin($plugin)
    {
        if ($plugin instanceof ServiceLocatorAwareInterface)
            $plugin->setServiceLocator($this->getServiceLocator());

        return parent::validatePlugin($plugin);
    }
}
