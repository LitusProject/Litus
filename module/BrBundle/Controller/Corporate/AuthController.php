<?php

namespace BrBundle\Controller\Corporate;

use Laminas\View\Model\ViewModel;

/**
 * AuthController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class AuthController extends \BrBundle\Component\Controller\CorporateController
{
    public function loginAction()
    {
        $form = $this->getForm('common_auth_login');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                $this->getAuthentication()->forget();

                $this->getAuthentication()->authenticate(
                    $formData['username'],
                    $formData['password'],
                    $formData['remember_me']
                );

                if ($this->getAuthentication()->isAuthenticated()) {
                    $this->flashMessenger()->success(
                        'SUCCESS',
                        'You have been successfully logged in!'
                    );
                } else {
                    $this->flashMessenger()->error(
                        'Error',
                        'The given username and password did not match. Please try again.'
                    );
                }
            } else {
                $this->flashMessenger()->error(
                    'Error',
                    'The given username and password did not match. Please try again.'
                );
            }
        }

        $this->redirect()->toRoute(
            'br_corporate_index',
            array(
                'language' => $this->getLanguage()->getAbbrev(),
            )
        );

        return new ViewModel();
    }

    public function logoutAction()
    {
        $this->getAuthentication()->forget();

        $this->flashMessenger()->success(
            'SUCCESS',
            'You have been successfully logged out!'
        );

        $this->redirect()->toRoute(
            'br_corporate_index',
            array(
                'language' => $this->getLanguage()->getAbbrev(),
            )
        );

        return new ViewModel();
    }
}