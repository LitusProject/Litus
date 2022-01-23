<?php

namespace BrBundle\Controller\Corporate;

use Laminas\View\Model\ViewModel;

/**
 * JobfairController
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class JobfairController extends \BrBundle\Component\Controller\CorporateController
{
    public function overviewAction()
    {
        $person = $this->getCorporateEntity();
        if ($person === null) {
            return new ViewModel();
        }

        return new ViewModel(
            array(
                'jobfairInfo' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('br.corporate_jobfair_info'),
            )
        );
    }
}
