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
    ApiBundle\Document\Token\Access as AccessToken,
    ApiBundle\Document\Token\Refresh as RefreshToken,
    CommonBundle\Entity\User\Person\Academic,
    CommonBundle\Component\Authentication\Authentication,
    CommonBundle\Component\Authentication\Adapter\Doctrine\Shibboleth as ShibbolethAdapter,
    CommonBundle\Component\FlashMessenger\FlashMessage,
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
        if ('code' != $this->getRequest()->getQuery('response_type')) {
            return new ViewModel(
                array(
                    'error' => 'The requested response type is not supported.'
                )
            );
        }

        $this->getSessionStorage()->key = $this->getRequest()->getQuery('client_id');

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
                    $key = $this->getKey('client_id');
                    if ($key instanceof ViewModel)
                        return $key;

                    $authorizationCode = new AuthorizationCode(
                        $this->getAuthentication()->getPersonObject(),
                        $key
                    );

                    $this->getDocumentManager()->persist($authorizationCode);
                    $this->getDocumentManager()->flush();

                    $this->redirect()->toUrl(
                        $this->getRequest()->getQuery('redirect_uri') . '?code=' . $authorizationCode->getCode()
                    );

                    return new ViewModel();
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
                'form' => $form,
                'shibbolethUrl' => $this->_getShibbolethUrl($this->getRequest()->getQuery('redirect_uri')),
            )
        );
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
                $this->getAuthenticationService()
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

                    if ($this->getAuthentication()->isAuthenticated()) {
                        $key = $this->getEntityManager()
                            ->getRepository('ApiBundle\Entity\Key')
                            ->findOneActiveByCode($this->getSessionStorage()->key);

                        $authorizationCode = new AuthorizationCode(
                            $this->getAuthentication()->getPersonObject(),
                            $key
                        );

                        $this->getDocumentManager()->persist($authorizationCode);
                        $this->getDocumentManager()->flush();

                        $this->redirect()->toUrl(
                            $code->getRedirect() . '?code=' . $authorizationCode->getCode()
                        );

                        return new ViewModel();
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
            'api_oauth'
        );

        return new ViewModel();
    }

    public function tokenAction()
    {
        $this->initJson();

        if (!$this->getRequest()->isPost())
            return $this->error(405, 'This endpoint can only be accessed through POST');

        if (null === $this->getRequest()->getPost('grant_type'))
            return $this->error(400, 'The grant type was not specified');

        if ('authorization_code' == $this->getRequest()->getPost('grant_type')) {
            if (null === $this->getRequest()->getPost('code'))
                return $this->error(400, 'No authorization code was provided');

            $authorizationCode = $this->getDocumentManager()
                ->getRepository('ApiBundle\Document\Code\Authorization')
                ->findOneByCode($this->getRequest()->getPost('code'));

            if (null === $authorizationCode)
                return $this->error(500, 'This authorization code does not exist');

            if ($authorizationCode->hasExpired())
                return $this->error(401, 'This authorization code has expired');

            if ($authorizationCode->hasBeenExchanged()) {
                $tokens = array_merge(
                    $this->getDocumentManager()
                        ->getRepository('ApiBundle\Document\Token\Access')
                        ->findAllActiveByAuthorizationCode($authorizationCode),
                    $this->getDocumentManager()
                        ->getRepository('ApiBundle\Document\Token\Refresh')
                        ->findAllActiveByAuthorizationCode($authorizationCode)
                );

                foreach ($tokens as $token)
                    $this->getDocumentManager()->remove($token);

                $this->getDocumentManager()->flush();

                return $this->error(401, 'This authorization code has already been exchanged');
            }

            $key = $this->getKey('client_id');
            if ($key instanceof ViewModel)
                return $key;

            $accessToken = new AccessToken(
                $authorizationCode->getPerson($this->getEntityManager()),
                $authorizationCode
            );
            $this->getDocumentManager()->persist($accessToken);

            $refreshToken = new RefreshToken(
                $authorizationCode->getPerson($this->getEntityManager()),
                $authorizationCode,
                $key
            );
            $this->getDocumentManager()->persist($refreshToken);

            $authorizationCode->exchange();

            $this->getDocumentManager()->flush();

            $result = array(
                'access_token'  => $accessToken->getCode(),
                'expires_in'    => AccessToken::DEFAULT_EXPIRATION_TIME,
                'token_type'    => 'Bearer',
                'refresh_token' => $refreshToken->getCode(),
            );

            return new ViewModel(
                array(
                    'result' => (object) $result
                )
            );
        }

        if ('refresh_token' == $this->getRequest()->getPost('grant_type')) {
            if (null === $this->getRequest()->getPost('refresh_token'))
                return $this->error(400, 'No refresh token was provided');

            $refreshToken = $this->getDocumentManager()
                ->getRepository('ApiBundle\Document\Token\Refresh')
                ->findOneByCode($this->getRequest()->getPost('refresh_token'));

            if (null === $refreshToken)
                return $this->error(500, 'This refresh token does not exist');

            if ($refreshToken->hasExpired())
                return $this->error(401, 'This refresh token has expired');

            if ($refreshToken->hasBeenExchanged()) {
                $tokens = array_merge(
                    $this->getDocumentManager()
                        ->getRepository('ApiBundle\Document\Token\Access')
                        ->findAllActiveByAuthorizationCode($refreshToken->getAuthorizationCode()),
                    $this->getDocumentManager()
                        ->getRepository('ApiBundle\Document\Token\Refresh')
                        ->findAllActiveByAuthorizationCode($refreshToken->getAuthorizationCode())
                );

                foreach ($tokens as $token)
                    $this->getDocumentManager()->remove($token);

                $this->getDocumentManager()->flush();

                return $this->error(401, 'This refresh token has already been exchanged');
            }

            $key = $this->getKey('client_id');
            if ($key instanceof ViewModel)
                return $key;

            $accessToken = new AccessToken(
                $refreshToken->getPerson($this->getEntityManager()),
                $refreshToken->getAuthorizationCode()
            );
            $this->getDocumentManager()->persist($accessToken);

            $refreshToken = new RefreshToken(
                $refreshToken->getPerson($this->getEntityManager()),
                $refreshToken->getAuthorizationCode(),
                $key
            );
            $this->getDocumentManager()->persist($refreshToken);

            $refreshToken->exchange();

            $this->getDocumentManager()->flush();

            $result = array(
                'access_token'  => $accessToken->getCode(),
                'expires_in'    => AccessToken::DEFAULT_EXPIRATION_TIME,
                'token_type'    => 'Bearer',
                'refresh_token' => $refreshToken->getCode(),
            );

            return new ViewModel(
                array(
                    'result' => (object) $result
                )
            );
        }

        return $this->error(400, 'The grant type is invalid');
    }

    /**
     * Create the full Shibboleth URL.
     *
     * @return string
     */
    protected function _getShibbolethUrl($redirect = '')
    {
        $shibbolethUrl = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shibboleth_url');

        try {
            if (false !== ($shibbolethUrl = unserialize($shibbolethUrl))) {
                if (false === getenv('SERVED_BY'))
                    throw new Exception\ShibbolethUrlException('The SERVED_BY environment variable does not exist');
                if (!isset($shibbolethUrl[getenv('SERVED_BY')]))
                    throw new Exception\ShibbolethUrlException('Array key ' . getenv('SERVED_BY') . ' does not exist');

                $shibbolethUrl = $shibbolethUrl[getenv('SERVED_BY')];
            }
        } catch (\ErrorException $e) {
            // No load balancer active
        }

        $shibbolethUrl .= '%3Fsource=api';

        if ('' != $redirect)
            $shibbolethUrl .= '%26redirect=' . urlencode($redirect);

        return $shibbolethUrl;
    }
}
