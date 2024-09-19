<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\Code\Authorization as AuthorizationCode;
use ApiBundle\Entity\Token\Access as AccessToken;
use ApiBundle\Entity\Token\Refresh as RefreshToken;
use CommonBundle\Component\Authentication\Adapter\Doctrine\Shibboleth as ShibbolethAdapter;
use CommonBundle\Component\Authentication\Authentication;
use CommonBundle\Component\Controller\ActionController\Exception\ShibbolethUrlException;
use CommonBundle\Component\Controller\Exception\HasNoAccessException;
use Laminas\View\Model\ViewModel;

/**
 * OAuthController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class OAuthController extends \ApiBundle\Component\Controller\ActionController\ApiController
{
    public function authorizeAction()
    {
        if ($this->getRequest()->getQuery('response_type') != 'code') {
            return new ViewModel(
                array(
                    'error' => 'The requested response type is not supported.',
                )
            );
        }

        if ($this->getAuthentication()->isAuthenticated()) {
            $key = $this->getKey('client_id');
            if ($key instanceof ViewModel) {
                return $key;
            }

            $authorizationCode = new AuthorizationCode(
                $this->getAuthentication()->getPersonObject(),
                $key
            );

            $this->getEntityManager()->persist($authorizationCode);
            $this->getEntityManager()->flush();

            $this->redirect()->toUrl(
                $this->getRequest()->getQuery('redirect_uri') . '?code=' . $authorizationCode->getCode() . '&state=' . $this->getRequest()->getQuery('state')
            );

            return new ViewModel();
        }

        $this->getSessionContainer()->key = $this->getRequest()->getQuery('client_id');

        // Store state in session
        $state = $this->getRequest()->getQuery('state');
        $this->getSessionContainer()->state = $state;

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
                    $key = $this->getKey('client_id');
                    if ($key instanceof ViewModel) {
                        return $key;
                    }

                    $authorizationCode = new AuthorizationCode(
                        $this->getAuthentication()->getPersonObject(),
                        $key
                    );

                    $this->getEntityManager()->persist($authorizationCode);
                    $this->getEntityManager()->flush();

                    $this->redirect()->toUrl(
                        $this->getRequest()->getQuery('redirect_uri') . '?code=' . $authorizationCode->getCode() . '&state=' . $this->getRequest()->getQuery('state')
                    );

                    return new ViewModel();
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

        return new ViewModel(
            array(
                'form'          => $form,
                'shibbolethUrl' => $this->getShibbolethUrl($this->getRequest()->getQuery('redirect_uri')),
            )
        );
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

                    if ($this->getAuthentication()->isAuthenticated()) {
                        $key = $this->getEntityManager()
                            ->getRepository('ApiBundle\Entity\Key')
                            ->findOneActiveByCode($this->getSessionContainer()->key);

                        $authorizationCode = new AuthorizationCode(
                            $this->getAuthentication()->getPersonObject(),
                            $key
                        );

                        $this->getEntityManager()->persist($authorizationCode);
                        $this->getEntityManager()->flush();

                        $redirectUri = $code->getRedirect() . '?code=' . $authorizationCode->getCode();

                        $state = $this->getSessionContainer()->state;
                        if ($state) {
                            $redirectUri .= '&state=' . $state;
                        }

                        $this->redirect()->toUrl($redirectUri);

                        return new ViewModel();
                    }
                }
            }
        }

        throw new HasNoAccessException(
            'Something went wrong while logging you in'
        );
    }

    public function tokenAction()
    {
        $this->initJson();

        if (!$this->getRequest()->isPost()) {
            return $this->error(405, 'This endpoint can only be accessed through POST');
        }

        if ($this->getRequest()->getPost('grant_type') === null) {
            return $this->error(400, 'The grant type was not specified');
        }

        if ($this->getRequest()->getPost('grant_type') == 'authorization_code') {
            if ($this->getRequest()->getPost('code') === null) {
                return $this->error(400, 'No authorization code was provided');
            }

            $authorizationCode = $this->getEntityManager()
                ->getRepository('ApiBundle\Entity\Code\Authorization')
                ->findOneByCode($this->getRequest()->getPost('code'));

            if ($authorizationCode === null) {
                return $this->error(404, 'This authorization code does not exist');
            }

            if ($authorizationCode->hasExpired()) {
                return $this->error(401, 'This authorization code has expired');
            }

            if ($authorizationCode->hasBeenExchanged()) {
                $tokens = array_merge(
                    $this->getEntityManager()
                        ->getRepository('ApiBundle\Entity\Token\Access')
                        ->findAllActiveByAuthorizationCode($authorizationCode),
                    $this->getEntityManager()
                        ->getRepository('ApiBundle\Entity\Token\Refresh')
                        ->findAllActiveByAuthorizationCode($authorizationCode)
                );

                foreach ($tokens as $token) {
                    $this->getEntityManager()->remove($token);
                }

                $this->getEntityManager()->flush();

                return $this->error(401, 'This authorization code has already been exchanged');
            }

            $key = $this->getKey('client_id');
            if ($key instanceof ViewModel) {
                return $key;
            }

            $accessToken = new AccessToken(
                $authorizationCode->getPerson(),
                $authorizationCode
            );
            $this->getEntityManager()->persist($accessToken);

            $refreshToken = new RefreshToken(
                $authorizationCode->getPerson(),
                $authorizationCode,
                $key
            );
            $this->getEntityManager()->persist($refreshToken);

            $authorizationCode->exchange();

            $this->getEntityManager()->flush();

            $result = array(
                'access_token'  => $accessToken->getCode(),
                'expires_in'    => AccessToken::DEFAULT_EXPIRATION_TIME,
                'token_type'    => 'Bearer',
                'refresh_token' => $refreshToken->getCode(),
            );

            return new ViewModel(
                array(
                    'result' => (object) $result,
                )
            );
        }

        if ($this->getRequest()->getPost('grant_type') == 'refresh_token') {
            if ($this->getRequest()->getPost('refresh_token') === null) {
                return $this->error(400, 'No refresh token was provided');
            }

            $refreshToken = $this->getEntityManager()
                ->getRepository('ApiBundle\Entity\Token\Refresh')
                ->findOneByCode($this->getRequest()->getPost('refresh_token'));

            if ($refreshToken === null) {
                return $this->error(404, 'This refresh token does not exist');
            }

            if ($refreshToken->hasExpired()) {
                return $this->error(401, 'This refresh token has expired');
            }

            if ($refreshToken->hasBeenExchanged()) {
                $tokens = array_merge(
                    $this->getEntityManager()
                        ->getRepository('ApiBundle\Entity\Token\Access')
                        ->findAllActiveByAuthorizationCode($refreshToken->getAuthorizationCode()),
                    $this->getEntityManager()
                        ->getRepository('ApiBundle\Entity\Token\Refresh')
                        ->findAllActiveByAuthorizationCode($refreshToken->getAuthorizationCode())
                );

                foreach ($tokens as $token) {
                    $this->getEntityManager()->remove($token);
                }

                $this->getEntityManager()->flush();

                return $this->error(401, 'This refresh token has already been exchanged');
            }

            $key = $this->getKey('client_id');
            if ($key instanceof ViewModel) {
                return $key;
            }

            if (is_null($key)) {
                return $this->error(401, 'Unknown client_id');
            }

            $newAccessToken = new AccessToken(
                $refreshToken->getPerson(),
                $refreshToken->getAuthorizationCode()
            );
            $this->getEntityManager()->persist($newAccessToken);

            $newRefreshToken = new RefreshToken(
                $refreshToken->getPerson(),
                $refreshToken->getAuthorizationCode(),
                $key
            );

            $refreshToken->exchange();

            $this->getEntityManager()->persist($refreshToken);
            $this->getEntityManager()->persist($newRefreshToken);
            $this->getEntityManager()->flush();

            $result = array(
                'access_token'  => $newAccessToken->getCode(),
                'expires_in'    => AccessToken::DEFAULT_EXPIRATION_TIME,
                'token_type'    => 'Bearer',
                'refresh_token' => $newRefreshToken->getCode(),
            );

            return new ViewModel(
                array(
                    'result' => (object) $result,
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
    protected function getShibbolethUrl($redirect = '')
    {
        $shibbolethUrl = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shibboleth_url');

        if (@unserialize($shibbolethUrl) !== false) {
            $shibbolethUrl = unserialize($shibbolethUrl);

            if (getenv('SERVED_BY') === false) {
                throw new ShibbolethUrlException('The SERVED_BY environment variable does not exist');
            }
            if (!isset($shibbolethUrl[getenv('SERVED_BY')])) {
                throw new ShibbolethUrlException('Array key ' . getenv('SERVED_BY') . ' does not exist');
            }

            $shibbolethUrl = $shibbolethUrl[getenv('SERVED_BY')];
        }

        $shibbolethUrl .= '%3Fsource=api';

        if ($redirect != '') {
            $shibbolethUrl .= '%26redirect=' . urlencode($redirect);
        }

        return $shibbolethUrl;
    }
}
