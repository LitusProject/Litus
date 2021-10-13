<?php

namespace CommonBundle\Component\ServiceManager\ServiceLocatorAware;

use Laminas\View\Renderer\PhpRenderer;

/**
 * A trait to define some common methods for classes with a ServiceLocator.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */

trait ViewRendererTrait
{
    /**
     * @return \Laminas\View\Renderer\RendererInterface
     */
    public function getViewRenderer()
    {
        return $this->getServiceLocator()->get(PhpRenderer::class);
    }

    /**
     * @return \Laminas\ServiceManager\ServiceLocatorInterface
     */
    abstract public function getServiceLocator();
}
