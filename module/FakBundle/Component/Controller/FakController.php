<?php

namespace FakBundle\Component\Controller;

use Laminas\Mvc\MvcEvent;

class FakController extends \CommonBundle\Component\Controller\ActionController
{
    /**
     * Execute the request.
     *
     * @param MvcEvent $e The MVC event
     * @return array
     */
    public function onDispatch(MvcEvent $e)
    {
        $result = parent::onDispatch($e);

        $e->setResult($result);

        return $result;
    }

    /**
     * @return array
     */
    public function getAuthenticationHandler()
    {
        return array(
            'action'         => 'common_auth',
            'controller'     => 'login',

            'auth_route'     => 'common_auth',
            'redirect_route' => 'fak_admin_scan_index',
        );
    }
}
