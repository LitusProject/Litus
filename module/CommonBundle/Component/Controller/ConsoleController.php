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

namespace CommonBundle\Component\Controller;

use Zend\Mvc\MvcEvent,
    Zend\Console\Request as ConsoleRequest,
    RuntimeException;

/**
 * We extend the basic Zend controller to simplify database access and to check if we're truly
 * in the console.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class ConsoleController extends AbstractActionController
{
    /**
     * Execute the request.
     *
     * @param \Zend\Mvc\MvcEvent $e The MVC event
     * @return array
     */
    public function onDispatch(MvcEvent $e)
    {
        if (!($this->getRequest() instanceof ConsoleRequest))
            throw new RuntimeException('Can only be called from the console!');

        $result = parent::onDispatch($e);

        $e->setResult($result);
        return $result;
    }

    /**
     * @return \Zend\Console\Console
     */
    public function getConsole()
    {
        return $this->getServiceLocator()->get('Console');
    }
}
