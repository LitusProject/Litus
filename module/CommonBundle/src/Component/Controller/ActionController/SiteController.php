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
 */
class SiteController extends \CommonBundle\Component\Controller\ActionController
{
    /**
     * Execute the request.
     * 
     * @param \Zend\Mvc\MvcEvent $e The MVC event
     * @return array
     */
    public function execute(MvcEvent $e)
    {
        $this->getLocator()->get('Zend\View\Renderer\PhpRenderer')
            ->plugin('headMeta')
            ->appendName('viewport', 'width=device-width, initial-scale=1.0');
        
        $result = parent::execute($e);
        
        $loginForm = new LoginForm(
            $this->url()->fromRoute(
                'index',
                array(
                    'action' => 'login'
                )
            )
        );
        
        $result->authenticatedUserObject = $this->getAuthentication()->getPersonObject();
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
