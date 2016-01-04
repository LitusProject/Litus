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

use CommonBundle\Component\Util\NamedPriorityQueue,
    CommonBundle\Entity\General\Language,
    Zend\Mvc\MvcEvent,
    Zend\Validator\AbstractValidator;

/**
 * We extend the CommonBundle controller.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class AdminController extends \CommonBundle\Component\Controller\ActionController
{
    /**
     * Execute the request.
     *
     * @param  MvcEvent $e The MVC event
     * @return array
     */
    public function onDispatch(MvcEvent $e)
    {
        $result = parent::onDispatch($e);

        $result->language = $this->getLanguage();
        $result->now = array(
            'iso8601' => date('c', time()),
            'display' => date('l, F j Y, H:i', time()),
        );

        if ($this->hasAccess()->toResourceAction('cudi_admin_stock_period', 'new')) {
            $period = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Period')
                ->findOneActive();

            $result->createNewStockPeriod = (
                null === $period
                || ($period->getStartDate()->format('Y') < date('Y') && (($period->getStartDate()->format('n') < 12) || ($period->getStartDate()->format('j') <= 15)))
                || $period->getStartDate() < $this->getCurrentAcademicYear()->getStartDate()
            );
        }

        $result->servedBy = null;
        if (false !== getenv('SERVED_BY')) {
            $result->servedBy = ucfirst(getenv('SERVED_BY'));
        }

        $result->menu = $this->getMenu();

        $e->setResult($result);

        return $result;
    }

    /**
     * Initializes the localization
     *
     * @return void
     */
    protected function initLocalization()
    {
        $language = $this->getLanguage();
        $translator = $this->getTranslator()->getTranslator();

        $translator->setCache($this->getCache())
            ->setLocale($language->getAbbrev());

        AbstractValidator::setDefaultTranslator($this->getTranslator());
    }

    /**
     * Returns the language that is currently requested.
     *
     * @return \CommonBundle\Entity\General\Language
     */
    protected function getLanguage()
    {
        if (null !== $this->language) {
            return $this->language;
        }

        $language = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findOneByAbbrev('en');

        if (null === $language) {
            $language = new Language(
                'en', 'English'
            );

            $this->getEntityManager()->persist($language);
            $this->getEntityManager()->flush();
        }

        $this->language = $language;

        return $language;
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

            'auth_route'     => 'common_admin_auth',
            'redirect_route' => 'common_admin_index',
        );
    }

    /**
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    public function findCurrentAcademicYear()
    {
        return $this->getCurrentAcademicYear(true);
    }

    /**
     * @param  string            $controller
     * @param  string|array      $settings
     * @param  \SplPriorityQueue $menu
     * @return boolean
     */
    private function addToMenu($controller, $settings, \SplPriorityQueue &$menu)
    {
        if (!is_array($settings)) {
            $settings = array('title' => $settings);
        }
        if (!array_key_exists('action', $settings)) {
            $settings['action'] = 'manage';
        }
        $settings['controller'] = $controller;

        if (array_key_exists('priority', $settings)) {
            $priority = array($settings['priority'], $settings['title']);
        } else {
            $priority = $settings['title'];
        }

        if ($this->hasAccess()->toResourceAction($controller, $settings['action'])) {
            $menu->insert($settings, $priority);

            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    private function getMenu()
    {
        $config = $this->getServiceLocator()->get('Config');
        $config = $config['litus']['admin'];

        $currentController = $this->getParam('controller');

        $general = array();

        foreach ($config['general'] as $name => $submenu) {
            $newSubmenu = new NamedPriorityQueue();

            foreach ($submenu as $controller => $settings) {
                $this->addToMenu($controller, $settings, $newSubmenu);
            }

            if (count($newSubmenu)) {
                $general[$name] = $newSubmenu->toArray();
            }
        }

        $submenus = array();

        foreach ($config['submenus'] as $name => $submenu) {
            $newSubmenu = array();

            natsort($submenu['subtitle']);
            $lastSubtitle = array_pop($submenu['subtitle']);
            $newSubmenu['subtitle'] = implode(', ', $submenu['subtitle']) . ' & ' . $lastSubtitle;

            $active = false;
            $newSubmenuItems = new NamedPriorityQueue();

            foreach ($submenu['items'] as $controller => $settings) {
                $this->addToMenu($controller, $settings, $newSubmenuItems);

                if ($currentController === $controller) {
                    $active = true;
                }
            }

            if (!$active && array_key_exists('controllers', $submenu)) {
                foreach ($submenu['controllers'] as $controller) {
                    if ($currentController === $controller) {
                        $active = true;
                        break;
                    }
                }
            }

            $newSubmenu['active'] = $active;
            $newSubmenu['items']  = $newSubmenuItems->toArray();

            if (count($newSubmenu['items'])) {
                $submenus[$name] = $newSubmenu;
            }
        }

        uksort($submenus, 'strnatcmp');

        return array(
            'general'  => $general,
            'submenus' => $submenus,
        );
    }

    /**
     * @return \CommonBundle\Component\Form\Factory
     */
    protected function getFormFactory()
    {
        return $this->getServiceLocator()->get('formfactory.admin');
    }
}
