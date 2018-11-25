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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\View\Helper;

use Zend\Router\RouteMatch;

/**
 * This view helper makes sure we can access the request paramaters in our view.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class GetParam extends \Zend\View\Helper\AbstractHelper
{
    /**
     * @var RouteMatch The matched router object
     */
    private $routeMatch = null;

    /**
     * @param  RouteMatch $routeMatch The matched router object
     * @return GetParam
     */
    public function setRouteMatch(RouteMatch $routeMatch)
    {
        $this->routeMatch = $routeMatch;

        return $this;
    }

    /**
     * @param  string      $key     The parameter's key
     * @param  string|null $default A default value for when the key is not present
     * @return string
     */
    public function __invoke($key, $default = null)
    {
        if ($this->routeMatch === null) {
            throw new Exception\RuntimeException('No matched route was provided');
        }

        return $this->routeMatch->getParam($key, $default);
    }
}
