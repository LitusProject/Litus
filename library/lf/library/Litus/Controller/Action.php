<?php

namespace Litus\Controller;

use \Litus\Acl\Acl;
use \Litus\Application\Resource\Doctrine as DoctrineResource;
use \Litus\Authentication\Action as AuthenticationAction;
use \Litus\Authentication\Adapter\Doctrine as DoctrineAdapter;
use \Litus\Authentication\Authentication;
use \Litus\Authentication\Service\SessionCookie as SessionCookieService;
use \Zend\Layout\Layout;

use \Zend\Controller\Request\AbstractRequest as Request;
use \Zend\Registry;

class Action extends \Zend\Controller\Action implements AuthenticationAware, DoctrineAware
{
    /**
     * @var \Doctrine\ORM\EntityManager The Entity Manager
     */
    private static $_entityManager = null;

    /**
     * @var \Litus\Authentication\Authentication The Authentication instance
     */
    private static $_authentication = null;

    /**
     * Called before an action is dispatched by Zend\Controller\Dispatcher.
     *
     * @throws \Litus\Controller\Exception\HasNoAccessException
     * @return void
     */
    public function preDispatch()
    {
        $authenticatedUser = 'Guest';
        /**
        if ($this->hasAccess()) {
            if ($this->getAuthentication()->isAuthenticated())
                $authenticatedUser = $this->getAuthentication()->getPersonObject()->getFirstName();
        } else {
            if (!$this->getAuthentication()->isAuthenticated()) {
                if ('auth' != $this->getRequest()->getControllerName() && 'login' != $this->getRequest()->getActionName())
                    $this->_redirect('/admin/auth/login');
            } else {
                throw new Exception\HasNoAccessException(
                    'You do not have sufficient permissions to access this resource'
                );
            }
        }
        **/
        $this->view->authenticatedUser = $authenticatedUser;
    }

    /**
     * Called after an action is dispatched by Zend\Controller\Dispatcher.
     *
     * @return void
     */
    public function postDispatch()
    {
        $this->getEntityManager()->flush();
    }

    /**
     * Singleton implementation for the Entity Manager, retrieved
     * from the Zend Registry.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (null === self::$_entityManager) {
            self::$_entityManager = Registry::get(DoctrineResource::REGISTRY_KEY);
        }
        return self::$_entityManager;
    }

    /**
     * Returns the Authentication instance
     *
     * @return \Litus\Authentication\Authentication
     */
    public function getAuthentication()
    {
        if (null === self::$_authentication) {
            self::$_authentication = new Authentication(
                new DoctrineAdapter('\Litus\Entity\Users\Person', 'username'),
                new SessionCookieService('litus')
            );
        }
        return self::$_authentication;
    }

    /**
     * This method verifies whether or not there is an active authentication session,
     * and if not, checks whether guest have access to this resource.
     *
     * @return bool
     */
    public function hasAccess()
    {
        $acl = new Acl();
        $request = $this->getRequest();

        return $acl->getAcl()->isAllowed(
            $this->getAuthentication()->isAuthenticated() ?
                    $this->getAuthentication()->getPersonObject()->getRole()->getName() : 'guest',
            $request->getModuleName() . '.' . $request->getControllerName(),
            $request->getActionName()
        );
    }
}
