<?php

namespace CommonBundle\Component\ServiceManager\ServiceLocatorAware;

/**
 * A trait to define some common methods for classes with a ServiceLocator.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */

trait MailTransportTrait
{
    /**
     * @return \Laminas\Mail\Transport\TransportInterface
     */
    public function getMailTransport()
    {
        return $this->getServiceLocator()->get('mail_transport');
    }

    /**
     * @return \Laminas\ServiceManager\ServiceLocatorInterface
     */
    abstract public function getServiceLocator();
}
