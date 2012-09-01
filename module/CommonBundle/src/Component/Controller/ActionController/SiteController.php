<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\Controller\ActionController;

use CommonBundle\Form\Auth\Login as LoginForm,
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
     * @param \Zend\Mvc\MvcEvent $e The MVC event
     * @return array
     */
    public function onDispatch(MvcEvent $e)
    {
        $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer')
            ->plugin('headMeta')
            ->appendName('viewport', 'width=device-width, initial-scale=1.0');

        $result = parent::onDispatch($e);

        $result->menu = $this->_buildMenu();

        $loginForm = new LoginForm(
            $this->url()->fromRoute(
                'index',
                array(
                    'action' => 'login'
                )
            )
        );

        $result->authenticated = $this->getAuthentication()->isAuthenticated();
        $result->loginForm = $loginForm;
        $result->shibbolethUrl = $this->_getShibbolethUrl();

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
            'controller'     => 'auth',

            'auth_route'     => 'auth',
            'redirect_route' => 'index'
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

        $i = 0;
        foreach ($categories as $category) {
            $menu[$i] = array(
                'type'  => 'category',
                'name'  => $category->getName(),
                'items' => array()
            );

            $pages = $this->getEntityManager()
                ->getRepository('PageBundle\Entity\Nodes\Page')
                ->findByCategory($category);

            foreach ($pages as $page) {
                $menu[$i]['items'][] = array(
                    'type'  => 'page',
                    'name'  => $page->getName(),
                    'title' => $page->getTitle($this->getLanguage())
                );

                $sort = array();
                foreach ($menu[$i]['items'] as $key => $value)
                    $sort[$key] = $value['title'];

                array_multisort($sort, $menu[$i]['items']);
            }

            $i++;
        }

        $sort = array();
        foreach ($menu as $key => $value)
            $sort[$key] = isset($value['title'])? $value['title'] : $value['name'];

        array_multisort($sort, $menu);

        return $menu;
    }

    /**
     * Create the full Shibboleth URL.
     *
     * @return string
     */
    private function _getShibbolethUrl()
    {
        $shibbolethUrl = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shibboleth_url');

        if ('%2F' != substr($shibbolethUrl, 0, -3))
            $shibbolethUrl .= '%2F';

        return $shibbolethUrl . '?source=site';
    }
}
