<?php

namespace Litus\Controller;

use \Litus\Acl\Acl;
use \Litus\Application\Resource\Doctrine as DoctrineResource;
use \Litus\Authentication\Action as AuthenticationAction;
use \Litus\Authentication\Adapter\Doctrine as DoctrineAdapter;
use \Litus\Authentication\Authentication;
use \Litus\Authentication\Service\Doctrine as DoctrineService;
use \Litus\Util\File;

use \Zend\Controller\Front;
use \Zend\Controller\Request\AbstractRequest as Request;
use \Zend\Layout\Layout;
use \Zend\Paginator\Paginator;
use \Zend\Paginator\Adapter\ArrayAdapter;
use \Zend\Registry;
use \Zend\View\Helper\PaginationControl;

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
     * This method will initialize our action.
     *
     * @return void
     */
    public function init()
    {
        $this->view->resolver()->addPath(
            File::getRealFilename(
                Front::getInstance()->getModuleDirectory($this->getRequest()->getModuleName()) . '/views'
            )
        );
    }

    /**
     * Called before an action is dispatched by Zend\Controller\Dispatcher.
     *
     * @throws \Litus\Controller\Exception\HasNoAccessException
     * @return void
     */
    public function preDispatch()
    {
        $this->view->startExecutionTime = microtime(true);

        $authenticatedUser = 'Guest';
        $this->getAuthentication()->authenticate();
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
        $this->view->authenticatedUser = $authenticatedUser;
    }

    /**
     * Called after an action is dispatched by Zend\Controller\Dispatcher.
     *
     * @return void
     */
    public function postDispatch()
    {
        $this->view->doctrineUnitOfWork = $this->getEntityManager()->getUnitOfWork()->size();
        $this->view->flushResult = $this->_flush();
    }

    /**
     * Flushes the entity manager and then redirects to the given url, not requiring any absolute URI's.
     *
     * @param string $action The action we want to redirect to
     * @param string $controller The controller we want to redirect to
     * @param string $module The module we want to redirect to
     * @param array $params Any additional params that should be passed
     * @return void
     */
    protected function _redirect($action, $controller = null, $module = null, array $params = array())
    {
        $this->view->flushResult = $this->_flush();

        if ($this->view->flushResult)
            $this->broker('redirector')->gotoSimple($action, $controller, $module, $params);
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
     * Flushes the entity manager and catches ORM exceptions, which is then stored in a view variable.
     *
     * @return bool
     */
    protected function _flush()
    {
        if ('development' == getenv('APPLICATION_ENV'))
            $this->getEntityManager()->flush();
        else {
            try {
                $this->getEntityManager()->flush();
            }
            catch (\PDOException $e) {
                return false;
            }
        }

        return true;
    }

    /**
     * Does some initialization work for asynchronously requested actions.
     *
     * @return void
     */
    protected function _initAjax()
    {
        if (!$this->getRequest()->isXmlHttpRequest())
            throw new \Litus\Controller\Request\Exception\NoXmlHttpRequestException();
        $this->broker('viewRenderer')->setNoRender();
    }

    /**
     * Create a paginator for a given entity.
     *
     * @param string $entity The name of the entity that should be paginated
     * @return \Zend\Paginator\Paginator
     */
    protected function _createPaginator($entity)
    {
        $paginator = new Paginator(
            new ArrayAdapter(
                $this->getEntityManager()->getRepository($entity)->findAll()
            )
        );
        $paginator->setItemCountPerPage(25);
        $paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));

        return $paginator;
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
                new DoctrineAdapter('Litus\Entity\Users\Person', 'username'),
                new DoctrineService('Litus\Entity\Users\Session', 2678400)
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
        // Making it easier to develop new actions and controllers, without all the ACL hassle
        if ('development' == getenv('APPLICATION_ENV'))
            return true;

        $acl = new Acl();
        $request = $this->getRequest();

        if ($this->getAuthentication()->isAuthenticated()) {
            foreach ($this->getAuthentication()->getPersonObject()->getRoles() as $role) {
                if ($acl->getAcl()->isAllowed(
                    $role->getName(),
                    $request->getModuleName() . '.' . $request->getControllerName(),
                    $request->getActionName()
                )
                ) {
                    return true;
                }
            }

            return false;
        } else {
            return $acl->getAcl()->isAllowed(
                'guest',
                $request->getModuleName() . '.' . $request->getControllerName(),
                $request->getActionName()
            );
        }
    }
}
