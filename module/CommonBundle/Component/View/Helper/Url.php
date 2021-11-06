<?php

namespace CommonBundle\Component\View\Helper;

use CommonBundle\Entity\General\Language;

/**
 * A view plugin for generating urls.
 *
 * @autor Kristof Mariën <kristof.marien@litus.cc>
 */
class Url extends \Laminas\View\Helper\Url
{
    /**
     * @var Language
     */
    private $language;

    /**
     * @param  Language $language
     * @return Url
     */
    public function setLanguage(Language $language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Generates an url given the name of a route.
     *
     * @see    Laminas\Router\RouteInterface::assemble()
     * @param  string|null $name               Name of the route
     * @param  array       $params             Parameters for the link
     * @param  array       $options            Options for the route
     * @param  boolean     $reuseMatchedParams Whether to reuse matched parameters
     * @return string                     Url                  For the link href attribute
     * @throws Exception\RuntimeException If no RouteStackInterface was provided
     * @throws Exception\RuntimeException If no RouteMatch was provided
     * @throws Exception\RuntimeException If RouteMatch didn't contain a matched route name
     */
    public function __invoke($name = null, $params = array(), $options = array(), $reuseMatchedParams = false)
    {
        if (!isset($params['language']) && $this->language) {
            $params['language'] = $this->language->getAbbrev();
        }

        return parent::__invoke($name, $params, $options, $reuseMatchedParams);
    }
}
