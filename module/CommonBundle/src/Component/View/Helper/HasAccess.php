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

use CommonBundle\Component\Acl\Driver\HasAccess as HasAccessDriver;

/**
 * A view helper that allows us to easily verify whether or not the authenticated user
 * has access to a resource. This is particularly useful for creating navigation items.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class HasAccess extends \Zend\View\Helper\AbstractHelper
{	
	/**
	 * @var \CommonBundle\Component\Acl\Helper\HasAccess The helper object
	 */
	private $_helper = null;
	
	/**
	 * @param \CommonBundle\Component\Acl\Helper\HasAccess $acl The helper object
	 * @return \CommonBundle\Component\View\Helper\HasAccess
	 */
	public function setHelper(HasAccessDriver $helper)
	{
		$this->_helper = $helper;
		
		return $this;
	}
	
	/**
	 * @param string $resource The resource that should be verified
	 * @param string $action The module that should be verified	 	 
	 * @return bool
	 */
    public function __invoke($resource, $action)
    {
    	if (null === $this->_helper)
        	throw new Exception\RuntimeException('No helper driver object was provided');
        
        $helper = $this->_helper;
        	
        return $helper(
        	$resource, $action
        );
    }
}