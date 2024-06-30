<?php

namespace LogisticsBundle\Component\Controller;

use CommonBundle\Component\Controller\ActionController\Exception\ShibbolethUrlException;
use CommonBundle\Component\Controller\Exception\HasNoAccessException;
use CommonBundle\Entity\General\Config;
use CommonBundle\Entity\User\Person\Academic;
use Laminas\Mvc\MvcEvent;
use LogisticsBundle\Entity\FlesserkeArticle;
use LogisticsBundle\Entity\InventoryArticle;
use LogisticsBundle\Entity\Order;

/**
 * We extend the CommonBundle controller.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class LogisticsController extends \CommonBundle\Component\Controller\ActionController
{
    /**
     * Execute the request.
     *
     * @param  MvcEvent $e The MVC event
     * @return array
     * @throws HasNoAccessException The user does not have permissions to access this resource
     */
    public function onDispatch(MvcEvent $e)
    {
        $result = parent::onDispatch($e);

        $result->loginForm = $this->getForm('common_auth_login')
            ->setAttribute('class', '')
            ->setAttribute(
                'action',
                $this->url()->fromRoute('logistics_auth', array('action' => 'login',))
            );
        $result->organizationUrl = $this->getEntityManager()
            ->getRepository(Config::class)
            ->getConfigValue('organization_url');
        $result->shibbolethUrl = $this->getShibbolethUrl();

        $e->setResult($result);

        return $result;
    }

    /**
     * We need to be able to specify all required authentication information,
     * which depends on the part of the site that is currently being used.
     *
     * @return array
     */
    public function getAuthenticationHandler(): array
    {
        return array(
            'action'         => 'index',
            'controller'     => 'common_index',

            'auth_route'     => 'logistics_order',
            'redirect_route' => 'logistics_order',
        );
    }

    /**
     * Create the full Shibboleth URL.
     *
     * @return string
     */
    private function getShibbolethUrl()
    {
        $shibbolethUrl = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shibboleth_url');

        if (@unserialize($shibbolethUrl) !== false) {
            $shibbolethUrl = unserialize($shibbolethUrl);

            if (getenv('SERVED_BY') === false) {
                throw new ShibbolethUrlException('The SERVED_BY environment variable does not exist');
            }
            if (!isset($shibbolethUrl[getenv('SERVED_BY')])) {
                throw new ShibbolethUrlException('Array key ' . getenv('SERVED_BY') . ' does not exist');
            }

            $shibbolethUrl = $shibbolethUrl[getenv('SERVED_BY')];
        }

        $shibbolethUrl .= '?source=logistics';

        if ($this->getParam('redirect') !== null) {
            $shibbolethUrl .= '%26redirect=' . urlencode($this->getParam('redirect'));
        }

        $server = $this->getRequest()->getServer();
        if (isset($server['X-Forwarded-Host']) && isset($server['REQUEST_URI'])) {
            $shibbolethUrl .= '%26redirect=' . urlencode('https://' . $server['X-Forwarded-Host'] . $server['REQUEST_URI']);
        }

        return $shibbolethUrl;
    }

    /**
     * @return Academic|null
     */
    protected function getAcademicEntity(): ?Academic
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            $this->flashMessenger()->error(
                'Error',
                'You are not authenticated! Login to get access to this service.'
            );
            return null;
        }

        $academic = $this->getAuthentication()->getPersonObject();

        if (!($academic instanceof Academic)) {
            $this->flashMessenger()->error(
                'Error',
                'You are not a student! Create a student account to get access to this service.'
            );
            return null;
        }

        return $academic;
    }

    /**
     * @return Order|null
     */
    protected function getOrderEntity(): ?Order
    {
        $order = $this->getEntityById(Order::class, 'order');
        if (!($order instanceof Order)) {
            $this->flashMessenger()->error(
                'Error',
                'No order was found!'
            );
            $this->redirect()->toRoute(
                'logistics_order',
                array(
                    'action' => 'index',
                )
            );
            return null;
        }

        return $order;
    }

    /**
     * @return InventoryArticle|null
     */
    protected function getInventoryArticleEntity(): ?InventoryArticle
    {
        $article = $this->getEntityById(InventoryArticle::class, 'article');
        if (!($article instanceof InventoryArticle)) {
            $this->flashMessenger()->error(
                'Error',
                'No article was found!'
            );
            $this->redirect()->toRoute(
                'logistics_inventory_article',
                array(
                    'action' => 'index',
                )
            );
            return null;
        }

        return $article;
    }

    /**
     * @return FlesserkeArticle|null
     */
    protected function getFlesserkeArticleEntity(): ?FlesserkeArticle
    {
        $article = $this->getEntityById(FlesserkeArticle::class, 'article');
        if (!($article instanceof FlesserkeArticle)) {
            $this->flashMessenger()->error(
                'Error',
                'No article was found!'
            );
            $this->redirect()->toRoute(
                'logistics_flesserke_article',
                array(
                    'action' => 'index',
                )
            );
            return null;
        }

        return $article;
    }
}
