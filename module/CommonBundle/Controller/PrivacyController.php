<?php

namespace CommonBundle\Controller;

use Laminas\View\Model\ViewModel;
use Locale;

/**
 * PrivacyController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class PrivacyController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function indexAction()
    {
        $privacyPolicies = unserialize(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('common.privacy_policy')
        );

        if (isset($privacyPolicies[$this->getLanguage()->getAbbrev()])) {
            $privacyPolicy = $privacyPolicies[$this->getLanguage()->getAbbrev()];
        } else {
            $privacyPolicy = $privacyPolicies[Locale::getDefault()];
        }

        return new ViewModel(
            array(
                'privacyPolicy' => $privacyPolicy,
            )
        );
    }
}
