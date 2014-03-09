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

        try {
            if (false !== ($shibbolethUrl = unserialize($shibbolethUrl))) {
                if (false === getenv('SERVED_BY'))
                    throw new ShibbolethUrlException('The SERVED_BY environment variable does not exist');
                if (!array_key_exists(getenv('SERVED_BY'), $shibbolethUrl))
                    throw new ShibbolethUrlException('Array key ' . getenv('SERVED_BY') . ' does not exist');

                $shibbolethUrl = $shibbolethUrl[getenv('SERVED_BY')];
            }
        } catch (\ErrorException $e) {}

        $shibbolethUrl .= '?source=wiki';

        if (null !== $this->getParam('redirect'))
            $shibbolethUrl .= '%26redirect=' . urlencode(urlencode($this->getParam('redirect')));

        return $shibbolethUrl;
    }
}
