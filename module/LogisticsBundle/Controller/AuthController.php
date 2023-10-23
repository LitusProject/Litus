<?php

namespace LogisticsBundle\Controller;

use CommonBundle\Component\Authentication\Adapter\Doctrine\Shibboleth as ShibbolethAdapter;
use CommonBundle\Component\Authentication\Authentication;
use CommonBundle\Component\Controller\Exception\HasNoAccessException;
use Laminas\View\Model\ViewModel;

/**
 * AuthController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class AuthController extends \LogisticsBundle\Component\Controller\LogisticsController
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
            }
        }

        $this->redirect()->toRoute(
            'logistics_catalog',
            array(
                'language' => $this->getLanguage()->getAbbrev(),
            )
        );

        return new ViewModel();
    }

    public function logoutAction()
    {
        $session = $this->getAuthentication()->forget();

        if ($session !== null && $session->isShibboleth()) {
            $shibbolethLogoutUrl = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shibboleth_logout_url');

            $this->redirect()->toUrl($shibbolethLogoutUrl);
        } else {
            $this->redirect()->toRoute(
                'logistics_auth'
            );
        }

        return new ViewModel();
    }

    public function shibbolethAction()
    {
        if ($this->getParam('identification') !== null && $this->getParam('hash') !== null) {
            $authentication = new Authentication(
                new ShibbolethAdapter(
                    $this->getEntityManager(),
                    'CommonBundle\Entity\User\Person\Academic',
                    'universityIdentification'
                ),
                $this->getAuthenticationService()
            );

            $code = $this->getEntityManager()
                ->getRepository('CommonBundle\Repository\User\Shibboleth\Code')
                ->findLastByUniversityIdentification($this->getParam('identification'));

            if ($code !== null) {
                if ($code->validate($this->getParam('hash'))) {
                    $this->getEntityManager()->remove($code);
                    $this->getEntityManager()->flush();

                    $authentication->authenticate(
                        $this->getParam('identification'),
                        '',
                        true,
                        true
                    );

                    if ($authentication->isAuthenticated()) {
                        if ($code->getRedirect() !== null) {
                            $this->redirect()->toUrl(
                                $code->getRedirect()
                            );
                        } else {
                            $this->redirect()->toRoute(
                                'logistics_catalog',
                                array(
                                    'language' => $this->getLanguage()->getAbbrev(),
                                )
                            );
                        }

                        return new ViewModel();
                    }
                }
            }
        }

        throw new HasNoAccessException(
            'Something went wrong while logging you in'
        );
    }
}
