<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

use CommonBundle\Entity\General\Language;

/**
 * A view plugin for generating urls.
 *
 * @autor Kristof Mariën <kristof.marien@litus.cc>
 */
class Url extends \Zend\View\Helper\Url
{
    /**
     * @var \CommonBundle\Entity\General\Language
     */
    private $_language;

    /**
     * @param \CommonBundle\Entity\General\Language $language
     * @return \CommonBundle\Component\Controller\Plugin\Url
     */
    public function setLanguage(Language $language)
    {
        $this->_language = $language;
        return $this;
    }

    /**
     * Generates an url given the name of a route.
     *
     * @see    Zend\Mvc\Router\RouteInterface::assemble()
     * @param  string  $name               Name of the route
     * @param  array   $params             Parameters for the link
     * @param  array   $options            Options for the route
     * @param  boolean $reuseMatchedParams Whether to reuse matched parameters
     * @return string Url                  For the link href attribute
     * @throws Exception\RuntimeException  If no RouteStackInterface was provided
     * @throws Exception\RuntimeException  If no RouteMatch was provided
     * @throws Exception\RuntimeException  If RouteMatch didn't contain a matched route name
     */
    public function __invoke($name = null, array $params = array(), $options = array(), $reuseMatchedParams = false)
    {
        if (!isset($params['language']) && $this->_language)
            $params['language'] = $this->_language->getAbbrev();

        return parent::__invoke($name, $params, $options, $reuseMatchedParams);
    }
}
