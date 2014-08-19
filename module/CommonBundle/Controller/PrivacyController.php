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

namespace CommonBundle\Controller;

use Locale,
    Zend\View\Model\ViewModel;

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

        if (isset($privacyPolicies[$this->getLanguage()->getAbbrev()]))
            $privacyPolicy = $privacyPolicies[$this->getLanguage()->getAbbrev()];
        else
            $privacyPolicy = $privacyPolicies[Locale::getDefault()];

        return new ViewModel(
            array(
                'privacyPolicy' => $privacyPolicy,
            )
        );
    }
}
