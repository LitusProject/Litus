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

namespace CommonBundle\Component\Controller\ActionController;

use CommonBundle\Form\Auth\Login as LoginForm,
    PageBundle\Entity\Node\Page,
    Zend\Mvc\MvcEvent;

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
     * @param  \Zend\Mvc\MvcEvent $e The MVC event
     * @return array
     */
    public function onDispatch(MvcEvent $e)
    {
        $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer')
            ->plugin('headMeta')
            ->appendName('viewport', 'width=device-width, initial-scale=1.0');

        $result = parent::onDispatch($e);

        $result->menu = $this->_buildMenu();

        $result->shibbolethUrl = $this->_getShibbolethUrl();

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
            'redirect_route' => 'common_index'
        );
    }

    /**
     * This is the top navigation menu, displayed on every page.
     *
     * @return array
     */
    private function _buildMenu()
    {
        $categories = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Category')
            ->findByParent(null);

        $menu = array();

        $activeItem = -1;

        $i = 0;
        foreach ($categories as $category) {
            $menu[$i] = array(
                'type'  => 'category',
                'name'  => $category->getName($this->getLanguage()),
                'items' => array(),
                'active' => false,
            );

            $pages = $this->getEntityManager()
                ->getRepository('PageBundle\Entity\Node\Page')
                ->findBy(
                    array(
                        'category' => $category,
                        'parent' => null,
                        'endTime' => null
                    )
                );

            $links = $this->getEntityManager()
                ->getRepository('PageBundle\Entity\Link')
                ->findBy(
                    array(
                        'category' => $category,
                        'parent' => null
                    )
                );

            foreach ($pages as $page) {
                $menu[$i]['items'][] = array(
                    'type'  => 'page',
                    'id'    => $page->getId(),
                    'name'  => $page->getName(),
                    'title' => $page->getTitle($this->getLanguage())
                );

                if ($activeItem < 0 && $this->getParam('controller') == 'page' && $this->getParam('name') == $page->getName())
                    $activeItem = $i;
            }

            foreach ($links as $link) {
                $menu[$i]['items'][] = array(
                    'type' => 'link',
                    'id'   => $link->getId(),
                    'name' => $link->getName($this->getLanguage()),
                    'url'  => $link->getUrl($this->getLanguage()),
                );

                if ($activeItem < 0 && strpos($this->getRequest()->getRequestUri(), $link->getUrl($this->getLanguage())) === 0)
                    $activeItem = $i;
            }

            $sort = array();
            foreach ($menu[$i]['items'] as $key => $value)
                $sort[$key] = isset($value['title']) ? $value['title'] : $value['name'];

            array_multisort($sort, $menu[$i]['items']);

            $i++;
        }

        if ($activeItem >= 0)
            $menu[$activeItem]['active'] = true;

        $sort = array();
        foreach ($menu as $key => $value)
            $sort[$key] = isset($value['title'])? $value['title'] : $value['name'];

        array_multisort($sort, $menu);

        return $menu;
    }

    /**
     * Build a pages submenu.
     *
     * @param  \PageBundle\Entity\Node\Page $page The page
     * @return array
     */
    protected function _buildSubmenu(Page $page)
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
                'type'     => 'page',
                'id'       => $page->getId(),
                'name'     => $page->getName(),
                'parent'   => $page->getParent()->getName(),
                'title'    => $page->getTitle($this->getLanguage()),
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
                'items' => array()
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
            foreach ($submenu[$i]['items'] as $key => $value)
                $sort[$key] = isset($value['title']) ? $value['title'] : $value['name'];

            array_multisort($sort, $submenu[$i]['items']);

            $i++;
        }

        $sort = array();
        foreach ($submenu as $key => $value)
            $sort[$key] = isset($value['title']) ? $value['title'] : $value['name'];

        array_multisort($sort, $submenu);

        return $submenu;
    }

    /**
     * Create the full Shibboleth URL.
     *
     * @return string
     */
    protected function _getShibbolethUrl()
    {
        $shibbolethUrl = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shibboleth_url');

        try {
            if (false !== ($shibbolethUrl = unserialize($shibbolethUrl))) {
                if (false === getenv('SERVED_BY'))
                    throw new Exception\ShibbolethUrlException('The SERVED_BY environment variable does not exist');
                if (!isset($shibbolethUrl[getenv('SERVED_BY')]))
                    throw new Exception\ShibbolethUrlException('Array key ' . getenv('SERVED_BY') . ' does not exist');

                $shibbolethUrl = $shibbolethUrl[getenv('SERVED_BY')];
            }
        } catch (\ErrorException $e) {}

        $shibbolethUrl .= '%3Fsource=site';

        $server = $this->getRequest()->getServer();
        if (isset($server['HTTP_HOST']) && isset($server['REQUEST_URI']))
            $shibbolethUrl .= '%26redirect=' . urlencode('https://' . $server['HTTP_HOST'] . $server['REQUEST_URI']);

        return $shibbolethUrl;
    }
}
