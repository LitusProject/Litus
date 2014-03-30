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

namespace ApiBundle\Component\Controller\ActionController;

use ApiBundle\Component\Controller\Request\Exception\NoPostRequestException,
    ApiBundle\Component\Controller\Request\Exception\NoKeyException,
    ApiBundle\Component\Controller\Request\Exception\InvalidKeyException,
    CommonBundle\Component\Util\AcademicYear,
    CommonBundle\Entity\General\AcademicYear as AcademicYearEntity,
    DateInterval,
    DateTime,
    Zend\Mvc\MvcEvent;

/**
 * We extend the CommonBundle controller.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class ApiController extends \CommonBundle\Component\Controller\ActionController
{
    /**
     * Execute the request.
     *
     * @param  \Zend\Mvc\MvcEvent $e The MVC event
     * @return array
     */
    public function onDispatch(MvcEvent $e)
    {
        $result = parent::onDispatch($e);

        if ('development' != getenv('APPLICATION_ENV')) {
            if (!$this->getRequest()->isPost()) {
                throw new NoPostRequestException(
                    'The API should be accessed using POST requests'
                );
            }

            if (null === $this->getRequest()->getPost('key')) {
                throw new NoKeyException(
                    'No API key was provided with the request'
                );
            }

            $key = $this->getEntityManager()
                ->getRepository('ApiBundle\Entity\Key')
                ->findOneActiveByCode($this->getRequest()->getPost('key'));

            $validateKey = $key->validate(
                $this->getRequest()->getServer('HTTP_X_FORWARDED_FOR', $this->getRequest()->getServer('REMOTE_ADDR'))
            );

            if (!$validateKey) {
                throw new InvalidKeyException(
                    'The given API key was invalid'
                );
            }
        }

        $e->setResult($result);

        return $result;
    }

    /**
     * We need to be able to specify all required authentication information,
     * which depends on the part of the site that is currently being used.
     *
     * @return array
     */
    public function getAuthenticationHandler()
    {
        return null;
    }
}
