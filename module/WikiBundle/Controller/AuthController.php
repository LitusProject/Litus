<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace WikiBundle\Controller;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Authentication\Authentication,
    CommonBundle\Component\Authentication\Adapter\Doctrine\Shibboleth as ShibbolethAdapter,
    WikiBundle\Form\Auth\Login as LoginForm,
    Zend\View\Model\ViewModel;

/**
 * AuthController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class AuthController extends \WikiBundle\Component\Controller\ActionController\WikiController
{
    public function loginAction()
    {
        $form = new LoginForm();

        if ($this->getAuthentication()->isAuthenticated()) {
            if ($this->getAuthentication()->isExternallyAuthenticated()) {
                $this->redirectAfterAuthentication();

                return new ViewModel();
            }

            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::NOTICE,
                    'Notice',
                    'You have to login again to go the wiki.'
                )
            );

            $form->setUsername(
                $this->getAuthentication()->getPersonObject()->getUsername()
            );
        }

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $this->getAuthentication()->forget();

                $this->getAuthentication()->authenticate(
                    $formData['username'], $formData['password'], true
                );

                if ($this->getAuthentication()->isAuthenticated()) {
                    if (!$this->getAuthentication()->isExternallyAuthenticated()) {
                        throw new \Exception('Impossible state: logged in but not externally visible');
                    }

                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'Success',
                            'You have been successfully logged in!'
                        )
                    );

                    $this->redirectAfterAuthentication();
                } else {
                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::ERROR,
                            'Error',
                            'You could not be logged in!'
                        )
                    );

                    $this->redirect()->toRoute(
                        'wiki_auth',
                        array(
                            'action' => 'login'
                        )
                    );

                    return new ViewModel();
                }
            }
        }

        return new ViewModel(
            array(
                'form' => $form
            )
        );
    }

    public function logoutAction()
    {
        $session = $this->getAuthentication()->forget();

        if (null !== $session && $session->isShibboleth()) {
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
        if ($this->getAuthentication()->isAuthenticated()) {
            $this->redirectAfterAuthentication();

            return new ViewModel();
        }

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
                    } else {
                        $this->redirect()->toRoute(
                            'wiki_auth',
                            array(
                                'action' => 'login'
                            )
                        );

                        return new ViewModel();
                    }
                }
            }
        }

        $this->redirectAfterAuthentication();

        return new ViewModel();
    }

    protected function redirectAfterAuthentication()
    {
        if (!$this->getAuthentication()->isAuthenticated()
            || !$this->getAuthentication()->isExternallyAuthenticated())
                return null;

        if (null !== $this->getParam('redirect')) {
            return $this->redirect()->toUrl(
                urldecode($this->getParam('redirect'))
            );
        } else {
            return $this->redirect()->toUrl(
                $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('wiki.url')
            );
        }
    }
}
