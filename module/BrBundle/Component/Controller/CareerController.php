<?php

namespace BrBundle\Component\Controller;

use Laminas\Mvc\MvcEvent;

/**
 * We extend the CommonBundle controller.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class CareerController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    /**
     * Execute the request.
     *
     * @param  \Laminas\Mvc\MvcEvent $e The MVC event
     * @return array
     * @throws \CommonBundle\Component\Controller\Exception\HasNoAccessException The user does not have permissions to access this resource
     */
    public function onDispatch(MvcEvent $e)
    {
        $result = parent::onDispatch($e);

        $result->currentAcademicYear = $this->getCurrentAcademicYear();

        $e->setResult($result);

        return $result;
    }
}
