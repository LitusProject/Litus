<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace CudiBundle\Component\Controller;

use Exception,
    Zend\Mvc\MvcEvent;

/**
 * We extend the CommonBundle controller to check a sale session is selected.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SaleController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    /**
     * Execute the request.
     *
     * @param \Zend\Mvc\MvcEvent $e The MVC event
     * @return array
     * @throws \CommonBundle\Component\Controller\Exception\HasNoAccessException The user does not have permissions to access this resource
     */
    public function onDispatch(MvcEvent $e)
    {
        if (isset($_SERVER['HTTPS']) && '' != $_SERVER['HTTPS'])
            $this->redirect()->toUrl('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

        $session = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session')
            ->findOneById($this->getParam('session'));

        if (null == $session || !$session->isOpen()) {
            $sessions = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Session')
                ->findOpen();
            if (sizeof($sessions) == 1) {
                $this->redirect()->toRoute(
                    $this->getParam('controller'),
                    array(
                        'action' => $this->getParam('action'),
                        'language' => $this->getLanguage()->getAbbrev(),
                        'session' => $sessions[0]->getId(),
                    )
                );
            }
        }

        $result = parent::onDispatch($e);

        if (null == $session || !$session->isOpen())
            $result->invalidSession = true;

        $language = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findOneByAbbrev('en');

        $result->language = $language;

        $result->session = $session;

        $result->organizationUrl = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('organization_url');

        $result->lightVersion = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.sale_light_version');

        $e->setResult($result);
        return $result;
    }

    /**
     * Returns the WebSocket URL.
     *
     * @return string
     */
    protected function getSocketUrl()
    {
        $address = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.queue_socket_remote_host');
        $port = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.queue_socket_port');

        return 'ws://' . $address . ':' . $port;
    }
}
