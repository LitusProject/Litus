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

namespace FormBundle\Component\Controller;

use CommonBundle\Component\Controller\ActionController\Exception\ShibbolethUrlException,
    CommonBundle\Component\Controller\Exception\HasNoAccessException,
    CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Form\Auth\Login as LoginForm,
    Zend\Mvc\MvcEvent;

/**
 * We extend the CommonBundle controller.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class FormController extends \CommonBundle\Component\Controller\ActionController
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
        $result = parent::onDispatch($e);

        $result->loginForm = new LoginForm($this->url()->fromRoute('form_manage_auth', array('action' => 'login')));
        $result->organizationUrl = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('organization_url');
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
            'action'         => 'index',
            'controller'     => 'common_index',

            'auth_route'     => 'form_manage',
            'redirect_route' => 'form_manage'
        );
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

        try {
            if (false !== ($shibbolethUrl = unserialize($shibbolethUrl))) {
                if (false === getenv('SERVED_BY'))
                    throw new ShibbolethUrlException('The SERVED_BY environment variable does not exist');
                if (!isset($shibbolethUrl[getenv('SERVED_BY')]))
                    throw new ShibbolethUrlException('Array key ' . getenv('SERVED_BY') . ' does not exist');

                $shibbolethUrl = $shibbolethUrl[getenv('SERVED_BY')];
            }
        } catch(\ErrorException $e) {}

        $shibbolethUrl .= '?source=form';

        if (isset($_SERVER['HTTP_HOST']) && isset($_SERVER['REQUEST_URI']))
            $shibbolethUrl .= '%26redirect=' . urlencode(((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

        return $shibbolethUrl;
    }
}
