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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

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

        $server = $this->getRequest()->getServer();
        if (isset($server['X-Forwarded-Host']) && isset($server['REQUEST_URI'])) {
            $shibbolethUrl .= '%26redirect=' . urlencode('https://' . $server['X-Forwarded-Host'] . $server['REQUEST_URI']);
        }

        return $shibbolethUrl;
    }
}
