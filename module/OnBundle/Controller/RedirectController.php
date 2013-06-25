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

namespace OnBundle\Controller;

use CommonBundle\Entity\User\Person\Academic,
    Zend\View\Model\ViewModel;

/**
 * RedirectController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class RedirectController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function indexAction()
    {
        if (!($slug = $this->_getSlug())) {
            $this->getResponse()->setStatusCode(404);
            return new ViewModel();
        }

        $slug->incrementHits();
        $this->getDocumentManager()->flush();

        $this->redirect()->toUrl(
            $slug->getUrl()
        );

        return new ViewModel();
    }

    private function _getSlug()
    {
        if (null === $this->getParam('name'))
            return null;

        return $this->getDocumentManager()
            ->getRepository('OnBundle\Document\Slug')
            ->findOneByName($this->getParam('name'));
    }
}
