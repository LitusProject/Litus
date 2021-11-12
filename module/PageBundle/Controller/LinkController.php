<?php

namespace PageBundle\Controller;

use Laminas\View\Model\ViewModel;
use PageBundle\Entity\Link;

/**
 * LinkController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class LinkController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function viewAction()
    {
        $link = $this->getLinkEntity();
        if ($link === null) {
            return $this->notFoundAction();
        }

        $this->redirect()->toUrl($link->getUrl($this->getLanguage()));

        return new ViewModel();
    }

    /**
     * @return Link|null
     */
    private function getLinkEntity()
    {
        $link = $this->getEntityById('PageBundle\Entity\Link');

        if (!($link instanceof Link)) {
            return;
        }

        return $link;
    }
}
