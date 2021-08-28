<?php

namespace CudiBundle\Component\Controller;

use CommonBundle\Component\Controller\Exception\HasNoAccessException;
use CudiBundle\Entity\User\Person\Supplier;
use Laminas\Mvc\MvcEvent;

/**
 * We extend the CommonBundle controller to check a valid user is logged in.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SupplierController extends \CommonBundle\Component\Controller\ActionController
{
    /**
     * Execute the request.
     *
     * @param  MvcEvent $e The MVC event
     * @return array
     * @throws HasNoAccessException The user does not have permissions to access this resource
     */
    public function onDispatch(MvcEvent $e)
    {
        $result = parent::onDispatch($e);

        $result->supplier = $this->getSupplierEntity();
        $result->loginForm = $this->getForm('common_auth_login')
            ->setAttribute('class', '')
            ->setAttribute(
                'action',
                $this->url()->fromRoute(
                    'cudi_supplier_auth',
                    array(
                        'action' => 'login',
                    )
                )
            );

        $result->organizationUrl = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('organization_url');

        $e->setResult($result);

        return $result;
    }

    /**
     * We need to be able to specify all required authentication information,
     * which depends on the part of the site that is currently being used.
     *
     * @return array
     */
    public function getAuthenticationHandler()
    {
        return array(
            'action'         => 'index',
            'controller'     => 'common_index',

            'auth_route'     => 'cudi_supplier_index',
            'redirect_route' => 'cudi_supplier_index',
        );
    }

    /**
     * Returns the supplier for the user that is currently logged in.
     *
     * @return Supplier
     */
    protected function getSupplierEntity()
    {
        if ($this->getAuthentication()->isAuthenticated()) {
            $person = $this->getAuthentication()->getPersonObject();

            if ($person instanceof Supplier) {
                return $person;
            }
        }

        throw new HasNoAccessException('You do not have sufficient permissions to access this resource');
    }
}
