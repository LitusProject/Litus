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

use CommonBundle\Form\Admin\Auth\Login as LoginForm;

/**
 * Authentication controller, providing basic actions like login and logout.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class AuthController extends \CommonBundle\Component\Controller\ActionController
{
    public function loginAction()
    {
        $isAuthenticated = $this->getAuthentication()->isAuthenticated();
        
        if (!$isAuthenticated)
            $form = new LoginForm();
		            
        return array(
        	'isAuthenticated' => $isAuthenticated,
        	'form' => $form
        );
    }

    public function logoutAction()
    {
        $this->getAuthentication()->forget();

        $this->redirect()->toRoute('admin_auth');
    }

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
	            $authResult['result'] = true;
	        } else {
	            $authResult['reason'] = 'USERNAME_PASSWORD';
	        }
        }

        return array(
        	'authResult' => $authResult
        );
    }
}
