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
	CommonBundle\Component\Util\File,
	Zend\Cache\StorageFactory,
	Zend\Mvc\MvcEvent,
	Zend\Paginator\Paginator,
	Zend\Paginator\Adapter\ArrayAdapter;

/**
 * We extend the basic Zend controller to simplify database access, authentication
 * and some other common functionality.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class ActionController extends \Zend\Mvc\Controller\ActionController implements AuthenticationAware, DoctrineAware
{	
	/**
     * Execute the request
     * 
     * @param \Zend\Mvc\MvcEvent $e The MVC event
     * @return array
     * @throws \CommonBundle\Component\Controller\Exception\HasNoAccessException The user does not have permissions to access this resource
     */
    public function execute(MvcEvent $e)
    {
        $result = parent::execute($e);
        
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
		$result['authenticatedUser'] = $authenticatedUser;
		        
        $result['flashMessenger'] = $this->flashMessenger();
  
        $e->setResult($result);
        return $result;
    }

    /**
     * Create a paginator for a given entity.
     *
     * @param string $entity The name of the entity that should be paginated
     * @param array $conditions These conditions will be passed to the Repository call
     * @return \Zend\Paginator\Paginator
     */
    protected function createEntityPaginator($entity, array $conditions = array(), array $orderBy = null)
    {
		return $this->createArrayPaginator((0 == count($conditions)) ?
            $this->getEntityManager()->getRepository($entity)->findBy(array(), $orderBy) :
            $this->getEntityManager()->getRepository($entity)->findBy($conditions, $orderBy));
    }

	/**
     * Create a paginator from a given array.
     *
     * @param string $entity The name of the entity that should be paginated
     * @param array $conditions These conditions will be passed to the Repository call
     * @return \Zend\Paginator\Paginator
     */
    protected function createArrayPaginator(array $records)
    {
        $paginator = new Paginator(
            new ArrayAdapter($records)
        );
        $paginator->setItemCountPerPage(25);
        $paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));

        return $paginator;
    }

    /**
     * Flushes the entity manager and catches ORM exceptions, which is then stored in a view variable.
     *
     * @return bool
     */
    protected function flush()
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
     * @throws \CommonBundle\Component\Controller\Request\Exception\NoXmlHttpRequestException The method was not accessed by a XHR request
     */
    protected function initAjax()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new Request\Exception\NoXmlHttpRequestException(
            	'This page is accessible only by a asynchroneous request'
            );
        }
    }

    /**
     * Returns the Authentication instance
     *
     * @return \CommonBundle\Component\Authentication\Authentication
     */
    public function getAuthentication()
    {
        return $this->getLocator()->get('commonbundle_authentication');
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
     * Gets a parameter from a GET request.  
     * 
     * @param string $param The parameter's key
     * @param mixed $default The default value, returned when the parameter is not found
     * @return string
     */
    public function getParam($param, $default = null)
    {
        return $this->getEvent()->getRouteMatch()->getParam($param, $default);
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
			$acl = new Acl(
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
            return $acl->isAllowed(
                'guest',
                $request->getModuleName() . '.' . $request->getControllerName(),
                $request->getActionName()
            );
        }
    }
}
