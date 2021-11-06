<?php

namespace OnBundle\Controller;

use Laminas\View\Model\ViewModel;

/**
 * RedirectController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class RedirectController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function indexAction()
    {
        $slug = $this->getSlug();
        if ($slug === null || $slug->isActive() == false) {
            $this->getResponse()->setStatusCode(404);
            return $this->getResponse();
        }

        $slug->incrementHits();
        $this->getEntityManager()->flush();

        $this->redirect()->toUrl(
            $slug->getUrl()
        );

        return new ViewModel();
    }

    /**
     * @return \OnBundle\Entity\Slug|null
     */
    private function getSlug()
    {
        return $this->getEntityManager()
            ->getRepository('OnBundle\Entity\Slug')
            ->findOneByName(strtolower($this->getParam('name', '')));
    }
}
