<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Controller\Prof;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Authentication\Authentication,
    CommonBundle\Component\Authentication\Adapter\Doctrine\Shibboleth as ShibbolethAdapter,
    CommonBundle\Form\Auth\Login as LoginForm,
    Zend\View\Model\ViewModel;

/**
 * AuthController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class AuthController extends \CudiBundle\Component\Controller\ProfController
{
    public function loginAction()
    {
        $form = new LoginForm();

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $this->getAuthentication()->forget();

                $this->getAuthentication()->authenticate(
                    $formData['username'], $formData['password'], $formData['remember_me']
                );

                if ($this->getAuthentication()->isAuthenticated()) {
                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'SUCCESS',
                            'You have been successfully logged in!'
                        )
                    );
                } else {
                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::ERROR,
                            'Error',
                            'The given username and password did not match. Please try again.'
                        )
                    );
                }
            }
        }

        $this->redirect()->toRoute(
            'cudi_prof_index',
            array(
                'language' => $this->getLanguage()->getAbbrev(),
            )
        );

        return new ViewModel();
    }

    public function logoutAction()
    {
        $session = $this->getAuthentication()->forget();

        if ($session->isShibboleth()) {
            $shibbolethLogoutUrl = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shibboleth_logout_url');

            $this->redirect()->toUrl($shibbolethLogoutUrl);
        } else {
            $this->redirect()->toRoute(
                'cudi_prof_index',
                array(
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );
        }

        return new ViewModel();
    }

    public function shibbolethAction()
    {
        if ((null !== $this->getParam('identification')) && (null !== $this->getParam('hash'))) {
            $authentication = new Authentication(
                new ShibbolethAdapter(
                    $this->getEntityManager(),
                    'CommonBundle\Entity\User\Person\Academic',
                    'universityIdentification'
                ),
                $this->getServiceLocator()->get('authentication_doctrineservice')
            );

            $code = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\User\Shibboleth\Code')
                ->findLastByUniversityIdentification($this->getParam('identification'));

            if (null !== $code) {
                if ($code->validate($this->getParam('hash'))) {
                    $this->getEntityManager()->remove($code);
                    $this->getEntityManager()->flush();

                    $this->getAuthentication()->forget();

                    $authentication->authenticate(
                        $this->getParam('identification'), '', true, true
                    );

                    if ($authentication->isAuthenticated()) {
                        if (null !== $code->getRedirect()) {
                            $this->redirect()->toUrl(
                                $code->getRedirect()
                            );

                            return new ViewModel();
                        }
                    }
                }
            }
        }

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::ERROR,
                'Error',
                'Something went wrong while logging you in. Please try again later.'
            )
        );

        $this->redirect()->toRoute(
            'cudi_prof_index'
        );

        return new ViewModel();
    }
}
