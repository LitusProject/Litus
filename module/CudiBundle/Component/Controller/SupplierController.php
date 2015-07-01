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

namespace CudiBundle\Component\Controller;

use CommonBundle\Component\Controller\Exception\HasNoAccessException,
    CudiBundle\Entity\User\Person\Supplier,
    Zend\Mvc\MvcEvent;

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
     * @param  MvcEvent             $e The MVC event
     * @return array
     * @throws HasNoAccessException The user does not have permissions to access this resource
     */
    public function onDispatch(MvcEvent $e)
    {
        $result = parent::onDispatch($e);

        $result->supplier = $this->getSupplierEntity();
        $result->loginForm = $this->getForm('common_auth_login')
            ->setAttribute('class', '')
            ->setAttribute('action', $this->url()->fromRoute(
                'cudi_supplier_auth',
                array(
                    'action' => 'login',
                )
            ));

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
