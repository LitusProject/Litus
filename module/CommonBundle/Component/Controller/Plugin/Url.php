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

namespace CommonBundle\Component\Controller\Plugin;

use CommonBundle\Entity\General\Language;

/**
 * A controller plugin for generating urls.
 *
 * @autor Kristof Mariën <kristof.marien@litus.cc>
 */
class Url extends \Zend\Mvc\Controller\Plugin\Url
{
    /**
     * @var \CommonBundle\Entity\General\Language
     */
    private $_language;

    /**
     * @param \CommonBundle\Entity\General\Language $language
     *
     * @return \CommonBundle\Component\Controller\Plugin\Url
     */
    public function setLanguage(Language $language)
    {
        $this->_language = $language;
        return $this;
    }

    /**
     * Generates a URL based on a route.
     *
     * @param  string $route RouteInterface name
     * @param  array $params Parameters to use in url generation, if any
     * @param  array|bool $options RouteInterface-specific options to use in url generation, if any. If boolean, and no fourth argument, used as $reuseMatchedParams
     * @param  boolean $reuseMatchedParams Whether to reuse matched parameters
     * @return string
     * @throws Exception\DomainException if composed controller does not implement InjectApplicationEventInterface, or
     *         router cannot be found in controller event
     * @throws Exception\RuntimeException if no RouteMatch instance or no matched route name present
     */
    public function fromRoute($route = null, $params = array(), $options = array(), $reuseMatchedParams = false)
    {
        if (!isset($params['language']) && $this->_language)
            $params['language'] = $this->_language->getAbbrev();

        return parent::fromRoute($route, $params, $options, $reuseMatchedParams);
    }
}
