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

namespace CommonBundle\Component\Controller\Plugin;

use CommonBundle\Entity\General\Language;

/**
 * A controller plugin for generating urls.
 *
 * @autor Kristof Mariën <kristof.marien@litus.cc>
 */
class Url extends \Laminas\Mvc\Controller\Plugin\Url
{
    /**
     * @var Language
     */
    private $language;

    /**
     * @param Language $language
     *
     * @return self
     */
    public function setLanguage(Language $language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Generates a URL based on a route.
     *
     * @param  string|null   $route              RouteInterface name
     * @param  array         $params             Parameters to use in url generation, if any
     * @param  array|boolean $options            RouteInterface-specific options to use in url generation, if any. If boolean, and no fourth argument, used as $reuseMatchedParams
     * @param  boolean       $reuseMatchedParams Whether to reuse matched parameters
     * @return string
     * @throws Exception\DomainException  if composed controller does not implement InjectApplicationEventInterface, or
     *                                                       router cannot be found in controller event
     * @throws Exception\RuntimeException if no RouteMatch instance or no matched route name present
     */
    public function fromRoute($route = null, $params = array(), $options = array(), $reuseMatchedParams = false)
    {
        if (!isset($params['language']) && $this->language) {
            $params['language'] = $this->language->getAbbrev();
        }

        return parent::fromRoute($route, $params, $options, $reuseMatchedParams);
    }
}
