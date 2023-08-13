<?php

namespace CommonBundle\Component\Controller\ActionController;

use Laminas\Http\Request as HttpRequest;
use Laminas\Mvc\MvcEvent;
use PageBundle\Entity\Node\Page;

/**
 * We extend the CommonBundle controller.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class SiteController extends \CommonBundle\Component\Controller\ActionController
{
    /**
     * Execute the request.
     *
     * @param  MvcEvent $e The MVC event
     * @return array
     */
    public function onDispatch(MvcEvent $e)
    {
        $this->getViewRenderer()
            ->plugin('headMeta')
            ->appendName('viewport', 'width=device-width, initial-scale=1.0');

        $iosAppId = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.ios_app_id');

        if ($iosAppId != '') {
            $this->getViewRenderer()
                ->plugin('headMeta')
                ->appendName('apple-itunes-app', 'app-id=' . $iosAppId);
        }

        $result = parent::onDispatch($e);

        $result->menu = $this->buildMenu();

        $result->shibbolethUrl = $this->getShibbolethUrl();

        $result->banners = $this->getEntityManager()
            ->getRepository('BannerBundle\Entity\Node\Banner')
            ->findAllActive();

        $result->currentAcademicYear = $this->getCurrentAcademicYear();

        $result->logos = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Logo')
            ->findAllByType('homepage');

        $result->logoPath = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.public_logo_path');

        if ($this->getRequest() instanceof HttpRequest) {
            $result->showCookieBanner = !isset($this->getRequest()->getCookie()->cookie_permission);
        } else {
            $result->showCookieBanner = true;
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
        return array(
            'action'         => 'login',
            'controller'     => 'common_auth',

            'auth_route'     => 'common_auth',
            'redirect_route' => 'common_index',
        );
    }

    /**
     * This is the top navigation menu, displayed on every page.
     *
     * @return array
     */
    private function buildMenu()
    {
        $categories = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Category')
            ->findByParent(null);

        $menu = array();

        $activeItem = -1;

        $i = 0;
        foreach ($categories as $category) {
            $menu[$i] = array(
                'type'   => 'category',
                'name'   => $category->getName($this->getLanguage()),
                'items'  => array(),
                'active' => false,
                'has_category_page' => !is_null($this->getEntityManager()->getRepository("PageBundle\Entity\CategoryPage")
                    ->findOneByCategory($category)),
            );

            $pages = $this->getEntityManager()
                ->getRepository('PageBundle\Entity\Node\Page')
                ->findBy(
                    array(
                        'category' => $category,
                        'parent'   => null,
                        'endTime'  => null,
                    )
                );

            $links = $this->getEntityManager()
                ->getRepository('PageBundle\Entity\Link')
                ->findBy(
                    array(
                        'category' => $category,
                        'parent'   => null,
                    )
                );

            foreach ($pages as $page) {
                if ($page->isLanguageAvailable($this->getLanguage()) && $page->isActive()) {
                    $menu[$i]['items'][] = array(
                        'type'        => 'page',
                        'id'          => $page->getId(),
                        'name'        => $page->getName(),
                        'title'       => $page->getTitle($this->getLanguage()),
                        'orderNumber' => $page->getOrderNumber(),
                    );

                    if ($activeItem < 0 && $this->getParam('controller') == 'page' && $this->getParam('name') == $page->getName()) {
                        $activeItem = $i;
                    }
                }
            }

            foreach ($links as $link) {
                if ($link->isLanguageAvailable($this->getLanguage()) && $link->isActive()) {
                    $menu[$i]['items'][] = array(
                        'type'        => 'link',
                        'id'          => $link->getId(),
                        'name'        => $link->getName($this->getLanguage()),
                        'url'         => $link->getUrl($this->getLanguage()),
                        'orderNumber' => $link->getOrderNumber(),
                    );

                    if ($activeItem < 0 && strpos($this->getRequest()->getRequestUri(), $link->getUrl($this->getLanguage())) === 0) {
                        $activeItem = $i;
                    }
                }
            }

            $sort = array();
            foreach ($menu[$i]['items'] as $key => $value) {
                $sort[$key] = $value['orderNumber'] ?? ($value['title'] ?? $value['name']);
            }

            array_multisort($sort, $menu[$i]['items']);

            $i++;
        }

        if ($activeItem >= 0) {
            $menu[$activeItem]['active'] = true;
        }

        $sort = array();
        foreach ($menu as $key => $value) {
            $sort[$key] = $value['title'] ?? $value['name'];
        }

        array_multisort($sort, $menu);

        return $menu;
    }

    /**
     * Build a pages submenu.
     *
     * @param  Page $page The page
     * @return array
     */
    protected function buildSubmenu(Page $page)
    {
        $pages = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Node\Page')
            ->findByParent($page->getId());

        $links = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Link')
            ->findByParent($page->getId());

        $categories = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Category')
            ->findByParent($page->getId());

        $submenu = array();
        foreach ($pages as $page) {
            $submenu[] = array(
                'type'   => 'page',
                'id'     => $page->getId(),
                'name'   => $page->getName(),
                'parent' => $page->getParent()->getName(),
                'title'  => $page->getTitle($this->getLanguage()),
            );
        }

        foreach ($links as $link) {
            $submenu[] = array(
                'type' => 'link',
                'id'   => $link->getId(),
                'name' => $link->getName($this->getLanguage()),
                'url'  => $link->getUrl($this->getLanguage()),
            );
        }

        $i = count($submenu);
        foreach ($categories as $category) {
            $submenu[$i] = array(
                'type'  => 'category',
                'name'  => $category->getName(),
                'items' => array(),
            );

            $pages = $this->getEntityManager()
                ->getRepository('PageBundle\Entity\Node\Page')
                ->findByCategory($category);

            $links = $this->getEntityManager()
                ->getRepository('PageBundle\Entity\Link')
                ->findByCategory($category);

            foreach ($pages as $page) {
                $submenu[$i]['items'][] = array(
                    'type'  => 'page',
                    'name'  => $page->getName(),
                    'title' => $page->getTitle($this->getLanguage()),
                );
            }

            foreach ($links as $link) {
                $submenu[$i]['items'][] = array(
                    'type' => 'link',
                    'id'   => $link->getId(),
                    'name' => $link->getName($this->getLanguage()),
                    'url'  => $link->getUrl($this->getLanguage()),
                );
            }

            $sort = array();
            foreach ($submenu[$i]['items'] as $key => $value) {
                $sort[$key] = $value['title'] ?? $value['name'];
            }

            array_multisort($sort, $submenu[$i]['items']);

            $i++;
        }

        $sort = array();
        foreach ($submenu as $key => $value) {
            $sort[$key] = $value['title'] ?? $value['name'];
        }

        array_multisort($sort, $submenu);

        return $submenu;
    }

    /**
     * Create the full Shibboleth URL.
     *
     * @return string
     */
    protected function getShibbolethUrl()
    {
        $shibbolethUrl = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shibboleth_url');

        if (@unserialize($shibbolethUrl) !== false) {
            $shibbolethUrl = unserialize($shibbolethUrl);

            if (getenv('SERVED_BY') === false) {
                throw new Exception\ShibbolethUrlException('The SERVED_BY environment variable does not exist');
            }
            if (!isset($shibbolethUrl[getenv('SERVED_BY')])) {
                throw new Exception\ShibbolethUrlException('Array key ' . getenv('SERVED_BY') . ' does not exist');
            }

            $shibbolethUrl = $shibbolethUrl[getenv('SERVED_BY')];
        }

        $shibbolethUrl .= '%3Fsource=site';

        $server = $this->getRequest()->getServer();
        if (isset($server['X-Forwarded-Host']) && isset($server['REQUEST_URI'])) {
            $shibbolethUrl .= '%26redirect=' . urlencode('https://' . $server['X-Forwarded-Host'] . $server['REQUEST_URI']);
        }

        return $shibbolethUrl;
    }

    /**
     * @return array|null
     */
    protected function getFathomInfo()
    {
        $enableFathom = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.enable_fathom');

        if (getenv('APPLICATION_ENV') == 'development' || !$enableFathom) {
            return null;
        }

        return array(
            'url' => $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('common.fathom_url'),
            'site_id' => $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('common.fathom_site_id'),
        );
    }
}
