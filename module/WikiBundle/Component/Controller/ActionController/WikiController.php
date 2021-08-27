<?php

namespace WikiBundle\Component\Controller\ActionController;

use CommonBundle\Component\Controller\ActionController\Exception\ShibbolethUrlException;

/**
 * We extend the CommonBundle controller.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class WikiController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    /**
     * We need to be able to specify all required authentication information,
     * which depends on the part of the site that is currently being used.
     *
     * @return array
     */
    public function getAuthenticationHandler()
    {
        return array(
            'action'     => 'login',
            'controller' => 'wiki_auth',

            'auth_route'     => 'wiki_auth',
            'redirect_route' => 'wiki_auth',
        );
    }

    /**
     * Create the full Shibboleth URL.
     *
     * @return string
     */
    protected function getShibbolethUrl()
    {
        $shibbolethUrl = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shibboleth_url');

        if (@unserialize($shibbolethUrl) !== false) {
            $shibbolethUrl = unserialize($shibbolethUrl);

            if (getenv('SERVED_BY') === false) {
                throw new ShibbolethUrlException('The SERVED_BY environment variable does not exist');
            }
            if (!array_key_exists(getenv('SERVED_BY'), $shibbolethUrl)) {
                throw new ShibbolethUrlException('Array key ' . getenv('SERVED_BY') . ' does not exist');
            }

            $shibbolethUrl = $shibbolethUrl[getenv('SERVED_BY')];
        }

        $shibbolethUrl .= '?source=wiki';

        if ($this->getParam('redirect') !== null) {
            $shibbolethUrl .= '%26redirect=' . urlencode(urlencode($this->getParam('redirect')));
        }

        return $shibbolethUrl;
    }
}
