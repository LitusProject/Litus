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

namespace CommonBundle\Component\Controller\ActionController;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Entity\General\Language,
    CommonBundle\Form\Auth\Login as LoginForm,
    Zend\Mvc\MvcEvent;

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
     * @param \Zend\Mvc\MvcEvent $e The MVC event
     * @return array
     */
    public function onDispatch(MvcEvent $e)
    {
        $result = parent::onDispatch($e);

        $language = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findOneByAbbrev('en');

        if (null === $language) {
            $language = new Language(
                'en', 'English'
            );
        }

        $result->language = $language;
        $result->now = array(
            'iso8601' => date('c', time()),
            'display' => date('l, F j Y, H:i', time())
        );

        if ($this->hasAccess()->resourceAction('cudi_admin_stock_period', 'new')) {
            $period = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Stock\Period')
                ->findOneActive();

            if (null === $period) {
                $this->addPersistentFlashMessage(
                    $result,
                    new FlashMessage(
                        FlashMessage::ERROR,
                        'Error',
                        'Please create a new stock period! To do so, please click <a href="' . $this->url()->fromRoute('cudi_admin_stock_period', array('action' => 'new')) . '">here</a>.'
                    )
                );
            } elseif ($period->getStartDate()->format('Y') < date('Y') || $period->getStartDate() < $this->getCurrentAcademicYear()->getStartDate()) {
                $this->addPersistentFlashMessage(
                    $result,
                    new FlashMessage(
                        FlashMessage::WARNING,
                        'Warning',
                        'Please create a new stock period! To do so, please click <a href="' . $this->url()->fromRoute('cudi_admin_stock_period', array('action' => 'new')) . '">here</a>.'
                    )
                );
            }
        }

        $result->servedBy = null;
        if (false !== getenv('SERVED_BY'))
            $result->servedBy = ucfirst(getenv('SERVED_BY'));

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

        $this->getTranslator()->setCache($this->getCache())
            ->setLocale($language->getAbbrev());

        $this->getMvcTranslator()->setCache($this->getCache())
            ->setLocale($language->getAbbrev());

        \Zend\Validator\AbstractValidator::setDefaultTranslator($this->getTranslator());
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
            'redirect_route' => 'common_admin_index'
        );
    }

    /**
     * Get the current academic year.
     *
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    protected function getCurrentAcademicYear($organization = true)
    {
        return parent::getCurrentAcademicYear($organization);
    }
}
