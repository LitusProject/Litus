<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\View\Helper;

use Zend\Mvc\Router\RouteMatch;

/**
 * This view helper makes sure we can access the request paramaters in our view.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class GetParam extends \Zend\View\Helper\AbstractHelper
{
    /**
     * @var \Zend\Mvc\Router\RouteMatch The matched router object
     */
    private $_routeMatch = null;

    /**
     * @param \Zend\Stdlib\RequestDescription $routeMatch The matched router object
     * @return \CommonBundle\Component\View\Helper\Request
     */
    public function setRouteMatch(RouteMatch $routeMatch)
    {
        $this->_routeMatch = $routeMatch;
        return $this;
    }

    /**
     * @param string $key The parameter's key
     * @param mixed $default A default value for when the key is not present
     * @return string
     */
    public function __invoke($key, $default = null)
    {
        if (null === $this->_routeMatch)
            throw new Exception\RuntimeException('No matched route was provided');

        return $this->_routeMatch->getParam($key, $default);
    }
}
