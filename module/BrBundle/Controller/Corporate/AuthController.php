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

    public function resetPasswordAction()
    {
        $form = $this->getForm('br_corporate_auth_resetpassword');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getData();

                $users = $this->getEntityManager()
                    ->getRepository('BrBundle\Entity\User\Person\Corporate')
                    ->findBy(
                        array(
                            'email' => $formData['email'],
                        )
                    );

                if (count($users) > 1) { // multiple users with this email -> don't reset
                    $brEmail = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('br.communication_mail');

                    $this->flashMessenger()->error(
                        'Error',
                        'There are more than 1 accounts with this email, please contact ' . $brEmail . ' to reset your password.'
                    );
                } elseif (count($users) == 0) { // no users with this email -> don't reset
                    $this->flashMessenger()->error(
                        'Error',
                        'There is no account associated with this email.'
                    );
                } else { // exactly 1 user with this email -> reset
                    $users[0]->activate(
                        $this->getEntityManager(),
                        $this->getMailTransport(),
                        false,
                        'br.account_activated_mail',
                        86400 * 30
                    );

                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'Succes',
                        'An email to reset your password has been send.'
                    );
                }

                $this->redirect()->toRoute(
                    'br_corporate_index',
                    array(
                        'language' => $this->getLanguage()->getAbbrev(),
                        'action' => 'login',
                    )
                );
                return new ViewModel();
            } else {
                $brEmail = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('br.communication_mail');

                $this->flashMessenger()->error(
                    'Error',
                    'There was an error resetting your password, please contact ' . $brEmail
                );
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }
}
