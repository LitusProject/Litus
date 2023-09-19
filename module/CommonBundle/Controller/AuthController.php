<?php

namespace CommonBundle\Controller;

use CommonBundle\Component\Authentication\Adapter\Doctrine\Shibboleth as ShibbolethAdapter;
use CommonBundle\Component\Authentication\Authentication;
use CommonBundle\Component\Controller\Exception\HasNoAccessException;
use Laminas\View\Model\ViewModel;

/**
 * AuthController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class AuthController extends \CommonBundle\Component\Controller\ActionController\SiteController
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

                    if ($this->getParam('redirect') !== null) {
                        return $this->redirect()->toUrl(
                            urldecode($this->getParam('redirect'))
                        );
                    } else {
                        $this->redirect()->toRoute(
                            'common_index',
                            array(
                                'language' => $this->getLanguage()->getAbbrev(),
                            )
                        );
                    }
                } else {
                    $this->flashMessenger()->error(
                        'Error',
                        'You could not be logged in!'
                    );

                    $this->redirect()->toRoute(
                        'common_index',
                        array(
                            'action' => 'index',
                        )
                    );

                    return new ViewModel();
                }
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
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
                'common_index',
                array(
                    'language' => $this->getLanguage()->getAbbrev(),
                )
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
                ->getRepository('CommonBundle\Entity\User\Shibboleth\Code')
                ->findLastByUniversityIdentification($this->getParam('identification'));

            if ($code !== null) {
                if ($code->validate($this->getParam('hash'))) {
                    $this->getEntityManager()->remove($code);
                    $this->getEntityManager()->flush();

                    $this->getAuthentication()->forget();

                    $authentication->authenticate(
                        $this->getParam('identification'),
                        '',
                        true,
                        true
                    );

                    if ($authentication->isAuthenticated()) {
                        $registrationEnabled = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('secretary.enable_registration');

                        if ($registrationEnabled) {
                            $academic = $this->getEntityManager()
                                ->getRepository('CommonBundle\Entity\User\Person\Academic')
                                ->findOneByUniversityIdentification($this->getParam('identification'));

                            if ($academic !== null && ($academic->getOrganizationStatus($this->getCurrentAcademicYear()) === null || $academic->getUniversityStatus($this->getCurrentAcademicYear()) === null)) {
                                $this->redirect()->toRoute(
                                    'secretary_registration'
                                );

                                return new ViewModel();
                            }
                        }

                        if ($code->getRedirect() !== null) {
                            $this->redirect()->toUrl(
                                $code->getRedirect()
                            );
                        } else {
                            $this->redirect()->toRoute(
                                'common_index'
                            );
                        }

                        return new ViewModel();
                    } else {
                        $this->redirect()->toRoute(
                            'secretary_registration'
                        );

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
