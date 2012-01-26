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

use Zend\Stdlib\RequestDescription as RequestDescription;

/**
 * This view helper makes sure we can access the Request object in our view.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Request extends \Zend\View\Helper\AbstractHelper
{
	/**
	 * @var \Zend\Stdlib\RequestDescription The request object
	 */
	private $_request = null;

	/**
	 * @param \Zend\Stdlib\RequestDescription $request The request object
	 * @return \CommonBundle\Component\View\Helper\Request
	 */
    public function setRequest(RequestDescription $request)
    {
    	$this->_request = $request;
    	
    	return $this;
    }
    
    /**
     * @return \Zend\Stdlib\RequestDescription
     */
    public function __invoke()
    {
    	if (null === $this->_request)
    		throw new \RuntimeException('No request object was provided');
    	
        return $this->_request;
    }
}