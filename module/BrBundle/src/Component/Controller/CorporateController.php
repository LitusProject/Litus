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
 
namespace BrBundle\Component\Controller;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Form\Auth\Login as LoginForm,
    Zend\Mvc\MvcEvent;

/**
 * We extend the CommonBundle controller.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class CorporateController extends \CommonBundle\Component\Controller\ActionController
{
    /**
     * Execute the request
     * 
     * @param \Zend\Mvc\MvcEvent $e The MVC event
     * @return array
     * @throws \CommonBundle\Component\Controller\Exception\HasNoAccessException The user does not have permissions to access this resource
     */
    public function execute(MvcEvent $e)
    {
        $result = parent::execute($e);
                
        $result->authenticatedUserObject = $this->getAuthentication()->getPersonObject();
        $result->authenticated = $this->getAuthentication()->isAuthenticated();
        $result->loginForm = new LoginForm($this->url()->fromRoute('corporate_auth', array('action' => 'login')));
        $result->unionUrl = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('union_url');
          
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
            'controller'     => 'index',
            
            'auth_route'     => 'corporate_index',
            'redirect_route' => 'corporate_index'
        );
    }
}
