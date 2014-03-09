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

namespace ApiBundle\Controller;

use ApiBundle\Document\Code\Authorization as AuthorizationCode,
    CommonBundle\Entity\User\Person\Academic,
    CommonBundle\Form\Auth\Login as LoginForm,
    Zend\View\Model\ViewModel;

/**
 * OAuthController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class OAuthController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    public function authorizeAction()
    {
        $form = new LoginForm();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $this->getAuthentication()->forget();

                $this->getAuthentication()->authenticate(
                    $formData['username'], $formData['password'], $formData['remember_me']
                );

                if ($this->getAuthentication()->isAuthenticated()) {
                    $authorizationCode = new AuthorizationCode(
                        $this->getAuthentication()->getPersonObject(),
                        $this->getKey()
                    );

                    $this->getDocumentManager()->persist($authorizationCode);
                    $this->getDocumentManager()->flush();

                    $this->redirect()->toUrl(
                        $this->getParam('redirect_uri') . '?code=' . $authorizationCode->getCode()
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

        return new ViewModel(
            array(
                'form' => $form
            )
        );
    }
}
