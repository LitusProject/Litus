<?php

namespace PromBundle\Component\Controller;

use CommonBundle\Entity\General\Language;
use Laminas\Mvc\MvcEvent;

/**
 * We extend the CommonBundle controller.
 *
 * @author Mathijs Cuppens
 */
class RegistrationController extends \CommonBundle\Component\Controller\ActionController
{
    /**
     * Execute the request.
     *
     * @param  MvcEvent $e The MVC event
     * @return array
     */
    public function onDispatch(MvcEvent $e)
    {
        $result = parent::onDispatch($e);

        $e->setResult($result);

        return $result;
    }

    /**
     * Returns the language that is currently requested.
     *
     * @return \CommonBundle\Entity\General\Language
     */
    protected function getLanguage()
    {
        if ($this->language !== null) {
            return $this->language;
        }

        $language = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findOneByAbbrev('en');

        if ($language === null) {
            $language = new Language(
                'en',
                'English'
            );

            $this->getEntityManager()->persist($language);
            $this->getEntityManager()->flush();
        }

        $this->language = $language;

        return $language;
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

            'auth_route'     => 'prom_registration_index',
            'redirect_route' => 'prom_registration_index',
        );
    }
}
