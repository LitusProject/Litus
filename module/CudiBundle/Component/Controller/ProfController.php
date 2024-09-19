<?php

namespace CudiBundle\Component\Controller;

use CommonBundle\Component\Controller\ActionController\Exception\ShibbolethUrlException;
use Laminas\Mvc\MvcEvent;

/**
 * We extend the CommonBundle controller.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ProfController extends \CommonBundle\Component\Controller\ActionController
{
    /**
     * Execute the request.
     *
     * @param  MvcEvent $e The MVC event
     * @return array
     * @throws \CommonBundle\Component\Controller\Exception\HasNoAccessException The user does not have permissions to access this resource
     */
    public function onDispatch(MvcEvent $e)
    {
        $result = parent::onDispatch($e);

        $result->shibbolethUrl = $this->getShibbolethUrl();
        $result->organizationUrl = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('organization_url');

        $e->setResult($result);

        return $result;
    }

    /**
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    protected function findCurrentAcademicYear()
    {
        return $this->getCurrentAcademicYear(true);
    }

    /**
     * We need to be able to specify all required authentication information,
     * which depends on the part of the site that is currently being used.
     *
     * @return array
     */
    public function getAuthenticationHandler()
    {
        return array(
            'action'         => 'index',
            'controller'     => 'common_index',

            'auth_route'     => 'cudi_prof_index',
            'redirect_route' => 'cudi_prof_index',
        );
    }

    /**
     * Create the full Shibboleth URL.
     *
     * @return string
     * @throws ShibbolethUrlException
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

        $shibbolethUrl .= '?source=prof';

        if ($this->getParam('redirect') !== null) {
            $shibbolethUrl .= '%26redirect=' . urlencode($this->getParam('redirect'));
        }

        $server = $this->getRequest()->getServer();
        if (isset($server['X-Forwarded-Host']) && isset($server['REQUEST_URI'])) {
            $shibbolethUrl .= '%26redirect=' . urlencode('https://' . $server['X-Forwarded-Host'] . $server['REQUEST_URI']);
        }

        return $shibbolethUrl;
    }
}
