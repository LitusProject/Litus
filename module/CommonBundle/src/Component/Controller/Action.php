<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */
 
namespace CommonBundle\Component\Controller;

use CommonBundle\Component\Acl\Acl,
 	CommonBundle\Component\Authentication\Action as AuthenticationAction,
	CommonBundle\Component\Authentication\Adapter\Doctrine as DoctrineAdapter,
	CommonBundle\Component\Authentication\Authentication,
	CommonBundle\Component\Authentication\Service\Doctrine as DoctrineService,
	CommonBundle\Component\Util\File,
	Zend\Controller\Front,
	Zend\Controller\Request\AbstractRequest as Request,
	Zend\Cache\StorageFactory,
	Zend\Layout\Layout,
	Zend\Paginator\Paginator,
	Zend\Paginator\Adapter\ArrayAdapter,
	Zend\Registry,
	Zend\View\Helper\PaginationControl;

/**
 * We extend the basic Zend controller to simplify database access, authentication
 * and some other common functionality
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Action extends \Zend\Mvc\Controller\ActionController implements AuthenticationAware, DoctrineAware
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
	 * @var array The flashmessages
	 */
	private $_flashMessages = null;

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
            if ($this->getAuthentication()->isAuthenticated()) {
                $authenticatedUser = $this->getAuthentication()->getPersonObject()->getFirstName();

                if ('auth' == $this->getRequest()->getControllerName() && 'login' == $this->getRequest()->getActionName())
                    $this->_redirect('index', 'index', 'admin');
            }
        } else {
            if (!$this->getAuthentication()->isAuthenticated()) {
                if ('auth' != $this->getRequest()->getControllerName() && 'login' != $this->getRequest()->getActionName())
                    $this->_redirect('login', 'auth', 'admin');
            } else {
                throw new Exception\HasNoAccessException(
                    'You do not have sufficient permissions to access this resource'
                );
            }
        }
        $this->view->authenticatedUser = $authenticatedUser;
		
		$this->_flashMessages = $this->broker('flashmessenger')->getMessages();
    }

    /**
     * Called after an action is dispatched by Zend\Controller\Dispatcher.
     *
     * @return void
     */
    public function postDispatch()
    {
        $this->view->doctrineUnitOfWork = $this->getEntityManager()->getUnitOfWork()->size();
        $this->view->flashMessages = $this->_flashMessages;
        
        $this->view->flushResult = $this->_flush();
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
     * Create a paginator for a given entity.
     *
     * @param string $entity The name of the entity that should be paginated
     * @param array $conditions These conditions will be passed to the Repository call
     * @return \Zend\Paginator\Paginator
     */
    protected function _createPaginator($entity, array $conditions = array(), array $orderBy = null)
    {
		return $this->_createPaginatorArray((0 == count($conditions)) ?
            $this->getEntityManager()->getRepository($entity)->findBy(array(), $orderBy) :
            $this->getEntityManager()->getRepository($entity)->findBy($conditions, $orderBy));
    }

	/**
     * Create a paginator for a given entity.
     *
     * @param string $entity The name of the entity that should be paginated
     * @param array $conditions These conditions will be passed to the Repository call
     * @return \Zend\Paginator\Paginator
     */
    protected function _createPaginatorArray(array $records)
    {
        $paginator = new Paginator(
            new ArrayAdapter($records)
        );
        $paginator->setItemCountPerPage(25);
        $paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));

        return $paginator;
    }

	/**
	 * Add a flashmessage that will be displayed on the current page.
	 *
	 * @param \Litus\FlashMessenger\FlashMessage $message The message
	 */
	protected function _addDirectFlashMessage($message)
	{
		$this->_flashMessages[] = $message;
	}
	
	/**
	 * Loads the given plugin
	 *
	 * @param sting $plugin The plugin that should be loaded
	 * @return mixed
	 */
	public function broker($plugin)
	{
		if (null === $plugin)
			throw new \InvalidArgumentException('No plugin was given');
	
		return $this->getBroker()->load($plugin);
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
                new DoctrineAdapter(
                	$this->getEntityManager(),
                	'CommonBundle\Entity\Users\Person',
                	'username',
                	'can_login'
                ),
                new DoctrineService(
                	$this->getEntityManager(),
                	'CommonBundle\Entity\Users\Session',
                	2678400
                )
            );
        }
        
        return self::$_authentication;
    }

    /**
     * Singleton implementation for the Entity Manager, retrieved
     * from the Zend Registry.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->getLocator()->get('doctrine_em');
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
		
		$cache = StorageFactory::adapterFactory(
		    'Filesystem',
		    array(
		        'ttl' => 0
		    )
		);
		
		if ($cache->hasItem('acl')) {
			$acl = $cache->getItem('acl');
		} else {
			$acl = $cache -> headernew Acl(
				$this->getEntityManager()
			);
			
			$cache->setItem('acl', $acl);	
		}
		
        $request = $this->getRequest();

        if ($this->getAuthentication()->isAuthenticated()) {
            foreach ($this->getAuthentication()->getPersonObject()->getRoles() as $role) {
                if (
                    $role->isAllowed(
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
