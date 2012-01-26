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
class AuthController extends \CommonBundle\Component\Controller\ControllerAction
{
    public function loginAction()
    {
        $this->view->isAuthenticated = $this->getAuthentication()->isAuthenticated();
        
        if (!$this->getAuthentication()->isAuthenticated())
            $this->view->form = new LoginForm();
    }

    public function logoutAction()
    {
        $this->broker('viewRenderer')->setNoRender();
        $this->getAuthentication()->forget();

        $this->_redirect('login');
    }

	public function authenticateAction()
    {
        $this->_initAjax();

        $postData = $this->getRequest()->getPost();
        parse_str($postData['formData'], $formData);
        $authResult = array(
            'result' => false,
            'reason' => ''
        );

        $this->getAuthentication()
            ->authenticate($formData['username'], $formData['password']);
        
        if ($this->getAuthentication()->isAuthenticated()) {
            $authResult['result'] = true;
        } else {
            $authResult['reason'] = 'USERNAME_PASSWORD';
        }

        return $authResult;
    }
}
