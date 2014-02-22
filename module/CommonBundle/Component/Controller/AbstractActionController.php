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
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\Controller;

use Zend\Mvc\MvcEvent,
    Zend\Console\Request as ConsoleRequest;

/**
 * We extend the basic Zend controller to simplify database access and create some helper functions.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class AbstractActionController extends \Zend\Mvc\Controller\AbstractActionController implements DoctrineAware
{
    /**
     * We want an easy method to retrieve the DocumentManager from
     * the DI container.
     *
     * @return \Doctrine\ODM\MongoDB\DocumentManager
     */
    public function getDocumentManager()
    {
        return $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
    }

    /**
     * We want an easy method to retrieve the EntityManager from
     * the DI container.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    }

    /**
     * We want an easy method to retrieve the Cache from
     * the DI container.
     *
     * @return \Zend\Cache\Storage\Adapter\Apc
     */
    public function getCache()
    {
        if ($this->getServiceLocator()->has('cache'))
            return $this->getServiceLocator()->get('cache');

        return null;
    }

    /**
     * We want an easy method to retrieve the Mail Transport from
     * the DI container.
     *
     * @return \Zend\Mail\Transport\TransportInterface
     */
    public function getMailTransport()
    {
        return $this->getServiceLocator()->get('mail_transport');
    }

    /**
     * Gets a parameter from a GET request.
     *
     * @param string $param The parameter's key
     * @param mixed $default The default value, returned when the parameter is not found
     * @return string
     */
    public function getParam($param, $default = null)
    {
        return $this->getEvent()->getRouteMatch()->getParam($param, $default);
    }

    /**
     * Retrieve the common session storage from the DI container.
     *
     * @return \Zend\Session\Container
     */
    public function getSessionStorage()
    {
        return $this->getServiceLocator()->get('common_sessionstorage');
    }
}
