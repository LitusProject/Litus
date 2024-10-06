<?php

namespace CudiBundle\Component\Controller;

use CommonBundle\Component\ServiceManager\ServiceLocatorAware\FormFactoryTrait;
use Laminas\Mvc\MvcEvent;

/**
 * We extend the CommonBundle controller to check a sale session is selected.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SaleController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    use FormFactoryTrait;

    /**
     * Execute the request.
     *
     * @param  MvcEvent $e The MVC event
     * @return array
     * @throws \CommonBundle\Component\Controller\Exception\HasNoAccessException The user does not have permissions to access this resource
     */
    public function onDispatch(MvcEvent $e)
    {
        error_log('SaleController::onDispatch');
        $session = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Sale\Session')
            ->findOneById($this->getParam('session'));

        if ($session == null || !$session->isOpen()) {
            $sessions = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Sale\Session')
                ->findOpen();

            if (count($sessions) == 1) {
                $this->redirect()->toRoute(
                    $this->getParam('controller'),
                    array(
                        'action'   => $this->getParam('action'),
                        'language' => $this->getLanguage()->getAbbrev(),
                        'session'  => $sessions[0]->getId(),
                    )
                );
            }
        }

        $result = parent::onDispatch($e);

        if ($session == null || !$session->isOpen()) {
            $result->invalidSession = true;
        }

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
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('cudi.queue_socket_public');
    }

    /**
     * We need to be able to specify all required authentication information,
     * which depends on the part of the site that is currently being used.
     *
     * @return array
     */
    public function getAuthenticationHandler()
    {
        return array(
            'action'         => 'index',
            'controller'     => 'common_index',

            'auth_route'     => 'cudi_sale_auth',
            'redirect_route' => 'cudi_sale_sale',
        );
    }
}
