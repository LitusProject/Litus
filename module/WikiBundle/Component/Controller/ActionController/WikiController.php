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

namespace WikiBundle\Component\Controller\ActionController;

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
            'action'         => 'login',
            'controller'     => 'wiki_auth',

            'auth_route'     => 'wiki_auth',
            'redirect_route' => 'wiki_auth'
        );
    }

    /**
     * Create the full Shibboleth URL.
     *
     * @return string
     */
    protected function _getShibbolethUrl()
    {
        $shibbolethUrl = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shibboleth_url');

        $shibbolethUrl .= '?source=wiki';

        if (null !== $this->getParam('redirect'))
            $shibbolethUrl .= '%26redirect=' . $this->getParam('redirect');

        return $shibbolethUrl;
    }
}
