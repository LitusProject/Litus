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
	CommonBundle\Component\Acl\Driver\HasAccess,
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
    	$startExecutionTime = microtime(true);
    
        $this->_initControllerPlugins();
        $this->_initViewHelpers();
        
        if (
        	$this->hasAccess()->resourceAction(
        		$this->getParam('controller'), $this->getParam('action')
        	)
        ) {
        	$authenticatedUser = 'Guest';
        
            if ($this->getAuthentication()->isAuthenticated()) {
                $authenticatedUser = $this->getAuthentication()->getPersonObject()->getFirstName();

                if ('auth' == $this->getParam('controller') && 'login' == $this->getParam('action'))
                    $this->redirect()->toRoute('admin_dashboard');
            }
        } else {
            if (!$this->getAuthentication()->isAuthenticated()) {
                if ('auth' != $this->getParam('controller') && 'login' != $this->getParam('action'))
                    $this->redirect()->toRoute('admin_auth');
            } else {
                throw new Exception\HasNoAccessException(
                    'You do not have sufficient permissions to access this resource'
                );
            }
        }
		
		$result = parent::execute($e);
		
        $result['flashMessenger'] = $this->flashMessenger();
        
        $result['authenticatedUser'] = $authenticatedUser;
  		
  		$result['environment'] = getenv('APPLICATION_ENV');
  		$result['developmentInformation'] = array(
  			'executionTime' => round(microtime(true) - $startExecutionTime, 3) * 1000,
  			'doctrineUnitOfWork' => $this->getEntityManager()->getUnitOfWork()->size()
  		);
  		
        $e->setResult($result);
        return $result;
    }

    /**
     * Does some initialization work for asynchronously requested actions.
     *
     * @return void
     * @throws \CommonBundle\Component\Controller\Request\Exception\NoXmlHttpRequestException The method was not accessed by a XHR request
     */
    protected function initAjax()
    {        
        if (
        	!$this->getRequest()->headers()->get('X_REQUESTED_WITH')
        	|| 'XMLHttpRequest' != $this->getRequest()->headers()->get('X_REQUESTED_WITH')->getFieldValue()
        ) {
            throw new Request\Exception\NoXmlHttpRequestException(
            	'This page is accessible only through an asynchroneous request'
            );
        }
    }
    
    /**
     * Initializes our custom view helpers.
     *
     * @return void
     */
    private function _initViewHelpers()
    {
    	$view = $this->getLocator()->get('view');
    	
    	// DateLocalized View Helper
    	$view->getEnvironment()->getBroker()->getClassLoader()->registerPlugin(
    		'datelocalized', 'CommonBundle\Component\View\Helper\DateLocalized'
    	);
		
    	// GetParam View Helper
    	$view->getEnvironment()->getBroker()->getClassLoader()->registerPlugin(
    		'getparam', 'CommonBundle\Component\View\Helper\GetParam'
    	);
    	$view->plugin('getParam')->setRouteMatch(
    		$this->getEvent()->getRouteMatch()
    	);
    	
    	// HasAccess View Helper
    	$view->getEnvironment()->getBroker()->getClassLoader()->registerPlugin(
    		'hasaccess', 'CommonBundle\Component\View\Helper\HasAccess'
    	);		
    	$view->plugin('hasAccess')->setDriver(
    		new HasAccess(
    			$this->_getAcl(), $this->getAuthentication()
    		)
    	);
    }
    
    /**
     * Initializes our custom controller plugins.
     *
     * @return void
     */
    private function _initControllerPlugins()
    {
    	// HasAccess Plugin
    	$this->getBroker()->getClassLoader()->registerPlugin(
    		'hasaccess', 'CommonBundle\Component\Controller\Plugin\HasAccess'
    	);		
    	$this->hasAccess()->setDriver(
    		new HasAccess(
    			$this->_getAcl(), $this->getAuthentication()
    		)
    	);
    	
    	// Paginator Plugin
    	$this->getBroker()->getClassLoader()->registerPlugin(
    		'paginator', 'CommonBundle\Component\Controller\Plugin\Paginator'
    	);
    }
    
    /**
     * Returns the ACL object
     *
     * @TODO Figure out how Zend\Cache works
     *
     * @return \CommonBundle\Component\Acl\Acl
     */
    private function _getAcl()
    {
    	return new Acl(
    		$this->getEntityManager()
    	);
    }
	
    /**
     * Returns the Authentication instance
     *
     * @return \CommonBundle\Component\Authentication\Authentication
     */
    public function getAuthentication()
    {
        return $this->getLocator()->get('authentication');
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
     * Singleton implementation for the Mail Transport, retrieved
     * from the Zend Registry.
     *
     * @return \Zend\Mail\Transport
     */
    public function getMailTransport()
    {
        return $this->getLocator()->get('mail_transport');
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
}
