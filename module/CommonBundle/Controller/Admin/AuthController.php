<?php

namespace CommonBundle\Controller\Admin;

use CommonBundle\Component\Authentication\Adapter\Doctrine\Shibboleth as ShibbolethAdapter;
use CommonBundle\Component\Authentication\Authentication;
use CommonBundle\Component\Controller\ActionController\Exception\ShibbolethUrlException;
use CommonBundle\Component\Controller\Exception\HasNoAccessException;
use Laminas\View\Model\ViewModel;

/**
 * AuthController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class AuthController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function authenticateAction()
    {
        $this->initAjax();

        $authResult = array(
            'result' => false,
            'reason' => 'NOT_POST',
        );

        if ($this->getRequest()->isPost()) {
            parse_str(
                $this->getRequest()->getPost()->get('formData'),
                $formData
            );

            $this->getAuthentication()->authenticate(
                $formData['username'],
                $formData['password'],
                $formData['remember_me']
            );

            if ($this->getAuthentication()->isAuthenticated()) {
                $authResult = array(
                    'result' => true,
                    'reason' => '',
                );
            } else {
                $authResult['reason'] = 'USERNAME_PASSWORD';
            }
        }

        return new ViewModel(
            array(
                'authResult' => $authResult,
            )
        );
    }

    public function loginAction()
    {
        $isAuthenticated = $this->getAuthentication()->isAuthenticated();

        if ($isAuthenticated) {
            $this->redirect()->toRoute('common_admin_index');

            return new ViewModel();
        }

        return new ViewModel(
            array(
                'isAuthenticated' => $isAuthenticated,
                'form'            => $this->getForm('common_auth_login'),
                'shibbolethUrl'   => $this->getShibbolethUrl(),
                'redirect'        => urldecode($this->getParam('redirect')),
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
                'common_admin_auth'
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
                                'common_admin_index'
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

    /**
     * @return string
     */
    private function getShibbolethUrl()
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

        $shibbolethUrl .= '?source=admin';

        if ($this->getParam('redirect') !== null) {
            $shibbolethUrl .= '%26redirect=' . urlencode($this->getParam('redirect'));
            $this->getSentryClient()->logMessage('shiburl: ' . $shibbolethUrl);
        }else {
            $this->getSentryClient()->logMessage("no redirect param");
        }

        $server = $this->getRequest()->getServer();
        if (isset($server['X-Forwarded-Host']) && isset($server['REQUEST_URI'])) {
            $shibbolethUrl .= '%26redirect=' . urlencode('https://' . $server['X-Forwarded-Host'] . $server['REQUEST_URI']);
        }

        return $shibbolethUrl;
    }
}
