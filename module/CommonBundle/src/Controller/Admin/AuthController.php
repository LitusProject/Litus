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
	CommonBundle\Form\Admin\Auth\Login as LoginForm,
	Zend\View\Model\ViewModel;

/**
 * AuthController
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
	        	$formData['username'], $formData['password'], $formData['remember_me']
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

        return new ViewModel(
            array(
            	'authResult' => $authResult,
            )
        );
    }

    public function loginAction()
    {
        $isAuthenticated = $this->getAuthentication()->isAuthenticated();
        
        if ($isAuthenticated) {
            $this->redirect()->toRoute('admin_index');
            
            return new ViewModel();
        }
		            
        return new ViewModel(
            array(
            	'isAuthenticated' => $isAuthenticated,
            	'form' => new LoginForm(),
            )
        );
    }

    public function logoutAction()
    {
        $this->getAuthentication()->forget();

        $this->redirect()->toRoute(
        	'admin_auth'
        );
        
        return new ViewModel();
    }
    
    public function shibbolethAction()
    {   
    	$authentication = new Authentication(
    		new ShibbolethAdapter(
    			$this->getEntityManager(),
    			'CommonBundle\Entity\Users\People\Academic',
    			'universityIdentification'
    		),
    		$this->getLocator()->get('authentication_doctrineservice')
    	);
    	
		$authentication->authenticate(
			$this->getRequest()->server()->get('Shib-Person-uid', '')
		);
    	
    	if ($authentication->isAuthenticated()) {
	    	$this->redirect()->toRoute(
	    		'admin_index'
	    	);
	    }
	    
	    return new ViewModel();
    }
}
