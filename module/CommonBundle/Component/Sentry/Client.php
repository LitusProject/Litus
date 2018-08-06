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

namespace CommonBundle\Component\Sentry;

use CommonBundle\Component\Authentication\Authentication,
    Exception,
    Raven_Client,
    Throwable,
    Zend\Console\Request as ConsoleRequest,
    Zend\Mvc\Application,
    Zend\Mvc\MvcEvent,
    Zend\Http\PhpEnvironment\Request as PhpRequest,
    Zend\Stdlib\RequestInterface;

/**
 * Sentry is an open-source error tracking platform that provides complete app logic,
 * deep context, and visibility across the entire stack in real time. This client
 * provides all functions needed to store Litus exceptions.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Client
{
    /**
     * @var Authentication The authentication instance
     */
    private $authentication;

    /**
     * @var Raven_Client The Raven client connecting to the Sentry server
     */
    private $ravenClient;

    /**
     * @var RequestInterface The request to the page
     */
    private $request;

    /**
     * Constructs a new Sentry client.
     *
     * @param Raven_Client          $ravenClient    The Raven client connecting to the Sentry server
     * @param Authentication|null   $authentication The authentication instance
     * @param RequestInterface|null $request        The request to the page
     */
    public function __construct(Raven_Client $ravenClient, Authentication $authentication = null, RequestInterface $request = null)
    {
        $this->authentication = $authentication;
        $this->ravenClient = $ravenClient;
        $this->request = $request;
    }

    /**
     * Sends an exception to the server.
     *
     * @param  Exception $exception The exception that should be sent
     * @return void
     */
    public function logException(Exception $exception)
    {
        $this->ravenClient->captureException(
            $exception,
            array(
                'user'  => $this->getUser(),
                'extra' => array(
                    'request_uri' => $this->getRequestUri(),
                    'user_agent'  => $this->getUserAgent(),
                ),
            )
        );
    }
    
    /**
     * Sends a log message to the server.
     *
     * @param  string $message The message that should be sent
     * @return void
     */
    public function logMessage($message)
    {
        $this->ravenClient->captureMessage(
            $message,
            array(),
            array(
                'level' => 'info',
            )
        );
    }
    
    /**
     * Handler that can be attached to Zend's EventManager and extracts the exception
     * from an MvcEvent
     *
     * @param  MvcEvent $event The MvcEvent passed by the EventManager
     * @return void
     */
    public function logMvcEvent(MvcEvent $event)
    {
        $exception = $event->getParam('exception');
        if (!$exception instanceof \Throwable) {
            return;
        }

        $this->logException($exception);
    }

    /**
     * Get the request URI.
     *
     * @return string
     */
    private function getRequestUri()
    {
        if ($this->request instanceof ConsoleRequest) {
            return $this->request->toString();
        } elseif ($this->request instanceof PhpRequest) {
            return '' != $this->request->getServer()->get('HTTP_HOST')
                ? (($this->request->getServer()->get('HTTPS') != 'off') ? 'https://' : 'http://') . $this->request->getServer()->get('HTTP_HOST') . $this->request->getServer()->get('REQUEST_URI')
                : '';
        }

        return '';
    }

    /**
     * Get the user.
     *
     * @return array
     */
    private function getUser() {
        if ($this->request instanceof ConsoleRequest) {
            $user = array(
                'id'       => 0,
                'username' => 'console',
            );
        } elseif ($this->request instanceof PhpRequest) {
            if ($this->authentication->isAuthenticated()) {
                $user = array(
                    'id'         => $this->authentication->getPersonObject()->getId(),
                    'username'   => $this->authentication->getPersonObject()->getUsername(),
                    'email'      => $this->authentication->getPersonObject()->getEmail(),
                    'name'       => $this->authentication->getPersonObject()->getFullName(), 
                    'session'    => $this->authentication->getSessionObject()->getId(),
                );
            } else {
                $user = array(
                    'id'       => 0,
                    'username' => 'guest',
                );
            }
        } else {
            $user = array();
        }
    }

    /**
     * Get the user agent.
     *
     * @return string
     */
    private function getUserAgent()
    {
        if ($this->request instanceof ConsoleRequest) {
            return 'Console';
        } elseif ($this->request instanceof PhpRequest) {
            return $this->request->getServer()->get('HTTP_USER_AGENT');
        }

        return '';
    }
}
