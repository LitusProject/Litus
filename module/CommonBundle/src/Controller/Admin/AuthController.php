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

namespace CommonBundle\Controller\Admin;

use CommonBundle\Component\Authentication\Authentication,
	CommonBundle\Component\Authentication\Adapter\Doctrine\Shibboleth as ShibbolethAdapter,
	CommonBundle\Form\Admin\Auth\Login as LoginForm;

/**
 * Authentication controller, providing basic actions like login and logout.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class AuthController extends \CommonBundle\Component\Controller\ActionController
{
	public function authenticateAction()
    {
        $this->initAjax();

		$authResult = array(
		    'result' => false,
		    'reason' => 'NOT_POST'
		);
		
        if ($this->getRequest()->isPost()) {
	        parse_str(
	        	$this->getRequest()->post()->get('formData'), $formData
	        );
	        
	        $this->getAuthentication()->authenticate(
	        	$formData['username'], $formData['password']
	        );
	        
	        if ($this->getAuthentication()->isAuthenticated()) {
	            $authResult = array(
	            	'result' => true,
	            	'reason' => ''
	            );
	        } else {
	            $authResult['reason'] = 'USERNAME_PASSWORD';
	        }
        }

        return array(
        	'authResult' => $authResult
        );
    }

    public function loginAction()
    {
        $isAuthenticated = $this->getAuthentication()->isAuthenticated();
        
        if ($isAuthenticated) {
            $this->redirect()->toRoute('admin_dashboard');
            
            return;
        }
		            
        return array(
        	'isAuthenticated' => $isAuthenticated,
        	'form' => new LoginForm()
        );
    }

    public function logoutAction()
    {
        $this->getAuthentication()->forget();

        $this->redirect()->toRoute(
        	'admin_auth'
        );
        
        return;
    }
    
    public function shibbolethAction()
    {
    	$authentication = new Authentication(
    		new ShibbolethAdapter(
    			$this->getEntityManager(),
    			'CommonBundle\Entity\Users\Person',
    			'username'
    		),
    		$this->getLocator()->get('authentication_doctrineservice')
    	);
    	
    	var_dump($this->getRequest()->server()->get('Shib-Person-uid'));
    	
		$authentication->authenticate(
			$this->getRequest()->server()->get('Shib-Person-uid'), ''
		);
		
		var_dump($authentication);
    	
    	if ($authentication->isAuthenticated()) {
	    	$this->redirect()->toRoute(
	    		'admin_dashboard'
	    	);
	    }
    }
}
