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

namespace CommonBundle\Controller\Console;

use Zend\Mvc\MvcEvent,
    Zend\View\Model\ViewModel,
    RuntimeException;

/**
 * AllInstallController calls all other installations.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class AllInstallController extends \CommonBundle\Component\Controller\ConsoleController
{
    public function indexAction()
    {
        foreach ($this->_getModules() as $module)
            $this->_installModule($module);

        return "Installation completed successfully!\n";
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

    private function _installModule($module)
    {
        echo('Installing module ' . $module . "\n");

        $controllerLoader = $this->getServiceLocator()->get('controllerloader');

        $moduleName = str_replace('bundle', '', strtolower($module));

        if (!($controllerLoader->has($moduleName . '_install')))
            throw new RuntimeException('Unknown controller ' . $moduleName . '_install for module ' . $module);

        $controller = $controllerLoader->get($moduleName . '_install');

        $controller->setServiceLocator($this->getServiceLocator());
        $controller->setEvent(clone $this->getEvent());
        return $controller->dispatch($this->getRequest());
    }
}
