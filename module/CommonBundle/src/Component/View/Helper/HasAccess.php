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
 
namespace CommonBundle\Component\View\Helper;

use CommonBundle\Component\Acl\Acl,
	CommonBundle\Component\Authentication\Authentication;

/**
 * A view helper that allows us to easily verify whether or not the authenticated user
 * has access to a resource. This is particularly useful for creating navigation items.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class HasAccess extends \Zend\View\Helper\AbstractHelper
{	
	/**
	 * @var \CommonBundle\Component\Acl\Acl The ACL object
	 */
	private $_acl = null;
	
	/**
	 * @var \CommonBundle\Component\Authentication\Authentication The authentication object
	 */
	private $_authentication = null;
	
	/**
	 * @param \CommonBundle\Component\Acl\Acl $acl The ACL object
	 * @return \CommonBundle\Component\View\Helper\HasAccess
	 */
	public function setAcl(Acl $acl)
	{
		$this->_acl = $acl;
		
		return $this;
	}
	
	/**
	 * @param \CommonBundle\Component\Authentication\Authentication $authentication The authentication object
	 * @return \CommonBundle\Component\View\Helper\HasAccess
	 */
	public function setAuthentication(Authentication $authentication)
	{
		$this->_authentication = $authentication;
		
		return $this;
	}
	
	/**
	 * @param string $module The module that should be verified
	 * @param string $controller The controller that should be verified
	 * @param string $action The module that should be verified	 	 
	 * @return bool
	 */
    public function __invoke($module, $controller, $action)
    {
    	if (null === $this->_acl)
    		throw new \RuntimeException('No ACL object was provided');
    		
    	if (null === $this->_authentication)
    		throw new \RuntimeException('No authentication object was provided');
    
        // Making it easier to develop new actions and controllers, without all the ACL hassle
        if ('development' == getenv('APPLICATION_ENV'))
            return true;

        if ($this->_authentication->isAuthenticated()) {
            foreach ($this->_authentication->getPersonObject()->getRoles() as $role) {
                if (
                    $role->isAllowed(
                        $module . '.' . $controller,
                        $action
                    )
                ) {
                    return true;
                }
            }

            return false;
        } else {
            return $this->_acl->isAllowed(
                'guest',
                $module . '.' . $controller,
                $action
            );
        }
    }
}